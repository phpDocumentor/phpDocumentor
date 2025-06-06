import {getElementWithName} from "./helpers/elements.lib";

describe('Showing properties for a class', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizza.html');
    });

    describe('Meta-data', function() {
        it('Can be marked "public" (visibility) to influence styling', function () {
            getElementWithName('property', '$description')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "protected" (visibility) to influence styling', function() {
            getElementWithName('property', '$extra')
                .should('not.have.class', '-public')
                .and('have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "private" (visibility) to influence styling', function() {
            getElementWithName('property', '$secretIngredient')
                .should('not.have.class', '-public')
                .and('not.have.class', '-protected')
                .and('have.class', '-private');
        });

        it('Is not marked as static or abstract by default', function () {
            getElementWithName('property', '$name')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract');
        });

        it('Can be marked as static, but not as deprecated or abstract', function() {
            getElementWithName('property', '$description')
                .and('have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('not.have.class', '-deprecated');
        });

        it('Can be marked as deprecated, but not as static or abstract', function() {
            getElementWithName('property', '$extra')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('have.class', '-deprecated');
        });

        it('Can be marked "public private(set)" (visibility) to influence styling', function() {
            getElementWithName('property', '$asymmetric')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('property', '$name')
                .should('be.visible');
        });

        it('Show the file name where the property is located in the project', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizza.php');
        });

        it('Links to the file documentation wherein the property is', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .should('have.attr', 'href', 'files/src-pizza.html');
        });

        it('Show the line number where the property is located', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Can show the "protected" visibility specifier', function() {
            getElementWithName('property', '$extra')
                .find('.phpdocumentor-signature__visibility')
                .contains('protected');
        });

        it('Can show the "private" visibility specifier', function() {
            getElementWithName('property', '$secretIngredient')
                .find('.phpdocumentor-signature__visibility')
                .contains('private');
        });

        it('Can show the "public private(set)" visibility specifier', function() {
            getElementWithName('property', '$asymmetric')
                .find('.phpdocumentor-signature__visibility')
                .contains('public private(set)');
        });

        it('has the deprecated modifier', function() {
            getElementWithName('property', '$extra')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the "static" keyword', function() {
            getElementWithName('property', '$description')
                .find('.phpdocumentor-signature .phpdocumentor-signature__static')
                .should('exist')
                .and('contain', 'static');
        });

        it('Shows the name of the property', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__name')
                .contains('$name');
        });

        it('Shows the type', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__type')
                .contains('string');
        });

        // FIXME: This is a bug in phpDocumentor; the following test should be green
        it.skip('Var tag overrides the type-hint', function() {
            getElementWithName('property', '$alwaysTrue')
                .find('.phpdocumentor-signature__type')
                .contains('true');
        });

        it('Var tag description is shown', function() {
            getElementWithName('property', '$secretIngredient')
                .find('.phpdocumentor-description')
                .contains('Even the type of this is secret!');
        });
    });

    describe('Property Hooks', function() {
        beforeEach(function(){
            cy.visit('build/default/classes/Marios-Pizza.html');
        });

        describe('Virtual Properties', function() {
            it('Shows read-only virtual property correctly', function() {
                getElementWithName('property', '$temperature')
                    .should('have.class', '-property')
                    .and('have.class', '-read-only')
                    .and('have.class', '-virtual')
                    .and('not.have.class', '-write-only');
            });

            it('Shows "virtual" label for properties with hooks that access other properties', function() {
                getElementWithName('property', '$temperature')
                    .find('.phpdocumentor-element__modifiers')
                    .contains('small', 'virtual')
                    .should('exist');
            });

            it('Does not show "virtual" label for properties with hooks that access their own field', function() {
                getElementWithName('property', '$toppingCount')
                    .find('.phpdocumentor-element__modifiers')
                    .contains('small', 'virtual')
                    .should('not.exist');
            });

            it('Non-virtual property with hooks should not have virtual class', function() {
                getElementWithName('property', '$toppingCount')
                    .should('have.class', '-property')
                    .and('not.have.class', '-virtual');
            });

            it('Shows the property hook get method', function() {
                // Look for the h5 element with text "Hooks" inside the property element
                getElementWithName('property', '$temperature')
                    .contains('h5', 'Hooks')
                    .should('exist');

                // Then check for the get method code element
                getElementWithName('property', '$temperature')
                    .contains('code', 'get')
                    .should('exist');
            });

            it('Shows accessor with description for moisture property', function() {
                // First check for the Hooks section
                getElementWithName('property', '$moisture')
                    .contains('h5', 'Hooks')
                    .should('exist');

                // Then check for the description content
                getElementWithName('property', '$moisture')
                    .contains('value is calculated during the time in the oven')
                    .should('exist');
            });
        });

        describe('Property Hooks with References', function() {
            it('Shows property with reference parameter in setter', function() {
                // Look for the hooks section
                getElementWithName('property', '$pizzeria')
                    .contains('h5', 'Hooks')
                    .should('exist');

                // Then check that the property exists and has a set method
                getElementWithName('property', '$pizzeria')
                    .contains('code', 'set')
                    .should('exist');
            });
        });

        describe('Property Hooks with Arrays', function() {
            it('Shows property with array type', function() {
                getElementWithName('property', '$ingredients')
                    .find('.phpdocumentor-signature__type')
                    .should('contain', 'array');
            });

            it('Shows doc comment type for array items', function() {
                // Check for the property description
                getElementWithName('property', '$ingredients')
                    .contains('.phpdocumentor-description', 'arrays')
                    .should('exist');
            });
        });

        describe('Property Hooks with Default Values', function() {
            it('Shows property with default value logic', function() {
                getElementWithName('property', '$cookingTime')
                    .should('exist');
            });
        });

        describe('Asymmetric Property Accessors', function() {
            it('Shows property with asymmetric visibility correctly', function() {
                getElementWithName('property', '$moisture')
                    .find('.phpdocumentor-signature__visibility')
                    .contains('public private(set)');
            });

            it('Shows different parameter type for setter than getter return type', function() {
                // First check for the Hooks section
                getElementWithName('property', '$moisture')
                    .contains('h5', 'Hooks')
                    .should('exist');

                // Look for set method
                getElementWithName('property', '$moisture')
                    .contains('code', 'set')
                    .should('exist');

                // Check for the int|float type somewhere in the property
                getElementWithName('property', '$moisture')
                    .contains('int|float')
                    .should('exist');
            });
        });

        describe('Property Hook Signatures', function() {
            it('Shows return type for get hook', function() {
                getElementWithName('property', '$temperature')
                    .contains('h5', 'Hooks')
                    .should('exist');

                // Get hook should have return type in signature
                getElementWithName('property', '$temperature')
                    .contains('code', 'get')
                    .find('.phpdocumentor-signature__type')
                    .should('exist')
                    .contains('float');
            });

            it('Does not show return type for set hook', function() {
                getElementWithName('property', '$ingredients')
                    .contains('h5', 'Hooks')
                    .should('exist');

                getElementWithName('property', '$ingredients')
                    .contains('code', 'get')
                    .find('.phpdocumentor-signature__type')
                    .should('exist');

                // Set hook should NOT have return type in signature
                getElementWithName('property', '$ingredients')
                    .contains('code', 'set')
                    .children('.phpdocumentor-signature__type')
                    .should('not.exist');
            });
        });

        describe('Write-Only Properties', function() {
            it('Shows write-only virtual property correctly', function() {
                getElementWithName('property', '$instructions')
                    .should('have.class', '-property')
                    .and('have.class', '-write-only')
                    .and('have.class', '-virtual')
                    .and('not.have.class', '-read-only');
            });

            it('Shows "write-only" label for properties with only set hook', function() {
                getElementWithName('property', '$instructions')
                    .find('.phpdocumentor-element__modifiers')
                    .contains('small', 'write-only')
                    .should('exist');
            });

            it('Shows "virtual" label for write-only properties', function() {
                getElementWithName('property', '$instructions')
                    .find('.phpdocumentor-element__modifiers')
                    .contains('small', 'virtual')
                    .should('exist');
            });

            it('Shows the property hook set method only', function() {
                getElementWithName('property', '$instructions')
                    .contains('h5', 'Hooks')
                    .should('exist');

                getElementWithName('property', '$instructions')
                    .contains('code', 'set')
                    .should('exist');

                getElementWithName('property', '$instructions')
                    .contains('code', 'get')
                    .should('not.exist');
            });
        });
    });
});
