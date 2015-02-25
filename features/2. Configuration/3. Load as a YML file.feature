Feature:
  As a User
  I want to provide a configuration file using YML as format
  So that I get to provide it in my favorite format

  @roadmap-v3
  Scenario: phpDocumentor loads the option file in an YML format
    Given a file named "phpdoc.yml" with:
    """
    title: This is an example title
    """
    When I run phpDocumentor against the directory "."
    Then the project has the title "This is an example title"
