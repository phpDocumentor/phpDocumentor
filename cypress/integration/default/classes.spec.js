import sidebar from './sidebar.inc';
import search from './search.inc';
import {getToc, getTocEntry} from './helpers/tableOfContents.lib';
import {getEntryIn, getSummaryEntry} from './helpers/onThisPage.lib';

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

    describe('Synopsis', function() {
        it('Has "Pizzeria" as title', function () {
            cy.get('.phpdocumentor-content__title').contains('Pizzeria');
        });

        it('Shows a single implemented interface; which is not clickable because it is external', function () {
            cy.get('.phpdocumentor-element__implements').contains("JsonSerializable");
            cy.get('.phpdocumentor-element__implements abbr')
                .should("have.attr", 'title', '\\JsonSerializable');
        });

        it('Has a summary', function () {
            cy.get('.phpdocumentor-element.-class > .phpdocumentor-summary')
                .contains("Entrypoint for this pizza ordering application.");
        });

        it('Has a description', function () {
            cy.get('.phpdocumentor-element.-class > .phpdocumentor-description')
                .contains('This class provides an interface through which you can order pizza\'s and pasta\'s from Mario\'s Pizzeria.');
        });

        it('Shows a class is readonly', function () {
            cy.visit('build/default/classes/Marios-Pizza.html');
            cy.get('.phpdocumentor-element.-class .phpdocumentor-label')
                .contains('Read only');
        });

        it('Shows a class is final', function () {
            cy.visit('build/default/classes/Marios-Pizza.html');
            cy.get('.phpdocumentor-element.-class .phpdocumentor-label')
                .contains('Final');
        });

        it('Shows a class is abstract', function () {
            cy.visit('build/default/classes/Marios-Pizza-Topping.html');
            cy.get('.phpdocumentor-element.-class .phpdocumentor-label')
                .contains('Abstract');
        });

        describe('Shows tags', function () {
            it('Shows the tags section', function () {
                cy.get('.phpdocumentor-tag-list__heading')
                    .contains("Tags")
            })

            it('Shows link without description', function () {
                cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                    .contains('https://wwww.phpdoc.org')
                    .should('have.attr', 'href', 'https://wwww.phpdoc.org')
            });

            it('Shows link with description', function () {
                cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                    .contains('docs')
                    .parent()
                    .should('have.attr', 'href', 'https://docs.phpdoc.org')
            });
        });
    });

    describe('Table of Contents', function() {
        it('Shows methods with their return type and visibility', function() {
            getTocEntry(getToc('methods', 'Methods'), 'jsonSerialize()')
                .should('have.class', '-method')
                .and('have.class', '-public')
                .and('contain', ': array<string|int, mixed>'); // type including generic arguments
        });
    });

    describe('On This Page', function() {
        it('renders links to the summary items', function() {
            getSummaryEntry('Methods').should('exist');
        });
        it('renders references to methods on this page', function() {
            getEntryIn('Methods', 'jsonSerialize()').should('exist');
            getEntryIn('Methods', 'order()').should('exist');
            getEntryIn('Methods', 'doOrder()').should('exist');
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
