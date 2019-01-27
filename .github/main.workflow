workflow "Qa workflow" {
  on = "push"
  resolves = [
    "PHPStan",
    "composer-require-checker",
  ]
}

action "PHPStan" {
  uses = "docker://jaapio/github-actions:phpstan"
  args = "analyse src tests --level 2 --configuration phpstan.neon"
  secrets = ["GITHUB_TOKEN"]
  env = {
    PHP_EXTENSIONS = "php7-intl php7-xsl"
  }
}

action "composer" {
  uses = "docker://composer"
  secrets = ["GITHUB_TOKEN"]
}

action "composer-require-checker" {
  uses = "docker://phpga/composer-require-checker-ga"
  secrets = ["GITHUB_TOKEN"]
  args = "check --config-file ./composer-require-config.json composer.json"
  needs = ["composer"]
}
