Scenario's
==========

In this folder are the requirements, or features, of phpDocumentor written down in the form of the Gherkin[1] DSL.
Using this notation is should become obvious for users and developers which features the system supports and what
usage scenario's are supported.

Another added benefit is that the practice of BDD can be applied this project and that there are automated tests
for verifying against regression and ensuring that everything functions as expected.

Structure
---------

> This section will be expanded once the experimentation period is past.

Every folder represents an 'epic' story that conveys feeling about what is offered as a user but not directly what you
can do with it. Inside those folders may be other 'epic' folders or features that represent an actual 'user story' or
requirement. Each feature in turn contains a series of scenarios that describe how you can interact with the given
feature.

[1]: http://docs.behat.org/guides/1.gherkin.html
