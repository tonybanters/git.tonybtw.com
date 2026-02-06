<?php

class Git_Model {
    private static function repo_root(): string {
        return $_SERVER['GIT_ROOT'] ?? getenv('GIT_ROOT') ?: '/srv/git';
    }

    public static function list_repos(): array {
        $repos = [];

        foreach (glob(self::repo_root() . '/*.git', GLOB_ONLYDIR) as $path) {
            $name = basename($path);
            $desc = @file_get_contents("$path/description") ?: '';
            if (str_contains($desc, 'Unnamed repository')) {
                $desc = '';
            }

            $last_commit = self::run($name, 'log -1 --format=%ct') ?: '0';

            $repos[] = [
                'name' => $name,
                'description' => trim($desc),
                'last_commit' => (int)$last_commit,
            ];
        }

        usort($repos, fn($a, $b) => $b['last_commit'] <=> $a['last_commit']);
        return $repos;
    }

    public static function get_repo_info(string $repo): ?array {
        $path = self::repo_path($repo);
        if (!is_dir($path)) {
            return null;
        }

        $desc = @file_get_contents("$path/description") ?: '';
        if (str_contains($desc, 'Unnamed repository')) {
            $desc = '';
        }

        $head = trim(self::run($repo, 'rev-parse --abbrev-ref HEAD') ?: 'master');
        $branches = self::get_branches($repo);
        $tags = self::get_tags($repo);

        return [
            'name' => $repo,
            'description' => trim($desc),
            'default_branch' => $head,
            'branches' => $branches,
            'tags' => $tags,
        ];
    }

    public static function get_commits(string $repo, string $ref = 'HEAD', int $limit = 50, int $offset = 0): array {
        $format = '%H%x00%s%x00%an%x00%ae%x00%at';
        $output = self::run($repo, "log --skip=$offset -n $limit --format='$format' " . escapeshellarg($ref));

        if (!$output) return [];

        $commits = [];
        foreach (explode("\n", trim($output)) as $line) {
            if (!$line) continue;
            [$hash, $subject, $author_name, $author_email, $timestamp] = explode("\x00", $line);
            $commits[] = [
                'hash' => $hash,
                'short_hash' => substr($hash, 0, 7),
                'subject' => $subject,
                'author_name' => $author_name,
                'author_email' => $author_email,
                'date' => (int)$timestamp,
            ];
        }

        return $commits;
    }

    public static function get_commit(string $repo, string $hash): ?array {
        $format = '%H%x00%s%x00%b%x00%an%x00%ae%x00%at%x00%P';
        $output = self::run($repo, "show -s --format='$format' " . escapeshellarg($hash));

        if (!$output) return null;

        [$hash, $subject, $body, $author_name, $author_email, $timestamp, $parents] = explode("\x00", trim($output));

        return [
            'hash' => $hash,
            'short_hash' => substr($hash, 0, 7),
            'subject' => $subject,
            'body' => trim($body),
            'author_name' => $author_name,
            'author_email' => $author_email,
            'date' => (int)$timestamp,
            'parents' => $parents ? explode(' ', $parents) : [],
        ];
    }

    public static function get_diff(string $repo, string $hash): string {
        return self::run($repo, "show --format='' --patch " . escapeshellarg($hash)) ?: '';
    }

    public static function get_tree(string $repo, string $ref = 'HEAD', string $path = ''): array {
        $treeish = $path ? "$ref:$path" : $ref;
        $output = self::run($repo, 'ls-tree ' . escapeshellarg($treeish));

        if (!$output) return [];

        $entries = [];
        foreach (explode("\n", trim($output)) as $line) {
            if (!$line) continue;
            if (preg_match('/^(\d+)\s+(\w+)\s+([a-f0-9]+)\t(.+)$/', $line, $m)) {
                $entries[] = [
                    'mode' => $m[1],
                    'type' => $m[2],
                    'hash' => $m[3],
                    'name' => $m[4],
                ];
            }
        }

        usort($entries, function($a, $b) {
            if (($a['type'] === 'tree') !== ($b['type'] === 'tree')) {
                return $a['type'] === 'tree' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        return $entries;
    }

    public static function get_blob(string $repo, string $ref, string $path): ?string {
        return self::run($repo, 'show ' . escapeshellarg("$ref:$path"));
    }

    public static function get_blob_size(string $repo, string $ref, string $path): int {
        $size = self::run($repo, 'cat-file -s ' . escapeshellarg("$ref:$path"));
        return $size ? (int)$size : 0;
    }

    public static function get_branches(string $repo): array {
        $output = self::run($repo, 'branch --format="%(refname:short)"');
        return $output ? array_filter(explode("\n", trim($output))) : [];
    }

    public static function get_tags(string $repo): array {
        $output = self::run($repo, 'tag --sort=-creatordate');
        return $output ? array_filter(explode("\n", trim($output))) : [];
    }

    public static function get_refs(string $repo): array {
        return [
            'branches' => self::get_branches($repo),
            'tags' => self::get_tags($repo),
        ];
    }

    public static function get_readme(string $repo, string $ref = 'HEAD'): ?array {
        $tree = self::get_tree($repo, $ref);
        $readme_names = ['README.md', 'README', 'README.txt', 'readme.md'];

        foreach ($tree as $entry) {
            if ($entry['type'] === 'blob' && in_array($entry['name'], $readme_names)) {
                $content = self::get_blob($repo, $ref, $entry['name']);
                return [
                    'name' => $entry['name'],
                    'content' => $content,
                ];
            }
        }

        return null;
    }

    public static function resolve_ref(string $repo, string $ref): ?string {
        $hash = self::run($repo, 'rev-parse ' . escapeshellarg($ref));
        return $hash ? trim($hash) : null;
    }

    public static function get_clone_urls(string $repo): array {
        $name = basename($repo, '.git') . '.git';
        return [
            'https' => "https://git.tonybtw.com/$name",
            'git' => "git://git.tonybtw.com/$name",
        ];
    }

    private static function run(string $repo, string $cmd): ?string {
        $repo_path = self::repo_path($repo);
        if (!is_dir($repo_path)) {
            return null;
        }

        $full_cmd = sprintf('git -C %s %s 2>/dev/null', escapeshellarg($repo_path), $cmd);
        $output = shell_exec($full_cmd);

        return $output !== null ? rtrim($output, "\n") : null;
    }

    private static function repo_path(string $repo): string {
        $repo = basename($repo);
        if (!str_ends_with($repo, '.git')) {
            $repo .= '.git';
        }
        return self::repo_root() . '/' . $repo;
    }
}
