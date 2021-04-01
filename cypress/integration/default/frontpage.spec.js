import {shouldVisitPageWithTitle} from "./helpers/pages.lib";
import {getSearchField} from "./helpers/search.lib";
import {getSidebarHeaderByTitle, getSidebarItemByTitle} from './helpers/sidebar.lib';

describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('build/default/index.html');
    });

    it('Has "Documentation" in the menu as title', function() {
        cy.get('.phpdocumentor-title').contains("Documentation");
    });

    describe('Search', function() {
        it('Shows the search bar', function() {
            getSearchField()
                .should('be.visible');
        });
    });

    describe('In the sidebar', function() {
        describe('Namespaces section', function() {
            it('Shows the Namespaces section', function () {
                getSidebarHeaderByTitle('Namespaces')
                    .should('be.visible');
            })

            it('Shows the "Marios" namespace', function () {
                getSidebarItemByTitle('Namespaces', 'Marios', 'namespace')
                    .should('be.visible');
            });

            it('Opens the "Marios" namespace', function() {
                getSidebarItemByTitle('Namespaces', 'Marios', 'namespace')
                    .click();

                shouldVisitPageWithTitle('/namespaces/marios.html', 'Marios');
            });

            it('Shows the "Pizza" (sub)namespace', function() {
                cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza");
            });

            it('Opens the "Pizza" namespace', function() {
                cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza")
                    .click();

                shouldVisitPageWithTitle('/namespaces/marios-pizza.html', 'Pizza');
            });
        });

        describe('Packages section', function() {
            it('Shows the Packages section', function () {
                getSidebarHeaderByTitle('Packages')
                    .should('be.visible');
            })

            it('Shows the "Default" package', function () {
                getSidebarItemByTitle('Packages', 'Default')
                    .should('be.visible');
            });

            it('Opens the "Default" package', function() {
                getSidebarItemByTitle('Packages', 'Default')
                    .click();
                shouldVisitPageWithTitle('/packages/Default.html', 'Default');
            });
        });

        describe('Reports section', function() {
            it('Shows the Reports section', function () {
                getSidebarHeaderByTitle('Reports').should('be.visible');
            });

            it('Has a report with Deprecated elements', function () {
                getSidebarItemByTitle('Reports', 'Deprecated')
                    .should('be.visible');
            });

            it('Has a report with Errors', function () {
                getSidebarItemByTitle('Reports', 'Errors')
                    .should('be.visible');
            });

            it('Has a report with Markers', function () {
                getSidebarItemByTitle('Reports', 'Markers')
                    .should('be.visible')
            });

            it('Opens the Deprecated elements report', function() {
                getSidebarItemByTitle('Reports', 'Deprecated')
                    .click();

                shouldVisitPageWithTitle('/reports/deprecated.html', 'Deprecated');
            });

            it('Opens the Errors report', function() {
                getSidebarItemByTitle('Reports', 'Errors')
                    .click();

                shouldVisitPageWithTitle('/reports/errors.html', 'Errors');
            });

            it('Opens the Markers report', function() {
                getSidebarItemByTitle('Reports', 'Markers')
                    .click();

                shouldVisitPageWithTitle('/reports/markers.html', 'Markers');
            });
        });

        describe('Indices section', function() {
            it('Shows the Indices section', function () {
                getSidebarHeaderByTitle('Indices')
                    .should('be.visible');
            });

            it('Has a index with all files in it', function () {
                getSidebarItemByTitle('Indices', 'Files')
                    .should('be.visible');
            });

            it('Opens the Files index', function() {
                getSidebarItemByTitle('Indices', 'Files')
                    .click();

                shouldVisitPageWithTitle('/indices/files.html', 'Files');
            });
        });
    });

    describe('Content', function() {
        describe('Packages', function() {
            it('Shows a section "Packages" with a table of contents', function() {
                cy.get('h3#packages').should('contain', 'Packages');
                cy.get('h3#packages').next('.phpdocumentor-table-of-contents');
            });

            it('Shows the "Default" package', function() {
                cy.get('h3#packages')
                    .next('.phpdocumentor-table-of-contents')
                    .find('.-package').should('contain', 'Default')
            });

            it('Opens the "Default" package', function() {
                cy
                    .get('h3#packages').next('.phpdocumentor-table-of-contents')
                    .find('.phpdocumentor-table-of-contents__entry.-package')
                    .contains("Default")
                    .click();

                shouldVisitPageWithTitle('/packages/Default.html', 'Default');
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
});
