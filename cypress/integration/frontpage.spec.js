describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('data/examples/MariosPizzeria/build/api/index.html');
    });

    it('Has "Documentation" as title', function() {
        cy.get('.phpdocumentor-title').contains("Documentation");
    });

    it('Has a search bar', function() {
        cy.get('.phpdocumentor-sidebar__category-header').contains("Search");
        cy.get('.phpdocumentor-field.phpdocumentor-search__field');
    });

    it('The "Marios" namespace in the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains("Marios");
    });

    it('Can open the "Marios" namespace page from the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
        cy.get('.phpdocumentor-content > h2').contains("Marios");
    });

    it('Can open the "Marios" namespace from the main content', function() {
        cy.get('.phpdocumentor-content').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
        cy.get('.phpdocumentor-content > h2').contains("Marios");
    });
});
