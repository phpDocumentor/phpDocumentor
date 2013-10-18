Feature: Show links to external documentation for specific external classes
  In order to link to classes not in scope of this project
  As a reader
  I want to generate a link when the location for that class on the web is known.

  Background:
    Given I am in the phpDocumentor root directory

  Scenario: Generate a link
    Given the "transformer" section of the configuration has
      """
      <external-class-documentation>
          <prefix>HTML_QuickForm2</prefix>
          <uri>http://pear.php.net/package/HTML_QuickForm2/docs/latest/HTML_QuickForm2/{CLASS}.html</uri>
      </external-class-documentation>
      """
    When I run phpDocumentor with:
      """
      <?php
      class myContainer extends \HTML_QuickForm2_Container
      {
      }
      """
    Then I expect the file "classes/myContainer.html"
     And the parent class should link to "http://pear.php.net/package/HTML_QuickForm2/docs/latest/HTML_QuickForm2/HTML_QuickForm2_Container"
