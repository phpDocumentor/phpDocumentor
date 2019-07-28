workflow "Qa workflow" {
  on = "push"
  resolves = [
    "PHPStan",
    "composer-require-checker",
    "Code style check",
  ]
}

action "composer" {
  uses = "docker://composer"
  secrets = ["GITHUB_TOKEN"]
  args = "install --no-interaction --prefer-dist --optimize-autoloader"
}

action "PHPStan" {
  uses = "docker://jaapio/github-actions:phpstan"
  args = "analyse src tests --level 3 --configuration phpstan.neon"
  secrets = ["GITHUB_TOKEN"]
  env = {
    PHP_EXTENSIONS = "php7-intl php7-xsl"
  }
  needs = ["composer"]
}

action "composer-require-checker" {
  uses = "docker://phpga/composer-require-checker-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "check --config-file ./composer-require-config.json composer.json"
  needs = ["composer"]
}

action "Code style check" {
  uses = "docker://oskarstark/phpcs-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "-d memory_limit=1024M"
  needs = ["composer"]
}

workflow "Issue management" {
  resolves = ["takanabe/add-new-issues-to-project-column@master"]
  on = "issues"
}

action "takanabe/add-new-issues-to-project-column@master" {
  uses = "takanabe/add-new-issues-to-project-column@master"
  secrets = ["GITHUB_TOKEN"]
  env = {
    PROJECT_NAME = "Issue Triage"
    PROJECT_COLUMN_NAME = "Needs triage"
  }
}

workflow "Release workflow" {
  on = "release"
  resolves = [
    "sign phar"
  ]
}

action "warm cache" {
  uses = "docker://phar-ga"
  args = "php bin/console cache:warmup --env=prod"
  needs = ["composer"]
}

action "build phar" {
  uses = "docker://phar-ga"
  args = "box compile"
  needs = ["warm cache"]
}

action "sign phar" {
  uses = "docker://phar-ga"
  args = "gpg --command-fd 0 --pinentry-mode loopback -u info@phpdoc.org --batch --detach-sign --output build/phpDocumentor.phar.asc build/phpDocumentor.phar"
  secrets = ["SECRET_KEY", "PASSPHRASE"]
  needs = ["build phar"]
}

action "release phar" {
  uses = "fnkr/github-action-ghr@v1"
  secrets = ["GITHUB_TOKEN"]
  env = {
    GHR_PATH = "build/"
  }
  needs = ["sign phar"]
}
