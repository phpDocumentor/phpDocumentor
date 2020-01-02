describe('Search', function() {
    beforeEach(function(){
        cy.visit('data/examples/MariosPizzeria/build/api/index.html');
    });

    it('Has an active search form in the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').get('.phpdocumentor-search')
            .get('input[type="search"]').should('not.be.disabled');
    });

    it('Search results pane should be invisible and unclickable when not in use', function() {
        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '0')
            .should('have.css', 'pointer-events', 'none')
        ;
    });

    it('Opens the results and shows results for "Pizza"', function() {
        cy.get('.phpdocumentor-sidebar').get('.phpdocumentor-search')
            .should('not.have.class', 'phpdocumentor-search--has-results')
            .get('input[type="search"]').type('pizza')
            .parents('.phpdocumentor-search')
            .should('have.class', 'phpdocumentor-search--has-results')
        ;

        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '1')
            .should('not.have.css', 'pointer-events', 'none')


    });
});
