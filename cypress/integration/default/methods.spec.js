import {getElementWithName} from "./helpers/elements.lib";

describe('Showing methods for a class', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizzeria.html');
    });

    describe('Meta-data', function() {
        it('Can be marked "public" (visibility) to influence styling', function () {
            getElementWithName('method', 'jsonSerialize()')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "protected" (visibility) to influence styling', function() {
            getElementWithName('method', 'doOrder()')
                .should('not.have.class', '-public')
                .and('have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "private" (visibility) to influence styling', function() {
            getElementWithName('method', 'doOldOrder()')
                .should('not.have.class', '-public')
                .and('not.have.class', '-protected')
                .and('have.class', '-private');
        });

        it('Is not marked as static, final or abstract by default', function () {
            getElementWithName('method', 'jsonSerialize()')
                .should('not.have.class', '-final')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract');
        });

        it('Can be marked as static, but not as final, deprecated or abstract', function() {
            getElementWithName('method', 'doOrder()')
                .should('not.have.class', '-final')
                .and('have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('not.have.class', '-deprecated');
        });

        it('Can be marked as final and deprecated, but not as static or abstract', function() {
            getElementWithName('method', 'doOldOrder()')
                .should('have.class', '-final')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('have.class', '-deprecated');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('method', 'jsonSerialize()')
                .should('be.visible');
        });

        it('Show the file name where the method is located in the project', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizzeria.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizzeria.php');
        });

        it('Links to the file documentation wherein the method is', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizzeria.php')
                .should('have.attr', 'href', 'files/src-pizzeria.html');
        });

        it('Show the line number where the method is located', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });

        // TODO: A feature request is to include https://github.com/JetBrains/phpstorm-stubs for inheritance resolving
        it('Does not show a summary because it @inheritdocs an external method', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-summary')
                .should('not.exist');
        });

        // TODO: A feature request is to include https://github.com/JetBrains/phpstorm-stubs for inheritance resolving
        it.skip('Does not show a description because it @inheritdocs an external method', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-signature')
                .next()
                .should('not.have.class', 'phpdocumentor-description');
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Can show the "protected" visibility specifier', function() {
            getElementWithName('method', 'doOrder()')
                .find('.phpdocumentor-signature__visibility')
                .contains('protected');
        });

        it('Can show the "private" visibility specifier', function() {
            getElementWithName('method', 'doOldOrder()')
                .find('.phpdocumentor-signature__visibility')
                .contains('private');
        });

        it('has the deprecated modifier', function() {
            getElementWithName('method', 'doOldOrder()')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the "static" keyword', function() {
            getElementWithName('method', 'doOrder()')
                .find('.phpdocumentor-signature .phpdocumentor-signature__static')
                .should('exist')
                .and('contain', 'static');
        });

        it('Shows the "final" keyword', function() {
            getElementWithName('method', 'doOldOrder()')
                .find('.phpdocumentor-signature .phpdocumentor-signature__final')
                .contains('final');
        });

        it('Shows the name of the method', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-signature__name')
                .contains('jsonSerialize');
        });

        it('Shows the "array" return value', function() {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-signature__response_type')
                .contains('array');
        });

        it('Shows "false" as return value; the return tag overrides the typehint', function() {
            getElementWithName('method', 'doOldOrder()')
                .find('.phpdocumentor-signature__response_type')
                .contains('false');
        });

        describe ('Arguments', function() {
            it('Show the name of argument $temp', function () {
                cy.visit('build/default/classes/Marios-Oven.html');

                getElementWithName('method', 'heatToTemp()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                    .contains('$temp');
            });

            it('Show the default value of argument $temp', function () {
                cy.visit('build/default/classes/Marios-Oven.html');

                getElementWithName('method', 'heatToTemp()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                    .contains('$temp')
                    .parent()
                    .find('.phpdocumentor-signature__argument__default-value')
                    .contains('self::DEFAULT_TEMPERATURE')
            });

            it('Show a variadic indicator', function () {
                getElementWithName('method', 'order()')
                    .find('.phpdocumentor-signature')
                    .find('.phpdocumentor-signature__argument__variadic-operator')
                    .contains('...');
            });

            it('Shows the default value links to type Sauce', function () {
                cy.visit('build/default/classes/Marios-Pizza-Base.html');

                getElementWithName('method', '__construct()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__type')
                    .contains('a', 'Sauce')
                    .should('have.attr', 'href', 'classes/Marios-Pizza-Sauce.html')
                    .find('abbr')
                    .should('have.attr', 'title', '\\Marios\\Pizza\\Sauce');

                getElementWithName('method', '__construct()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__default-value')
                    .should('contain.text', 'new Sauce()')
                    .find('a')
                    .should('have.attr', 'href', 'classes/Marios-Pizza-Sauce.html')
                    .find('abbr')
                    .should('have.attr', 'title', '\\Marios\\Pizza\\Sauce');
            });
        });
    });

    describe ('Shows tags', function () {
        it('Can have a tags section', function () {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-tag-list__heading')
                .contains("Tags");
        })

        it('Will show an inheritDoc tag', function () {
            getElementWithName('method', 'jsonSerialize()')
                .find('.phpdocumentor-tag-list__heading')
                .next()
                .should('have.class', 'phpdocumentor-tag-list')
                .contains('.phpdocumentor-tag-list__entry', 'inheritDoc');
        })
    });

    describe ('Shows the parameters for a method', function () {
        it('Can have a parameters section', function () {
            getElementWithName('method', 'order()')
                .find('.phpdocumentor-argument-list__heading')
                .contains('Parameters');
        })

        it('Will show a parameter with a linked type', function () {
            getElementWithName('method', 'order()')
                .find('.phpdocumentor-argument-list__heading')
                .next()
                .should('have.class', 'phpdocumentor-argument-list')
                .within(function () {
                    // find the argument entry row that mentions the $pizzas parameter
                    cy.contains('.phpdocumentor-argument-list__entry', '$pizzas')
                        .as('pizzasEntry');

                    // assert the name is present
                    cy.get('@pizzasEntry')
                        .find('.phpdocumentor-argument-list__argument__name')
                        .contains('$pizzas');

                    // assert there is a linked type pointing to the Pizza class (href + abbr title)
                    cy.get('@pizzasEntry')
                        .find('.phpdocumentor-argument-list__argument__type a')
                        .should('have.attr', 'href', 'classes/Marios-Pizza.html')
                        .find('abbr')
                        .should('have.attr', 'title', '\\Marios\\Pizza');
                });
        })

        it('Will show a parameter with a description', function () {
            getElementWithName('method', 'doOrder()')
                .find('.phpdocumentor-argument-list__heading')
                .next('.phpdocumentor-argument-list')
                .contains('.phpdocumentor-argument-list__entry', '$pizza')
                .next('.phpdocumentor-argument-list__definition')
                .contains('.phpdocumentor-description', 'The specific pizza to place an order for.');
        })

        it('Will show default parameter value null without link', function () {
            getElementWithName('method', 'setBestPizzaEver()')
                .find('.phpdocumentor-argument-list__heading')
                .next()
                .should('have.class', 'phpdocumentor-argument-list')
                .within(function () {
                    // find the argument entry row that mentions the $pizzas parameter
                    cy.contains('.phpdocumentor-argument-list__entry', 'pizza')
                        .as('pizzaArgument');

                    // assert the name is present
                    cy.get('@pizzaArgument')
                        .find('.phpdocumentor-argument-list__argument__name')
                        .contains('$pizza');

                    // assert there is a linked type pointing to the Pizza class (href + abbr title)
                    cy.get('@pizzaArgument')
                        .find('.phpdocumentor-argument-list__argument__default-value')
                        .contains('null')
                        .find('a')
                        .should('not.exist');
                });
        })
    });

    describe ('Shows what a method returns', function () {
        it('Can have a return values section', function () {
            getElementWithName('method', 'doOrder()')
                .find('.phpdocumentor-return-value__heading')
                .contains('Return values');
        })

        it('Will show the type and description', function () {
            getElementWithName('method', 'doOrder()')
                .find('.phpdocumentor-return-value__heading')
                .next()
                .should('have.class', 'phpdocumentor-signature__response_type')
                .contains('bool')
                .next('.phpdocumentor-description')
                .contains('Whether the order succeeded')
        })
    });
});

describe('Showing methods for an interface', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Product.html');
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('method', 'getName()')
                .should('be.visible');
        });

        it('Show the file name where the method is located in the project', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Product.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Product.php');
        });

        it('Links to the file documentation wherein the method is', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Product.php')
                .should('have.attr', 'href', 'files/src-product.html');
        });

        it('Show the line number where the method is located', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Shows the name of the method', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-signature__name')
                .contains('getName');
        });

        it('Shows the return type', function() {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-signature__response_type')
                .contains('string');
        });
    });

    describe ('Shows what a method returns', function () {
        it('Can have a return values section', function () {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-return-value__heading')
                .contains('Return values');
        })

        it('Will show the type and description', function () {
            getElementWithName('method', 'getName()')
                .find('.phpdocumentor-return-value__heading')
                .next()
                .should('have.class', 'phpdocumentor-signature__response_type')
                .contains('string')
                .next('.phpdocumentor-description')
                .contains('the name of this product')
        })
    });
});

describe('Showing methods for class with parent', function() {
    beforeEach(function () {
        cy.visit('build/default/classes/Marios-Pizza-Toppings-Pepperoni.html');
    });

    describe('Synopsis', function() {
        it('Show the name', function () {
            getElementWithName('method', 'publiclyAvailable()')
                .should('be.visible');
        });

        it('Show the file name where the method points to parent class', function () {
            getElementWithName('method', 'availableToAll()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Topping.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizza/Topping.php');
        });

        it('Links to the file documentation wherein the method points to parrent class', function () {
            getElementWithName('method', 'availableToAll()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Topping.php')
                .should('have.attr', 'href', 'files/src-pizza-topping.html');
        });

        it('Show the file name where the method points to current class', function () {
            getElementWithName('method', 'publiclyAvailable()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pepperoni.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizza/Toppings/Pepperoni.php');
        });

        it('Links to the file documentation wherein the method points to current class', function () {
            getElementWithName('method', 'publiclyAvailable()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pepperoni.php')
                .should('have.attr', 'href', 'files/src-pizza-toppings-pepperoni.html');
        });
    });
});
