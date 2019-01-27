workflow "Qa workflow" {
  on = "push"
  resolves = [
    "PHPStan",
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
