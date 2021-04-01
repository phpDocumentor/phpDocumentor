export function shouldVisitPageWithTitle(url, title) {
    cy.url().should('include', url);
    cy.get('.phpdocumentor-content__title').contains(title);
}
