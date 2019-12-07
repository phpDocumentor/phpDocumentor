Feature:
  To be able to create links in the description to other elements
  As a developer
  I want to be able to reference to other elements in the same class.

  Background:
    Given A single file named "test.php" based on "tags/internalSee.php"
    When I run "phpdoc -f test.php"

  Scenario: class level description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has description:
    """
    Inline see to same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to same class relative [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to property in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to property in same class relative [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to method in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to method in same class relative [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to constant in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)

    Inline see to constant in same class relative [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    """

  Scenario: constant level description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has constant CONSTANT with description:
    """
    Inline see to same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to same class relative [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to property in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to property in same class relative [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to method in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to method in same class relative [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to constant in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)

    Inline see to constant in same class relative [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    """

  Scenario: method level description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has method method with description:
    """
    Inline see to same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to same class relative [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to property in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to property in same class relative [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to method in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to method in same class relative [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to constant in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)

    Inline see to constant in same class relative [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    """

  Scenario: property level description
    Then class "\phpDocumentor\Descriptor\TestSeeTagIssue" has property property with description:
    """
    Inline see to same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to same class relative [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)
    Inline see to same class absolute [class itself](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html)

    Inline see to property in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::$property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to property in same class relative [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)
    Inline see to property in same class absolute [own property](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#property_property)

    Inline see to method in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::method()](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to method in same class relative [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)
    Inline see to method in same class absolute [own method](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#method_method)

    Inline see to constant in same class relative [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [\phpDocumentor\Descriptor\TestSeeTagIssue::CONSTANT](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)

    Inline see to constant in same class relative [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    Inline see to constant in same class absolute [own constant](../classes/phpDocumentor-Descriptor-TestSeeTagIssue.html#constant_CONSTANT)
    """
