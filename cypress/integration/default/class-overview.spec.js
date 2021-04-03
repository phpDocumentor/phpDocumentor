import {getElementWithName} from "./helpers/elements.lib";
import sidebar from "./sidebar.inc";
import search from "./search.inc";

describe('Class Overview', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizzeria.html');
    });
    describe('Search', search);
    describe('In the sidebar', sidebar);

    it('Has "Pizzeria" as title', function() {
        cy.get('.phpdocumentor-content__title').contains("Pizzeria");
    });

    it('Has a breadcrumb featuring "Marios"', function() {
        cy.get('.phpdocumentor-breadcrumb').should('have.length', 2);
        cy.get('.phpdocumentor-breadcrumb').contains('Marios');
    });

    it('will send you to the namespace page when clicking on "Marios" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumb')
            .contains("Marios")
            .click();
        cy.url().should('include', '/namespaces/marios.html');
    });

    describe('In the sidebar', sidebar);

    it('Has a summary', function() {
        cy.get('.phpdocumentor-element.-class > .phpdocumentor-summary')
            .contains("Entrypoint for this pizza ordering application.");
    });

    it('Has a description', function() {
        cy.get('.phpdocumentor-element.-class > .phpdocumentor-description')
            .contains('This class provides an interface through which you can order pizza\'s and pasta\'s from Mario\'s Pizzeria.');
    });

    it('Has a tags', function () {
        cy.get('.phpdocumentor-tag-list__heading')
            .contains("Tags")
    })

    describe ('Shows class tags', function () {
        it('Shows link without description', function() {
            cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                .contains('https://wwww.phpdoc.org')
                .should('have.attr', 'href', 'https://wwww.phpdoc.org')
        });

        it('Shows link with description', function() {
            cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                .contains('docs')
                .parent()
                .should('have.attr', 'href', 'https://docs.phpdoc.org')
        });
    });

    it('Shows a single implemented interface; which is not clickable because it is external', function() {
        cy.get('.phpdocumentor-element__implements').contains("JsonSerializable");
        cy.get('.phpdocumentor-element__implements abbr')
            .should("have.attr", 'title', '\\JsonSerializable');
    });

    it('Show methods with return type in the Table of Contents', function() {
        cy.get('.phpdocumentor-table-of-contents__entry')
            .contains("jsonSerialize()").parent()
            .contains(': array'); // type
    });

    describe('Showing a method in a class', function() {
        it('Shows the variadic indicator with argument "$pizzas" in the "order" method', function() {
            getElementWithName('method', 'order()')
                .find('.phpdocumentor-signature')
                .find('.phpdocumentor-signature__argument__variadic-operator')
                .contains('...');
        });

        describe('Shows a public, implemented, method from an interface', function () {
            it('Shows the name "jsonSerialize()"', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .should('be.visible');
            });

            it('Shows the file name where "jsonSerialize()" is located', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .find('.phpdocumentor-element-found-in__file')
                    .should('have.attr', 'title', 'src/Pizzeria.php')
                    .contains('Pizzeria.php');
            });

            it('Shows the line number where "jsonSerialize()" is located', function() {
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

            it('Has a "public" visibility class to influence styling', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .should('have.class', '-public')
                    .and('not.have.class', '-protected')
                    .and('not.have.class', '-private');
            });

            it('Is not marked as static, final or abstract', function() {
                getElementWithName('method', 'jsonSerialize()')
                    .should('not.have.class', '.-final')
                    .and('not.have.class', '.-static')
                    .and('not.have.class', '.-abstract');
            });

            describe('Signature', function () {
                it('Shows the "public" visibility specifier', function() {
                    getElementWithName('method', 'jsonSerialize()')
                        .find('.phpdocumentor-signature__visibility')
                        .contains('public');
                });

                it.skip('Shows the name of the method', function() {
                    getElementWithName('method', 'jsonSerialize()')
                        .find('.phpdocumentor-signature__argument__name')
                        .contains('jsonSerialize');
                });

                it('Shows the "array" return value', function() {
                    getElementWithName('method', 'jsonSerialize()')
                        .find('.phpdocumentor-signature__response_type')
                        .contains('array');
                });
            });
        });

        describe('Shows a protected static method with summary and description', function () {
            it('Shows the name "doOrder()"', function() {
                getElementWithName('method', 'doOrder()')
                    .should('be.visible');
            });

            it('Only has "protected" visibility', function() {
                getElementWithName('method', 'doOrder()')
                    .should('not.have.class', '-public')
                    .and('have.class', '-protected')
                    .and('not.have.class', '-private');
            });

            it('Is marked as static, but not as final, deprecated or abstract', function() {
                getElementWithName('method', 'doOrder()')
                    .should('not.have.class', '-final')
                    .and('have.class', '-static')
                    .and('not.have.class', '-abstract')
                    .and('not.have.class', '-deprecated');
            });

            describe('signature', function () {
                it('Shows the "protected" visibility specifier', function() {
                    getElementWithName('method', 'doOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__visibility')
                        .should('contain','protected');
                });

                it('Shows the "static" keyword', function() {
                    getElementWithName('method', 'doOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__static')
                        .should('exist')
                        .and('contain','static');
                });

                it.skip('Shows "array" as return value', function() {
                    getElementWithName('method', 'doOrder()')
                        .find('.phpdocumentor-signature .phpdocumentor-signature__argument__return-type')
                        .should('contain','array');
                });
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
