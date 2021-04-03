import {getElementWithName} from "./helpers/elements.lib";

describe('Methods', function() {
    describe('Showing a method in a class', function() {
        beforeEach(function(){
            cy.visit('build/default/classes/Marios-Pizzeria.html');
        });

        describe('Meta-data', function() {
            it('Can have a class "public" (visibility) to influence styling', function () {
                getElementWithName('method', 'jsonSerialize()')
                    .should('have.class', '-public')
                    .and('not.have.class', '-protected')
                    .and('not.have.class', '-private');
            });

            it('Can have a class "protected" (visibility) to influence styling', function() {
                getElementWithName('method', 'doOrder()')
                    .should('not.have.class', '-public')
                    .and('have.class', '-protected')
                    .and('not.have.class', '-private');
            });

            it('Can have a class "private" (visibility) to influence styling', function() {
                getElementWithName('method', 'doOldOrder()')
                    .should('not.have.class', '-public')
                    .and('not.have.class', '-protected')
                    .and('have.class', '-private');
            });

            it('Is not marked as static, final or abstract by default', function () {
                getElementWithName('method', 'jsonSerialize()')
                    .should('not.have.class', '-final')
                    .and('not.have.class', '-static')
                    .and('not.have.class', '-abstract');
            });

            it('Can be marked as static, but not as final, deprecated or abstract', function() {
                getElementWithName('method', 'doOrder()')
                    .should('not.have.class', '-final')
                    .and('have.class', '-static')
                    .and('not.have.class', '-abstract')
                    .and('not.have.class', '-deprecated');
            });
        });

        describe('Synopsis', function() {
            it('Show the name', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .should('be.visible');
            });

            it('Show the file name where the method is located in the project', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-element-found-in__file')
                    .contains('a', 'Pizzeria.php')
                    .find('abbr')
                    .should('have.attr', 'title', 'src/Pizzeria.php');
            });

            it('Links to the file documentation wherein the method is', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-element-found-in__file')
                    .contains('a', 'Pizzeria.php')
                    .should('have.attr', 'href', 'files/src-pizzeria.html');
            });

            it('Show the line number where the method is located', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-element-found-in__line')
                    .should((element) => {
                        expect(parseInt(element.text())).to.be.at.least(1);
                    });
            });

            // TODO: A feature request is to include https://github.com/JetBrains/phpstorm-stubs for inheritance resolving
            it('Does not show a summary because it @inheritdocs an external method', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-summary')
                    .should('not.exist');
            });

            // TODO: A feature request is to include https://github.com/JetBrains/phpstorm-stubs for inheritance resolving
            it.skip('Does not show a description because it @inheritdocs an external method', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-signature')
                    .next()
                    .should('not.have.class', 'phpdocumentor-description');
            });
        });

        describe('Signature', function() {
            it('Can show the "public" visibility specifier', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-signature__visibility')
                    .contains('public');
            });

            it('Can show the "protected" visibility specifier', function() {
                getElementWithName('method', 'doOrder()')
                    .find('.phpdocumentor-signature__visibility')
                    .contains('protected');
            });

            it('Can show the "private" visibility specifier', function() {
                getElementWithName('method', 'doOldOrder()')
                    .find('.phpdocumentor-signature__visibility')
                    .contains('private');
            });

            it('Shows the "static" keyword', function() {
                getElementWithName('method', 'doOrder()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__static')
                    .should('exist')
                    .and('contain', 'static');
            });

            it('Shows the name of the method', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-signature__name')
                    .contains('jsonSerialize');
            });

            it('Show a variadic indicator', function () {
                getElementWithName('method', 'order()')
                    .find('.phpdocumentor-signature')
                    .find('.phpdocumentor-signature__argument__variadic-operator')
                    .contains('...');
            });

            it('Shows the "array" return value', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-signature__response_type')
                    .contains('array');
            });
        });

        describe('Show a deprecated private final method', function () {
            it('Has a "private" visibility class to influence styling', function() {
                getElementWithName('method', 'doOldOrder()')
                    .should('not.have.class', '-public')
                    .and('not.have.class', '-protected')
                    .and('have.class', '-private');
            });

            it('Is marked as final and deprecated, but not as static or abstract', function() {
                getElementWithName('method', 'doOldOrder()')
                    .should('have.class', '-final')
                    .and('not.have.class', '-static')
                    .and('not.have.class', '-abstract')
                    .and('have.class', '-deprecated');
            });

            describe('signature', function () {
                it('has the deprecated modifier', function() {
                    getElementWithName('method', 'doOldOrder()')
                        .find('.phpdocumentor-signature')
                        .should('have.class', '-deprecated');
                });

                it('Shows the "private" visibility specifier', function() {
                    getElementWithName('method', 'doOldOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__visibility')
                        .contains('private');
                });

                it('Shows the "final" keyword', function() {
                    getElementWithName('method', 'doOldOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__final')
                        .contains('final');
                });

                it.skip('Shows "false" as return value; the return tag overrides the typehint', function() {
                    getElementWithName('method', 'doOldOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__argument__return-type')
                        .contains('false');
                });
            });
        });

        describe ('Parameters can have a default value', function() {
            beforeEach(function () {
                return cy.visit('build/default/classes/Marios-Oven.html');
            });

            describe('signature', function () {
                it('Show the name of argument $temp', function () {
                    getElementWithName('method', 'heatToTemp()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                        .contains('$temp');
                });

                it('Show the default value of argument $temp', function () {
                    getElementWithName('method', 'heatToTemp()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                        .contains('$temp')
                        .parent()
                        .find('.phpdocumentor-signature__argument__default-value')
                        .contains('self::DEFAULT_TEMPERATURE')
                });
            });
        })
    });
});
