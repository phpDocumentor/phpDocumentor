import sidebar from './sidebar.inc';
import search from './search.inc';
import {getEntryIn, getSummaryEntry} from './helpers/onThisPage.lib';

describe('Enums', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Delivery.html');
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);

    describe('Breadcrumb', function() {
        it('Has a breadcrumb featuring "Marios"', function() {
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
        it('Has "Delivery" as title', function () {
            cy.get('.phpdocumentor-content__title').contains('Delivery');
        });

        it('Has a summary', function () {
            cy.get('.phpdocumentor-element.-class > .phpdocumentor-summary')
                .contains('This is an enum example');
        });

        it('Has a description', function () {
            cy.get('.phpdocumentor-element.-class > .phpdocumentor-description')
                .contains('Enums are introduced in php 8.1.');
        });
    });

    describe('Cases', function() {
        it('Lists every declared case', function() {
            cy.get('#enumcase_PICKUP').should('exist');
            cy.get('#enumcase_DELIVER').should('exist');
        });

        it('Renders the docblock summary attached to a case', function() {
            cy.get('#enumcase_PICKUP')
                .closest('.phpdocumentor-element')
                .find('.phpdocumentor-summary')
                .contains('Pickup case');
        });

        it('Renders the docblock description attached to a case', function() {
            cy.get('#enumcase_PICKUP')
                .closest('.phpdocumentor-element')
                .find('.phpdocumentor-description')
                .contains('Cases can have docblocks.');
        });
    });

        it('Renders the backing value next to the case in the table of contents', function() {
            cy.get('.phpdocumentor-table-of-contents__entry')
                .contains('PICKUP')
                .parent()
                .should('contain', "'pickup'");
        });
    });

    describe('Methods', function() {
        it('Lists methods declared on the enum', function() {
            cy.get('#method_isDeliver').should('exist');
        });
    });

    describe('On This Page', function() {
        it('renders links to the summary items', function() {
            getSummaryEntry('Cases').should('exist');
            getSummaryEntry('Methods').should('exist');
        });

        it('renders references to cases on this page', function() {
            getEntryIn('Cases', 'PICKUP').should('exist');
            getEntryIn('Cases', 'DELIVER').should('exist');
        });

        it('renders references to methods on this page', function() {
            getEntryIn('Methods', 'isDeliver()').should('exist');
        });
    });
});
