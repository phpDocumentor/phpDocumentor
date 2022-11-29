import sidebar from './sidebar.inc';
import search from './search.inc';
import {getToc, getTocEntry} from './helpers/tableOfContents.lib';
import {getEntryIn, getSummaryEntry} from './helpers/onThisPage.lib';

describe('Interfaces', function() {
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
});
