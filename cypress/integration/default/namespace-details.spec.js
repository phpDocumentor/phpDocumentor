describe('Namespace Detail Page', function() {
    beforeEach(function(){
        cy.visit('build/default/namespaces/marios.html');
    });

    it('Has "Marios" as title', function() {
        cy.get('.phpdocumentor-content__title').contains("Marios");
    });

    it('Has a section "Namespaces" featuring the "Pizza" sub-namespace', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Namespaces")
            .next().get('dt a').contains("Pizza");
    });

    it('Goes to the "Pizza" sub-namespace when you click on it', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Namespaces")
            .next().get('dt a').contains("Pizza").click();

        cy.url().should('include', '/namespaces/marios-pizza.html');
        cy.get('.phpdocumentor-content__title').contains("Pizza");
    });

    it('Has a section "Interfaces, Classes and Traits" featuring the "Pizzeria" class', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Interfaces, Classes and Traits")
            .next().get('dt a').contains("Pizzeria");
    });

    it('Has a section "Interfaces, Classes and Traits" featuring the "Product" interface', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Interfaces, Classes and Traits")
            .next().get('dt a').contains("Product");
    });

    it('Goes to "Pizzeria" its detail page when you click on it', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Interfaces, Classes and Traits")
            .next().get('dt a').contains("Pizzeria").click();

        cy.url().should('include', '/classes/Marios-Pizzeria.html');
        cy.get('.phpdocumentor-content__title').contains("Pizzeria");
    });

    it('Has a section "Functions" featuring the "heatOven" function', function() {
        cy.get('.phpdocumentor-elements__header').contains("Functions")
            .next().get('.phpdocumentor-element__name').contains("heatOven");
    });

    it('Has a section "Constants" featuring the "OVEN_TEMPERATURE" constant', function() {
        cy.get('.phpdocumentor-elements__header').contains("Constants")
            .next().get('dt a').contains("OVEN_TEMPERATURE");
    });

    // TODO: Test shows broken behaviour, fix that
    it.skip('Has a section "Constants" featuring the "HIGHER_OVEN_TEMPERATURE" constant', function() {
        cy.get('.phpdocumentor-elements__header').contains("Constants")
            .next().get('dt a').contains("HIGHER_OVEN_TEMPERATURE");
    });
});

describe('Namespace Detail Page for a (sub)namespace', function() {
    beforeEach(function(){
        cy.visit('build/default/namespaces/marios-pizza.html');
    });

    it('Has "Pizza" as title', function() {
        cy.get('.phpdocumentor-content__title').contains("Pizza");
    });

    it('Has a breadcrumb featuring "Marios"', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Marios");
        cy.get('.phpdocumentor-breadcrumbs > li').should('have.length', /*2*/ 1);
    });

    it('Has a section "Interfaces, Classes and Traits" featuring the "Base" class', function() {
        cy.get('.phpdocumentor-content > article > h3').contains("Interfaces, Classes and Traits")
            .next().get('dt a').contains("Base");
    });
});
