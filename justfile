default:
    @just --list

dev port="8888":
    GIT_ROOT={{justfile_directory()}}/test-repos php -S localhost:{{port}} -t public

init-test-repos:
    mkdir -p test-repos
    @if [ ! -d "test-repos/git-btw.git" ]; then \
        git clone --bare . test-repos/git-btw.git; \
        echo "Created test-repos/git-btw.git"; \
    fi

clean-test-repos:
    rm -rf test-repos
