export default function() {
    it('Has an active search form', function() {
        cy.get('.phpdocumentor-header').get('.phpdocumentor-search')
            .get('input[type="search"]').should('not.be.disabled');
    });

    it('Search results pane should be invisible and unclickable when not in use', function() {
        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '0')
            .should('have.css', 'pointer-events', 'none');
    });

    it('Opens the results and shows results for "Pizza"', function() {
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('not.have.class', 'phpdocumentor-search--has-results')
            .get('input[type="search"]')
            .type('pizza');
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('have.class', 'phpdocumentor-search--has-results');

        let searchResults = cy.get('.phpdocumentor-search-results');
        searchResults.get('h2').contains("Search results");
        searchResults
            .should('have.css', 'opacity', '1')
            .should('not.have.css', 'pointer-events', 'none')
            .get('ul li').should('have.length.gte', 1)
            .contains('Pizza')
        ;
    });

    it('Goes to the detail page for the search result "Pizza"', function() {
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .type('pizza');

        let searchResults = cy.get('.phpdocumentor-search-results');
        searchResults.get('ul li').contains('Pizza').click();
        cy.url().should('include', '/namespaces/marios-pizza.html');
    });

    it('Closes the results when clearing the search field', function() {
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .type('pizza');
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('have.class', 'phpdocumentor-search--has-results');
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .clear();
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('not.have.class', 'phpdocumentor-search--has-results');

        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '0')
            .should('have.css', 'pointer-events', 'none')
        ;
    });

    it('Closes the results when pressing escape', function() {
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .type('pizza')
            .blur(); // Ensure focus is gone

        cy.get('body').type('{esc}');

        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('not.have.class', 'phpdocumentor-search--has-results');

        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '0')
            .should('have.css', 'pointer-events', 'none')
        ;
    });
}
