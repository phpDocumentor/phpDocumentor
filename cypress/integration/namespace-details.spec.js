describe('Namespace Detail Page', function() {
    beforeEach(function(){
        cy.visit('data/examples/MariosPizzeria/build/api/namespaces/marios.html');
    });

    it('Has "Marios" as title', function() {
        cy.get('.phpdocumentor-content > h2').contains("Marios");
    });

    it('Has a breadcrumb featuring "Home"', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Home");
        cy.get('.phpdocumentor-breadcrumbs > li').should('have.length', 1);
    });

    it('will send you to the index when clicking on "Home" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Home").click();
        cy.url().should('include', '/index.html');
    });

    it('Has a section Namespaces featuring the "Pizza" sub-namespace', function() {
        cy.get('.phpdocumentor-content > h3').contains("Namespaces");
        cy.get('.phpdocumentor-content > h3 + ul li a').contains("Pizza");
    });

    it('Has a section classes and interfaces featuring the "Pizzeria" class', function() {
        cy.get('.phpdocumentor-content > h3').contains("Interfaces and Classes");
        cy.get('.phpdocumentor-content > h3 + dl dt a').contains("Pizzeria");
    });

    it('Has a section classes and interfaces featuring the "Product" interface', function() {
        cy.get('.phpdocumentor-content > h3').contains("Interfaces and Classes");
        cy.get('.phpdocumentor-content > h3 + dl dt a').contains("Product");
    });

    it('Goes to "Pizzeria" its detail page when you click on it', function() {
        cy.get('.phpdocumentor-content > h3 + dl dt a').contains("Pizzeria").click();
        cy.url().should('include', '/classes/Marios-Pizzeria.html');
        cy.get('.phpdocumentor-content > h2').contains("Pizzeria");
    });
});
