workflow "Qa workflow" {
  on = "push"
  resolves = [
    "PHPStan",
  ]
}

action "PHPStan" {
  uses = "docker://oskarstark/phpstan-ga"
  args = "analyse src tests --level 2 --configuration phpstan.neon"
  secrets = ["GITHUB_TOKEN"]
}
