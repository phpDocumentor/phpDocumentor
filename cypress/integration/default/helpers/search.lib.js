export function getSearchField() {
    cy.get('.phpdocumentor-header').contains("Search");

    return cy.get('.phpdocumentor-field.phpdocumentor-search__field');
}
