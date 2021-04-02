import {getSidebarHeaderByTitle, getSidebarItemByTitle} from "./helpers/sidebar.lib";
import {shouldVisitPageWithTitle} from "./helpers/pages.lib";

export default function() {
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
            getSidebarHeaderByTitle('Reports')
                .should('be.visible');
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
};
