import sidebar from './sidebar.inc';
import search from './search.inc';
import {getToc, getTocEntry} from './helpers/tableOfContents.lib';
import {getEntryIn, getSummaryEntry} from './helpers/onThisPage.lib';

describe('Interfaces', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Product.html');
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
        it('Has "Product" as title', function () {
            cy.get('.phpdocumentor-content__title').contains('Product');
        });

        it('Has a summary', function () {
            cy.get('.phpdocumentor-element.-interface > .phpdocumentor-summary')
                .contains('Contract between products.');
        });

        it('Has a description', function () {
            cy.get('.phpdocumentor-element.-interface > .phpdocumentor-description')
                .contains('This is a description on an interface.');
        });
    });

    describe('Table of Contents', function() {
        it('Shows methods with their return type and visibility', function() {
            getTocEntry(getToc('methods', 'Methods'), 'getName()')
                .should('have.class', '-method')
                .and('have.class', '-public')
                .and('contain', ': string');
        });
        it('Shows constants with their visibility', function() {
            getTocEntry(getToc('constants', 'Constants'), 'PUBLIC_CONSTANT')
                .should('have.class', '-constant')
                .and('have.class', '-public');
            getTocEntry(getToc('constants', 'Constants'), 'PROTECTED_CONSTANT')
                .should('have.class', '-constant')
                .and('have.class', '-protected');
            getTocEntry(getToc('constants', 'Constants'), 'PRIVATE_CONSTANT')
                .should('have.class', '-constant')
                .and('have.class', '-private');
        });
    });

    describe('On This Page', function() {
        it('renders links to the summary items', function() {
            getSummaryEntry('Constants').should('exist');
            getSummaryEntry('Methods').should('exist');
        });
        it('renders references to constants on this page', function() {
            getEntryIn('Constants', 'PUBLIC_CONSTANT').should('exist');
            getEntryIn('Constants', 'PROTECTED_CONSTANT').should('exist');
            getEntryIn('Constants', 'PRIVATE_CONSTANT').should('exist');
        });
        it('renders references to methods on this page', function() {
            getEntryIn('Methods', 'getName()').should('exist');
        });
    });
});
