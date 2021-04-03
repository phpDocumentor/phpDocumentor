import {shouldVisitPageWithTitle} from "./helpers/pages.lib";
import sidebar from "./sidebar.inc";
import search from "./search.inc";

describe('Namespaces', function() {
    beforeEach(function(){
        cy.visit('build/default/namespaces/marios.html');
    });

    it('Has "Marios" as title', function() {
        cy.get('.phpdocumentor-content__title')
            .contains("Marios");
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);
    describe('Breadcrumb', function() {
        it('Child namespace feature a breadcrumb to their parent', function () {
            cy.visit('build/default/namespaces/marios-pizza.html');
            cy.get('.phpdocumentor-breadcrumb')
                .should('have.length', 1)
                .contains('a', 'Marios')
                .and('have.attr', 'href', 'namespaces/marios.html');
        });
    });

    describe('Table of Contents', function() {
        describe('Child namespaces', function () {
            it('Has a section "Namespaces" featuring the "Pizza" sub-namespace', function () {
                cy.get('h3#namespaces').contains("Namespaces")
                    .next('.phpdocumentor-table-of-contents')
                    .find('.phpdocumentor-table-of-contents__entry')
                    .should('contain', 'Pizza');
            });

            it('Goes to the "Pizza" sub-namespace when you click on it', function () {
                cy.get('h3#namespaces')
                    .contains("Namespaces")
                    .next('.phpdocumentor-table-of-contents')
                    .find('.phpdocumentor-table-of-contents__entry')
                    .contains("Pizza")
                    .click();

                shouldVisitPageWithTitle('/namespaces/marios-pizza.html', 'Pizza');
            });
        })

        describe('Interfaces, Classes and Traits', function () {
            const sectionTitle = 'Interfaces, Classes and Traits';

            it('Has a section "Interfaces, Classes and Traits" with a table of contents', function () {
                cy.get('h3#interfaces_class_traits')
                    .contains(sectionTitle)
                    .next('.phpdocumentor-table-of-contents');
            });

            it('Features the "Product" interface', function () {
                const name = 'Product';

                cy.get('h3#interfaces_class_traits')
                    .contains(sectionTitle)
                    .next('.phpdocumentor-table-of-contents')
                    .contains('.phpdocumentor-table-of-contents__entry', name)
                    .should('have.class', '-interface');
            });

            it('Features the "Pizzeria" class and its description', function () {
                const title = 'Pizzeria';
                const description = 'Entrypoint for this pizza ordering application.';

                cy.get('h3#interfaces_class_traits')
                    .contains(sectionTitle)
                    .next('.phpdocumentor-table-of-contents')
                    .contains('.phpdocumentor-table-of-contents__entry', title)
                    .should('have.class', '-class')
                    .next('dd')
                    .should('have.text', description)
            });

            it('Goes to "Pizzeria" its detail page when you click on it', function () {
                const title = 'Pizzeria';

                cy.get('h3#interfaces_class_traits')
                    .contains(sectionTitle)
                    .next('.phpdocumentor-table-of-contents')
                    .contains('.phpdocumentor-table-of-contents__entry a', title)
                    .click();

                shouldVisitPageWithTitle('/classes/Marios-Pizzeria.html', title);
            });

            it('Features the "SharedTrait" trait with its description', function () {
                const name = 'SharedTrait';

                cy.get('h3#interfaces_class_traits')
                    .contains(sectionTitle)
                    .next('.phpdocumentor-table-of-contents')
                    .contains('.phpdocumentor-table-of-contents__entry', name)
                    .should('have.class', '-trait')
                    .next('dd')
                    .should('have.text', 'Trait that all pizza\'s could share.');
            });
        });
    });

    describe('Constants section', function() {
        let sectionTitle = 'Constants';

        it('Shows the title "Constants"', function () {
            cy.get('h3#constants')
                .contains(sectionTitle);
        });

        it('Features the "OVEN_TEMPERATURE" constant', function () {
            let name = 'OVEN_TEMPERATURE';

            cy.get('h3#constants')
                .contains(sectionTitle)
                .siblings()
                .contains('.phpdocumentor-element', name)
                .should('have.class', '-constant');
        });
    });

    describe('Functions section', function() {
        let sectionTitle = 'Functions';

        it('Shows the title "Functions"', function () {
            cy.get('h3#functions')
                .contains(sectionTitle);
        });

        it('features the "heatOven" function', function () {
            let title = 'heatOven';

            cy.get('h3#functions')
                .contains(sectionTitle)
                .siblings()
                .contains('.phpdocumentor-element', title)
                .should('have.class', '-function');
        });
    });
});
