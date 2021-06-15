import {shouldVisitPageWithTitle} from "./helpers/pages.lib";
import sidebar from './sidebar.inc';
import search from './search.inc';

describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('build/default/index.html');
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);

    describe('Packages', function() {
        it('Shows a section "Packages" with a table of contents', function() {
            cy.get('h3#packages').should('contain', 'Packages');
            cy.get('h3#packages').next('.phpdocumentor-table-of-contents');
        });

        it('Shows the "Marios" package', function() {
            cy.get('h3#packages')
                .next('.phpdocumentor-table-of-contents')
                .find('.-package').should('contain', 'Marios')
        });

        it('Opens the "Marios" package', function() {
            cy
                .get('h3#packages').next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry.-package')
                .contains("Marios")
                .click();

            shouldVisitPageWithTitle('/packages/Marios.html', 'Marios');
        });
    });

    describe('Namespaces', function() {
        it('Shows a section "Namespaces" with a table of contents', function() {
            cy.get('h3#namespaces').should('contain', 'Namespaces');
            cy.get('h3#namespaces').next('.phpdocumentor-table-of-contents');
        });

        it('Shows the "Marios" namespace', function() {
            cy.get('h3#namespaces')
                .next('.phpdocumentor-table-of-contents')
                .find('.-namespace').should('contain', 'Marios')
        });

        it('Opens the "Marios" namespace', function() {
            cy
                .get('h3#namespaces').next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry.-namespace')
                .contains("Marios")
                .click();

            shouldVisitPageWithTitle('/namespaces/marios.html', 'Marios');
        });
    });

    describe('Global constants and functions', function() {
        it('Shows a section "Table Of Contents"', function() {
            cy.get('h3#toc').should('contain', 'Table of Contents');
            cy.get('h3#toc').next('.phpdocumentor-table-of-contents');
        });

        it('Shows the "HIGHER_OVEN_TEMPERATURE" constant', function() {
            cy.get('h3#toc')
                .next('.phpdocumentor-table-of-contents')
                .find('.-constant').should('contain', 'HIGHER_OVEN_TEMPERATURE')
        });

        // TODO: Is this the outcome that we want?
        it('Opens the "HIGHER_OVEN_TEMPERATURE" constant', function() {
            cy
                .get('h3#toc').next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry.-constant')
                .contains('HIGHER_OVEN_TEMPERATURE')
                .click();

            shouldVisitPageWithTitle('/namespaces/default.html', 'API Documentation');
        });
    });
});
