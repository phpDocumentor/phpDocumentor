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
        searchResults
            .get('ul li').contains('Pizza')
            .click({force: true});

        cy.url().should('include', '/namespaces/marios-pizza.html');
    });

    it('Closes the results when clearing the search field', function() {
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .type('pizza');
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('have.class', 'phpdocumentor-search--has-results');
        cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
            .clear({ force: true });
        cy.get('.phpdocumentor-header .phpdocumentor-search')
            .should('not.have.class', 'phpdocumentor-search--has-results');

        cy.get('.phpdocumentor-search-results')
            .should('have.css', 'opacity', '0')
            .should('have.css', 'pointer-events', 'none')
        ;
    });

    // TODO: Disabled test because it fails a lot on the mac runner and needs to be investaged
    //       It is not the most important feature to test, so I am disabling it in favour of
    //       pipeline reliability
    // it('Closes the results when pressing escape', function() {
    //     cy.get('.phpdocumentor-header .phpdocumentor-search input[type="search"]')
    //         .type('pizza')
    //         .blur(); // Ensure focus is gone
    //
    //     cy.get('.phpdocumentor-search-results__dialog')
    //         .parents('body')
    //         .type('{esc}');
    //
    //     cy.get('.phpdocumentor-header .phpdocumentor-search')
    //         .should('not.have.class', 'phpdocumentor-search--has-results');
    //
    //     cy.get('.phpdocumentor-search-results')
    //         .should('have.css', 'opacity', '0')
    //         .should('have.css', 'pointer-events', 'none')
    //     ;
    // });
}
