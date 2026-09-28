describe('Directive options list directive', function () {
    beforeEach(function () {
        cy.visit('build/guides-directives/guide/index.html');
    });

    it('Renders an "Options" section for the documented directive', function () {
        cy.contains('h3', 'Options').should('exist');
    });

    it('Lists each option defined on the directive', function () {
        cy.get('.phpdocumentor-element__name').contains(':template:').should('exist');
        cy.get('.phpdocumentor-element__name').contains(':force:').should('exist');
    });

    it('Does not render an option that was never defined on the directive', function () {
        cy.get('.phpdocumentor-element__name').should(($names) => {
            expect($names.text()).not.to.contain(':does-not-exist:');
        });
    });
});
