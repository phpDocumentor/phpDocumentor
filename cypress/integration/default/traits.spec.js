import sidebar from './sidebar.inc';
import search from './search.inc';
import {getToc, getTocEntry} from './helpers/tableOfContents.lib';
import {getEntryIn, getSummaryEntry} from './helpers/onThisPage.lib';
import {getElementWithName} from './helpers/elements.lib';

describe('Traits', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-SharedTrait.html');
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);

    describe('Breadcrumb', function() {
        it('Has a breadcrumb featuring "Marios"', function() {
            cy.get('.phpdocumentor-breadcrumb').should('have.length', 1);
            cy.get('.phpdocumentor-breadcrumb').contains('Marios');
        });

        it('will send you to the namespace page when clicking on "Marios" in the breadcrumb', function() {
            cy.get('.phpdocumentor-breadcrumb')
                .contains('Marios')
                .click();
            cy.url().should('include', '/namespaces/marios.html');
        });
    });

    describe('Synopsis', function() {
        it('Has "SharedTrait" as title', function () {
            cy.get('.phpdocumentor-content__title').contains('SharedTrait');
        });

        it('Has a summary', function () {
            cy.get('.phpdocumentor-element.-trait > .phpdocumentor-summary')
                .contains('Trait that all pizza\'s could share.');
        });

        it('Has a description', function () {
            cy.get('.phpdocumentor-element.-trait > .phpdocumentor-description')
                .contains('Okay, so I couldn\'t think of something that fits the theme .. If you have a cool idea: please issue a PR :)');
        });
    });

    describe('Table of Contents', function() {
        it('Shows methods with their return type and visibility', function() {
            getTocEntry(getToc('methods', 'Methods'), 'sayHello()')
                .should('have.class', '-method')
                .and('have.class', '-public')
                .and('contain', 'Base');
        });
    });

    describe('On This Page', function() {
        it('renders links to the summary items', function() {
            getSummaryEntry('Methods').should('exist');
        });
        it('renders references to methods on this page', function() {
            getEntryIn('Methods', 'sayHello()').should('exist');
        });
    });

    describe('Showing constants', function() {
        describe('Synopsis', function() {
            it('Show the name', function() {
                getElementWithName('constant', 'MY_CONSTANT').should('be.visible');
            });

            it('Show the file name where the constant is located in the project', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-element-found-in__file')
                    .contains('a', 'SharedTrait.php')
                    .find('abbr')
                    .should('have.attr', 'title', 'src/SharedTrait.php');
            });

            it('Can be marked "public" (visibility) to influence styling', function () {
                getElementWithName('constant', 'MY_CONSTANT')
                    .should('have.class', '-public')
                    .and('not.have.class', '-protected')
                    .and('not.have.class', '-private');
            });

            it('Links to the file documentation wherein the constant is', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-element-found-in__file')
                    .contains('a', 'SharedTrait.php')
                    .should('have.attr', 'href', 'files/src-sharedtrait.html');
            });

            it('Show the line number where the constant is located', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-element-found-in__line')
                    .should((element) => {
                        expect(parseInt(element.text())).to.be.at.least(1);
                    });
            });
        });

        describe('Signature', function() {
            it('Can show the "public" visibility specifier', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-signature__visibility')
                    .contains('public');
            });

            it('Shows the name of the constant', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-signature__name')
                    .contains('MY_CONSTANT');
            });

            it('Type defaults to mixed when unable to infer', function() {
                getElementWithName('constant', 'MY_CONSTANT')
                    .find('.phpdocumentor-signature__type')
                    .contains('mixed');
            });
        });
    });

    describe('Traits using other traits', function() {
        beforeEach(function(){
            cy.visit('build/default/classes/Marios-PizzaToppingTrait.html');
        });

        describe('Methods', function() {
            it('Shows methods inherited from used trait', function() {
                // Check that the Table of Contents includes methods from the trait
                getTocEntry(getToc('methods', 'Methods'), 'getSauceType()')
                    .should('exist')
                    .and('have.class', '-method')
                    .and('have.class', '-public');
            });

            it.skip('Indicates which trait a method came from', function() {
                // Ensure the method shows where it was inherited from
                getTocEntry(getToc('methods', 'Methods'), 'getSauceType()')
                    .find('.phpdocumentor-table-of-contents__entry__trait')
                    .should('contain', 'PizzaSauceTrait');
            });

            it.skip('Links to the trait from which a method was inherited', function() {
                // Ensure the trait name links to that trait's documentation
                getTocEntry(getToc('methods', 'Methods'), 'getSauceType()')
                    .find('.phpdocumentor-table-of-contents__entry__trait a')
                    .should('have.attr', 'href', 'classes/Marios-PizzaSauceTrait.html');
            });
        });

        describe('Properties', function() {
            it('Shows properties inherited from used trait', function() {
                // Check that the Table of Contents includes properties from the trait
                getTocEntry(getToc('properties', 'Properties'), 'sauceType')
                    .should('exist')
                    .and('have.class', '-property')
                    .and('have.class', '-protected');
            });

            it.skip('Indicates which trait a property came from', function() {
                // Ensure the property shows where it was inherited from
                getTocEntry(getToc('properties', 'Properties'), 'sauceType')
                    .find('.phpdocumentor-table-of-contents__entry__trait')
                    .should('contain', 'PizzaSauceTrait');
            });

            it.skip('Links to the trait from which a property was inherited', function() {
                // Ensure the trait name links to that trait's documentation
                getTocEntry(getToc('properties', 'Properties'), 'sauceType')
                    .find('.phpdocumentor-table-of-contents__entry__trait a')
                    .should('have.attr', 'href', 'classes/Marios-PizzaSauceTrait.html');
            });
        });

        describe('Constants', function() {
            beforeEach(function(){
                // First, let's check if we can see a trait's constants in the documentation
                cy.visit('build/default/classes/Marios-SharedTrait.html');
                // Then let's create a copy of this test file to check at runtime
                cy.visit('build/default/classes/Marios-PizzaToppingTrait.html');
            });

            it('Shows constants inherited from used trait', function() {
                // Check that the Table of Contents includes constants from the trait
                getTocEntry(getToc('constants', 'Constants'), 'SAUCE_VERSION')
                    .should('exist')
                    .and('have.class', '-constant')
                    .and('have.class', '-public');
            });

            it.skip('Indicates which trait a constant came from', function() {
                // Ensure the constant shows where it was inherited from
                getTocEntry(getToc('constants', 'Constants'), 'SAUCE_VERSION')
                    .find('.phpdocumentor-table-of-contents__entry__trait')
                    .should('contain', 'PizzaSauceTrait');
            });

            it.skip('Links to the trait from which a constant was inherited', function() {
                // Ensure the trait name links to that trait's documentation
                getTocEntry(getToc('constants', 'Constants'), 'SAUCE_VERSION')
                    .find('.phpdocumentor-table-of-contents__entry__trait a')
                    .should('have.attr', 'href', 'classes/Marios-PizzaSauceTrait.html');
            });
        });

    });
});
