{
  description = "git-btw - A minimal git web interface";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };
  outputs = {
    self,
    nixpkgs,
  }: let
    systems = ["x86_64-linux" "aarch64-linux"];

    forAllSystems = fn: nixpkgs.lib.genAttrs systems (system: fn nixpkgs.legacyPackages.${system});
  in {
    devShells = forAllSystems (pkgs: {
      default = pkgs.mkShell {
        packages = [
          pkgs.php
          pkgs.just
        ];
        shellHook = ''
          export PS1="(git-btw) $PS1"
          echo ""
          echo "  git-btw dev server"
          echo "  ------------------"
          echo "  just dev         - start php server on localhost:8888"
          echo "  just dev 3000    - start on custom port"
          echo ""
          echo "  GIT_ROOT defaults to ./test-repos"
          echo "  Create test repos: just init-test-repos"
          echo ""
        '';
      };
    });

    formatter = forAllSystems (pkgs: pkgs.alejandra);
  };
}
