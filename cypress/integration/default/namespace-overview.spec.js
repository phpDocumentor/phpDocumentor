import {shouldVisitPageWithTitle} from "./helpers/pages.lib";

describe('Namespace Overview', function() {
    beforeEach(function(){
        cy.visit('build/default/namespaces/marios.html');
    });

    it('Has "Marios" as title', function() {
        cy.get('.phpdocumentor-content__title')
            .contains("Marios");
    });

    it('Child namespace feature a breadcrumb to their parent', function() {
        cy.visit('build/default/namespaces/marios-pizza.html');
        cy.get('.phpdocumentor-breadcrumb')
            .should('have.length', 1)
            .contains('a', 'Marios')
            .and('have.attr','href', 'namespaces/marios.html');
    });

    describe('contains a list of child namespaces', function(){
        it('Has a section "Namespaces" featuring the "Pizza" sub-namespace', function() {
            cy.get('h3#namespaces').contains("Namespaces")
                .next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry')
                .should('contain', 'Pizza');
        });

        it('Goes to the "Pizza" sub-namespace when you click on it', function() {
            cy.get('h3#namespaces')
                .contains("Namespaces")
                .next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry')
                .contains("Pizza")
                .click();

            shouldVisitPageWithTitle('/namespaces/marios-pizza.html', 'Pizza');
        });
    })

    describe('contains a list of interfaces, classes and traits', function() {
        it('Has a section "Interfaces, Classes and Traits" featuring the "Pizzeria" class', function() {
            cy.get('h3#interfaces_class_traits')
                .contains("Interfaces, Classes and Traits")
                .next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry')
                .should('contain', 'Pizzeria');
        });

        it('Has a section "Interfaces, Classes and Traits" featuring the "Product" interface', function() {
            cy.get('h3#interfaces_class_traits')
                .contains("Interfaces, Classes and Traits")
                .next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry')
                .should('contain', 'Product');
        });

        it('Goes to "Pizzeria" its detail page when you click on it', function() {
            cy.get('h3#interfaces_class_traits')
                .contains("Interfaces, Classes and Traits")
                .next('.phpdocumentor-table-of-contents')
                .find('.phpdocumentor-table-of-contents__entry')
                .contains("Pizzeria").click();

            shouldVisitPageWithTitle('/classes/Marios-Pizzeria.html', 'Pizzeria');
        });
    });

    describe('contains a section with the details of the functions in this namespace', function() {
        it('Has a section "Functions" featuring the "heatOven" function', function () {
            cy.get('h3#functions').contains("Functions")
                .siblings('.phpdocumentor-element.-function')
                .contains("heatOven");
        });
    });

    describe('contains a section with the details of the constants in this namespace', function() {
        it('Has a section "Constants" featuring the "OVEN_TEMPERATURE" constant', function () {
            cy.get('h3#constants').contains("Constants")
                .siblings('.phpdocumentor-element.-constant')
                .contains("OVEN_TEMPERATURE");
        });
    });
});
