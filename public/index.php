<?php

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/app/models/Git_Model.php';
require APP_ROOT . '/app/lib/Parsedown.php';

$parsedown = new Parsedown();
$parsedown->setSafeMode(true);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rawurldecode($uri);
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET /' => 'home',
    'GET /(?<repo>[^/]+\.git)' => 'repo_summary',
    'GET /(?<repo>[^/]+\.git)/tree/(?<ref>[^/]+)(?<path>/.*)?$' => 'repo_tree',
    'GET /(?<repo>[^/]+\.git)/blob/(?<ref>[^/]+)(?<path>/.+)$' => 'repo_blob',
    'GET /(?<repo>[^/]+\.git)/raw/(?<ref>[^/]+)(?<path>/.+)$' => 'repo_raw',
    'GET /(?<repo>[^/]+\.git)/log/(?<ref>[^/]+)?$' => 'repo_log',
    'GET /(?<repo>[^/]+\.git)/commit/(?<hash>[a-f0-9]+)$' => 'repo_commit',
    'GET /(?<repo>[^/]+\.git)/refs$' => 'repo_refs',
];

$handler = null;
$params = [];

foreach ($routes as $pattern => $h) {
    [$route_method, $route_pattern] = explode(' ', $pattern, 2);
    if ($method !== $route_method) continue;

    $regex = '#^' . $route_pattern . '$#';
    if (preg_match($regex, $uri, $matches)) {
        $handler = $h;
        $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        break;
    }
}

if (!$handler) {
    http_response_code(404);
    $error = '404 Not Found';
    require APP_ROOT . '/app/views/error.php';
    exit;
}

$params = array_map(fn($v) => trim($v, '/'), $params);

switch ($handler) {
    case 'home':
        $repos = Git_Model::list_repos();
        require APP_ROOT . '/app/views/home.php';
        break;

    case 'repo_summary':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $ref = $repo['default_branch'];
        $tree = Git_Model::get_tree($params['repo'], $ref);
        $commits = Git_Model::get_commits($params['repo'], $ref, 10);
        $readme = Git_Model::get_readme($params['repo'], $ref);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/summary.php';
        break;

    case 'repo_tree':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $ref = $params['ref'] ?: $repo['default_branch'];
        $path = $params['path'] ?? '';
        $tree = Git_Model::get_tree($params['repo'], $ref, $path);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/tree.php';
        break;

    case 'repo_blob':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $ref = $params['ref'];
        $path = $params['path'];
        $content = Git_Model::get_blob($params['repo'], $ref, $path);
        if ($content === null) {
            http_response_code(404);
            $error = 'File not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $size = Git_Model::get_blob_size($params['repo'], $ref, $path);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/blob.php';
        break;

    case 'repo_raw':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            echo 'Repository not found';
            exit;
        }
        $ref = $params['ref'];
        $path = $params['path'];
        $content = Git_Model::get_blob($params['repo'], $ref, $path);
        if ($content === null) {
            http_response_code(404);
            echo 'File not found';
            exit;
        }
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: inline; filename="' . basename($path) . '"');
        echo $content;
        break;

    case 'repo_log':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $ref = $params['ref'] ?: $repo['default_branch'];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 50;
        $commits = Git_Model::get_commits($params['repo'], $ref, $per_page, ($page - 1) * $per_page);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/log.php';
        break;

    case 'repo_commit':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $commit = Git_Model::get_commit($params['repo'], $params['hash']);
        if (!$commit) {
            http_response_code(404);
            $error = 'Commit not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $diff = Git_Model::get_diff($params['repo'], $params['hash']);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/commit.php';
        break;

    case 'repo_refs':
        $repo = Git_Model::get_repo_info($params['repo']);
        if (!$repo) {
            http_response_code(404);
            $error = 'Repository not found';
            require APP_ROOT . '/app/views/error.php';
            exit;
        }
        $refs = Git_Model::get_refs($params['repo']);
        $clone_urls = Git_Model::get_clone_urls($params['repo']);
        require APP_ROOT . '/app/views/repo/refs.php';
        break;
}
