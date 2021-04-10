import sidebar from "./sidebar.inc";
import search from "./search.inc";

describe('Classes', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizzeria.html');
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);

    describe('Breadcrumb', function() {
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
    });

    it('Has "Pizzeria" as title', function() {
        cy.get('.phpdocumentor-content__title').contains("Pizzeria");
    });

    it('Shows a single implemented interface; which is not clickable because it is external', function() {
        cy.get('.phpdocumentor-element__implements').contains("JsonSerializable");
        cy.get('.phpdocumentor-element__implements abbr')
            .should("have.attr", 'title', '\\JsonSerializable');
    });

    it('Has a summary', function() {
        cy.get('.phpdocumentor-element.-class > .phpdocumentor-summary')
            .contains("Entrypoint for this pizza ordering application.");
    });

    it('Has a description', function() {
        cy.get('.phpdocumentor-element.-class > .phpdocumentor-description')
            .contains('This class provides an interface through which you can order pizza\'s and pasta\'s from Mario\'s Pizzeria.');
    });

    describe ('Shows tags', function () {
        it('Shows the tags section', function () {
            cy.get('.phpdocumentor-tag-list__heading')
                .contains("Tags")
        })

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

    describe('Table of Contents', function(){
        it('Show methods', function() {
            cy.get('.phpdocumentor-table-of-contents__entry')
                .contains("jsonSerialize()")
                .parent()
                .contains(': array'); // type
        });
    });

    describe('Applying a trait', function() {
        // FIXME: This is a bug in the application; the method is not imported
        it.skip('Use method with an alias and changed visibility', function() {
            cy.get('.phpdocumentor-table-of-contents__entry')
                .contains('myPrivateHello()');
        });
    });
});
