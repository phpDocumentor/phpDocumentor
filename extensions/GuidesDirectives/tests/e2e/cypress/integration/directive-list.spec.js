describe('Directive list directive', function () {
    beforeEach(function () {
        cy.visit('build/guides-directives/guide/index.html');
    });

    // Scenario: Every non-abstract directive class is listed in alphabetical order with an anchor
    it('Lists the directives in alphabetical order with an anchor', function () {
        cy.get('#directive-aliased-directive').should('exist');
        cy.get('#directive-example-directive').should('exist');
        cy.get('[data-directive-name]').then(($entries) => {
            const names = [...$entries].map((entry) => entry.getAttribute('data-directive-name'));
            expect(names).to.deep.equal([...names].sort());
        });
    });

    // Scenario: Aliases are shown
    it('Shows the aliases of a directive', function () {
        cy.get('#directive-aliased-directive').should('contain', 'aliased').and('contain', 'alias-two');
    });

    // Scenario: The aliases line is omitted without aliases
    it('Shows no aliases for a directive without aliases', function () {
        cy.get('#directive-example-directive').should('not.contain', 'Aliases');
    });

    // Scenario: The class docblock is shown as the description
    it('Shows the class docblock as the description', function () {
        cy.get('#directive-example-directive').should('contain', 'A directive used purely as a fixture');
    });

    // Scenario: Docblock tags are not shown
    it('Does not show docblock tags', function () {
        cy.get('#directive-aliased-directive').should('not.contain', 'this tag must not show up');
    });

    // Scenario: Options are listed with their details
    it('Lists the options of a directive with their details', function () {
        cy.get('#directive-example-directive').within(() => {
            cy.contains(':template:').should('exist');
            cy.contains(':force:').should('exist');
        });
    });

    // Scenario: The options section is omitted without options
    it('Shows no options section for a directive without options', function () {
        cy.get('#directive-aliased-directive').should('not.contain', 'Options');
    });

    // Scenario: Abstract directive classes are not listed
    it('Does not list abstract directive classes', function () {
        cy.get('#directive-abstract-directive').should('not.exist');
    });
});
