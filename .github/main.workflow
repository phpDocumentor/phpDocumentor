workflow "Example workflow" {
  on = "push"
  resolves = ["Send message to slack"]
}

action "Send message to slack" {
  uses = "apex/actions/slack@master"
  secrets = ["SLACK_WEBHOOK"]
}
