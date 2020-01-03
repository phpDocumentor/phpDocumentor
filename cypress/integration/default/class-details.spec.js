describe('Class Detail Page', function() {
    beforeEach(function(){
        cy.visit('data/examples/MariosPizzeria/build/default/classes/Marios-Pizzeria.html');
    });

    it('Has "Pizzeria" as title', function() {
        cy.get('.phpdocumentor-content > h2').contains("Pizzeria");
    });

    it('Has a breadcrumb featuring "Home" and "Marios"', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Home");
        cy.get('.phpdocumentor-breadcrumbs').contains("Marios");
        cy.get('.phpdocumentor-breadcrumbs > li').should('have.length', 2);
    });

    it('will send you to the index when clicking on "Home" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Home").click();
        cy.url().should('include', '/index.html');
    });

    it('will send you to the namespace page when clicking on "Marios" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
    });

    it('Has a summary', function() {
        cy.get('.phpdocumentor-content > .phpdocumentor-class__summary')
            .contains("Entrypoint for this pizza ordering application.");
    });

    it('Has a description', function() {
        cy.get('.phpdocumentor-content > .phpdocumentor-class__description')
            .contains("This class provides an interface through which you can order pizza's and pasta's from Mario's Pizzeria.");
    });

    it('Shows a single implemented interface; which is not clickable because it is external', function() {
        cy.get('.phpdocumentor-class__implements').contains("JsonSerializable");
        cy.get('.phpdocumentor-class__implements abbr')
            .should("have.attr", 'title', '\\JsonSerializable');
    });

    it('Show methods with return type in the Table of Contents', function() {
        cy.get('.phpdocumentor-table_of_contents th')
            .contains("jsonSerialize()").parent()
            .next() // empty description
            .next().contains('array'); // type
    });

    describe('Showing a method in a class', function() {
        let methods;
        before(function(){
            methods = cy.get('.phpdocumentor-method');
        });

        it('Has a method called jsonSerialize', function() {
            methods.get('.phpdocumentor-method__name').contains("jsonSerialize()");
        });
    });
});
