describe('Class Detail Page', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizzeria.html');
    });

    it('Has "Pizzeria" as title', function() {
        cy.get('.phpdocumentor-content__title').contains("Pizzeria");
    });

    // TODO: Partially broken. Either remove the "Home" part of this test if the Home breadcrumb will no longer be created or fix it.
    //it('Has a breadcrumb featuring "Home" and "Marios"', function() {
    it('Has a breadcrumb featuring "Marios"', function() {
        //cy.get('.phpdocumentor-breadcrumbs').contains("Home");
        cy.get('.phpdocumentor-breadcrumbs').contains("Marios");
        cy.get('.phpdocumentor-breadcrumbs > li').should('have.length', /*3*/ 2);
    });

    // TODO: Broken. Either remove this test if the Home breadcrumb will no longer be created or fix it.
    it.skip('will send you to the index when clicking on "Home" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Home").click();
        cy.url().should('include', '/index.html');
    });

    it('will send you to the namespace page when clicking on "Marios" in the breadcrumb', function() {
        cy.get('.phpdocumentor-breadcrumbs').contains("Marios").click();
        cy.url().should('include', '/namespaces/marios.html');
    });

    it('Has a summary', function() {
        cy.get('.phpdocumentor-summary')
            .contains("Entrypoint for this pizza ordering application.");
    });

    it('Has a description', function() {
        cy.get('.phpdocumentor-description')
            .contains("This class provides an interface through which you can order pizza's and pasta's from Mario's Pizzeria.");
    });

    it('Has a tags', function () {
        cy.get('.phpdocumentor-tag-list__heading')
            .contains("Tags")
    })

    describe ('Shows class tags', function () {
        it('Shows link without description', function() {
            cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                .contains('https://wwww.phpdoc.org')
                .should('have.attr', 'href', 'https://wwww.phpdoc.org')
        });

        it('Shows link with description', function() {
            cy.get('.phpdocumentor-element.-class  > .phpdocumentor-tag-list >.phpdocumentor-tag-list__definition > a')
                .contains('docs')
                .parent()
                .should('have.attr', 'href', 'https://docs.phpdoc.org')
        });
    });

    it('Shows a single implemented interface; which is not clickable because it is external', function() {
        cy.get('.phpdocumentor-element__implements').contains("JsonSerializable");
        cy.get('.phpdocumentor-element__implements abbr')
            .should("have.attr", 'title', '\\JsonSerializable');
    });

    it('Show methods with return type in the Table of Contents', function() {
        cy.get('.phpdocumentor-table-of-contents__entry')
            .contains("jsonSerialize()").parent()
            .contains(': array'); // type
    });

    describe('Showing a method in a class', function() {
        let methods;

        const findMethod = (name) => methods.get('.phpdocumentor-element__name').contains(name).parent();

         before(function(){
            cy.visit('build/default/classes/Marios-Pizzeria.html');
            methods = cy.get('.phpdocumentor-element.-method');
        });

        it('Shows the variadic indicator with argument "$pizzas" in the "order" method', function() {
            let method = methods.get('.phpdocumentor-element__name').contains("order()").parent();
            method.get('.phpdocumentor-signature__element__variadic-operator').contains('...');
        });

        describe('Shows the "jsonSerialize" method; as an example of a public, implemented, method from the "JsonSerializable" interface', function () {
            let method;

            before(function(){
                method = findMethod('jsonSerialize()');
            });

            it('Shows the name "jsonSerialize()"', function() {
                method.get('.phpdocumentor-element__name').contains("jsonSerialize()");
            });

            it('Shows the file name where "jsonSerialize()" is located', function() {
                let el = method.get('.phpdocumentor-element-found-in__file');
                el.contains('Pizzeria.php');
                el.should('have.attr', 'title', '/data/examples/MariosPizzeria/Pizzeria.php');
            });

            it('Shows the line number where "jsonSerialize()" is located', function() {
                method.get('.phpdocumentor-element-found-in__line').should('be.an', 'integer');
            });

            it('Does not show a name or description because it @inheritdocs an external method', function() {
                method.get('.phpdocumentor-summary').should('not.exist');
                method.get('.phpdocumentor-description').should('not.exist');
            });

            it('Has a "public" visibility class to influence styling', function() {
                method.should('have.class', '.-public');
                method.should('not.have.class', '.-protected');
                method.should('not.have.class', '.-private');
            });

            it('Is not marked as static, final or abstract', function() {
                method.should('not.have.class', '.-final');
                method.should('not.have.class', '.-static');
                method.should('not.have.class', '.-abstract');
            });

            describe('signature', function () {
                let signature;

                beforeEach(function(){
                    signature = method.get('.phpdocumentor-signature');
                });

                it('Shows the "public" visibility specifier', function() {
                    signature.get('.phpdocumentor-signature__visibility').contains('public');
                });

                it('Shows the name of the method', function() {
                    signature.get('.phpdocumentor-signature__argument__name').contains('jsonSerialize');
                });

                it('Shows the "array" return value', function() {
                    signature.get('.phpdocumentor-signature__argument__return-type').contains('array');
                });
            });
        });

        describe('Shows "doOrder" as an example of a protected static method with summary and description', function () {
            let method;

            before(function(){
                method = findMethod('doOrder()');
            });

            it('Shows the name "doOrder()"', function() {
                method.get('.phpdocumentor-element__name').contains("doOrder()");
            });

            it('Has a "protected private" visibility class to influence styling', function() {
                method.should('not.have.class', '.-public');
                method.should('have.class', '.-protected');
                method.should('not.have.class', '.-private');
            });

            it('Is marked as static, but not as final, deprecated or abstract', function() {
                method.should('not.have.class', '.-final');
                method.should('have.class', '.-static');
                method.should('not.have.class', '.-abstract');
                method.should('not.have.class', '.-deprecated');
            });

            describe('signature', function () {
                let signature;

                beforeEach(function(){
                    signature = method.get('.phpdocumentor-signature');
                });

                it('Shows the "protected" visibility specifier', function() {
                    signature.get('.phpdocumentor-signature__visibility').contains('protected');
                });

                it('Shows the "static" keyword', function() {
                    signature.get('.phpdocumentor-signature__static').contains('static');
                });

                it('Shows "array" as return value', function() {
                    signature.get('.phpdocumentor-signature__argument__return-type').contains('array');
                });
            });
        });

        describe('Shows "doOldOrder" as an example of a deprecated private final method', function () {
            let method;

            before(function(){
                method = findMethod('doOldOrder()');
            });

            it('Shows the name "doOldOrder()"', function() {
                method.get('.phpdocumentor-element__name').contains("doOldOrder()");
            });

            it('Has a "private" visibility class to influence styling', function() {
                method.should('not.have.class', '.-public');
                method.should('not.have.class', '.-protected');
                method.should('have.class', '.-private');
            });

            it('Is marked as final and deprecated, but not as static or abstract', function() {
                method.should('have.class', '.-final');
                method.should('not.have.class', '.-static');
                method.should('not.have.class', '.-abstract');
                method.should('have.class', '.-deprecated');
            });

            describe('signature', function () {
                let signature;

                beforeEach(function(){
                    signature = method.get('.phpdocumentor-signature');
                });

                it('has the deprecated modifier', function() {
                    signature.should('have.class', '.-deprecated');
                });

                it('Shows the "private" visibility specifier', function() {
                    signature.get('.phpdocumentor-signature__visibility').contains('private');
                });

                it('Shows the "final" keyword', function() {
                    signature.get('.phpdocumentor-signature__final').contains('final');
                });

                it('Shows "false" as return value; the return tag overrides the typehint', function() {
                    // @todo fix this; this should have failed!!!
                    signature.get('.phpdocumentor-signature__argument__return-type').contains('false');
                });
            });
        });
    });
});
