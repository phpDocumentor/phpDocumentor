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
        describe('Namespaces', function() {

            it('Shows a section "Namespaces" with a table of contents', function() {
                cy.get('h3#namespaces').should('contain', 'Namespaces');
                cy.get('h3#namespaces').next('.phpdocumentor-table-of-contents');
            });

            it('Shows the "Marios" namespace', function() {
                const toc = cy.get('h3#namespaces').next('.phpdocumentor-table-of-contents');
                toc.find('.-namespace').should('contain', 'Marios')
            });

            it('Opens the "Marios" namespace', function() {
                const toc = cy.get('h3#namespaces').next('.phpdocumentor-table-of-contents');
                toc
                    .find('.-namespace')
                    .contains("Marios")
                    .click();
                cy.url().should('include', '/namespaces/marios.html');
                cy.get('.phpdocumentor-content__title').should('contain', 'Marios');
            });
        });
    });
});
