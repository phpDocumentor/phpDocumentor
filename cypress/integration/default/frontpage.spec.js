describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('build/default/index.html');
    });

    it('Has "Documentation" as title', function() {
        cy.get('.phpdocumentor-title').contains("Documentation");
    });

    describe('Search', function() {
        it('Shows the search bar', function() {
            cy.get('.phpdocumentor-header').contains("Search");
            cy.get('.phpdocumentor-field.phpdocumentor-search__field');
        });
    });

    describe('In the sidebar', function() {
        it('Shows the "Marios" namespace', function () {
            cy.get('.phpdocumentor-sidebar').contains("Marios");
        });

        it('Opens the "Marios" namespace', function() {
            cy.get('.phpdocumentor-sidebar').contains("Marios").click();
            cy.url().should('include', '/namespaces/marios.html');
            cy.get('.phpdocumentor-content > article > h2').contains("Marios");
        });

        it('Shows the "Pizza" (sub)namespace', function() {
            cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza");
        });

        it('Opens the "Pizza" namespace', function() {
            cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza").click();
            cy.url().should('include', '/namespaces/marios-pizza.html');
            cy.get('.phpdocumentor-content > article > h2').contains("Pizza");
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
                cy.url().should('include', '/packages/Default.html');
                cy.get('.phpdocumentor-content__title').should('contain', 'Default');
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
                cy.url().should('include', '/namespaces/marios.html');
                cy.get('.phpdocumentor-content__title').should('contain', 'Marios');
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
                cy.url().should('include', '/namespaces/default.html');
                cy.get('.phpdocumentor-content__title').should('contain', 'API Documentation');
            });
        });
    });
});
