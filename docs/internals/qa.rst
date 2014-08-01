Quality Assurance
=================

To continuously improve on the stability of phpDocumentor we make use of Wercker_ and Scrutinizer_ to verify and inspect
the source code.

Using Wercker_ we are able to run all automated tests, such as PHPUnit and Behat, and verify if the application still
behaves as expected. The code coverage that is collected during the running of PHPUnit is sent to Scrutinizer, which
will create statistics out of it.

Scrutinizer_ is a service that runs static analysis tools and extracts valuable information that can be used to improve
the application. The page for phpDocumentor is located at https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor2.

The following aspects of the application are inspected:

1. Code Coverage
2. Overall quality
3. Coding Standards violations
4. Mess detection
5. Security advisories
6. Copy/paste detection
7. Metrics

and much more.

The best location to start looking for parts to improve is on the Issues_ page; there you will find all things that
Scrutinizer detected to be wrong with the project.


.. _Wercker:     http://wercker.com
.. _Scrutinizer: https://scrutinizer-ci.com
.. _Issues:      https://scrutinizer-ci.com/g/phpDocumentor/phpDocumentor2/issues/develop