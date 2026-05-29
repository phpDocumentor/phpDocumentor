describe('Markdown guides', function() {
    beforeEach(function() {
        cy.visit('build/markdown/guide/index.html');
    });

    it('renders the Markdown guide front page', function() {
        cy.url().should('include', '/build/markdown/guide/index.html');
        cy.get('h1').contains('Markdown docs');
    });

    it('shows markdown docs entries in the sidebar', function() {
        cy.get('.phpdocumentor-sidebar__category-header').contains('Markdown-Docs');
        cy.get('.phpdocumentor-sidebar').contains('Other page');
        cy.get('.phpdocumentor-sidebar').contains('Sub directory');
        cy.get('.phpdocumentor-sidebar').contains('nested page');
    });

    it('navigates to the other page from the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains('Other page').click();

        cy.url().should('include', '/build/markdown/guide/other.html');
        cy.get('h1').contains('Other page');
    });

    it('navigates to nested pages from the sidebar', function() {
        cy.get('.phpdocumentor-sidebar').contains('Sub directory').click();

        cy.url().should('include', '/build/markdown/guide/subdir/index.html');
        cy.get('h1').contains('Sub directory');

        cy.get('.phpdocumentor-sidebar').contains('nested page').click();

        cy.url().should('include', '/build/markdown/guide/subdir/page.html');
        cy.get('h1').contains('nested page');
    });

    it('uses the expected base path on nested pages', function() {
        cy.visit('build/markdown/guide/subdir/index.html');

        cy.get('base').should('have.attr', 'href', '../../');
    });

    it('loads template assets on guide pages', function() {
        cy.get('link[rel="stylesheet"][href="css/template.css"]').should('exist');
        cy.get('script[src="js/template.js"]').should('exist');
        cy.get('script[src="js/search.js"]').should('exist');
    });
});
