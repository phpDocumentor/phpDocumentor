describe('Frontpage', function() {
    before(function(){
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
});
