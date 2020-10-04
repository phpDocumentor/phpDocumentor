describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('build/default/index.html');
    });

    it('Has "Documentation" as title', function() {
        cy.get('.phpdocumentor-title').contains("Documentation");
    });

    it('Has a search bar', function() {
        cy.get('.phpdocumentor-sidebar__category-header').contains("Search");
        cy.get('.phpdocumentor-field.phpdocumentor-search__field');
    });

    it('Has the "Marios" namespace in the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains("Marios");
    });

    it('Can open the "Marios" namespace page from the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
        cy.get('.phpdocumentor-content > article > h2').contains("Marios");
    });

    it('Has the "Pizza" (sub)namespace in the sidebar', function() {
        cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza");
    });

    it('Can open the "Pizza" namespace page from the sidebar', function() {
        cy.get('.phpdocumentor-sidebar .phpdocumentor-list').contains("Pizza").click();
        cy.url().should('include', '/namespaces/marios-pizza.html');
        cy.get('.phpdocumentor-content > article > h2').contains("Pizza");
    });

    it('Has the "Marios" namespace in the main content', function() {
        cy.get('.phpdocumentor-content').contains("Marios");
    });

    it('Can open the "Marios" namespace from the main content', function() {
        cy.get('.phpdocumentor-content').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
        cy.get('.phpdocumentor-content > article > h2').contains("Marios");
    });
});
