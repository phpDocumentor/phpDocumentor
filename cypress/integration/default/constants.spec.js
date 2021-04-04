import {getElementWithName} from "./helpers/elements.lib";

describe('Showing constants for a class', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizza.html');
    });

    describe('Meta-data', function() {
        it('Can be marked "public" (visibility) to influence styling', function () {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "protected" (visibility) to influence styling', function() {
            getElementWithName('constant', 'TYPE_AMERICAN')
                .should('not.have.class', '-public')
                .and('have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "private" (visibility) to influence styling', function() {
            getElementWithName('constant', 'TYPE_HYBRID')
                .should('not.have.class', '-public')
                .and('not.have.class', '-protected')
                .and('have.class', '-private');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .should('be.visible');
        });

        it('Show the file name where the constant is located in the project', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizza.php');
        });

        it('Links to the file documentation wherein the constant is', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .should('have.attr', 'href', 'files/src-pizza.html');
        });

        it('Show the line number where the constant is located', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Can show the "protected" visibility specifier', function() {
            getElementWithName('constant', 'TYPE_AMERICAN')
                .find('.phpdocumentor-signature__visibility')
                .contains('protected');
        });

        it('Can show the "private" visibility specifier', function() {
            getElementWithName('constant', 'TYPE_HYBRID')
                .find('.phpdocumentor-signature__visibility')
                .contains('private');
        });

        it('has the deprecated modifier', function() {
            getElementWithName('constant', 'TYPE_AMERICAN')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the name of the constant', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-signature__name')
                .contains('TYPE_ITALIAN');
        });

        it('Shows the type', function() {
            getElementWithName('constant', 'TYPE_AMERICAN')
                .find('.phpdocumentor-signature__type')
                .contains('string');
        });

        it('Type defaults to mixed when unable to infer', function() {
            getElementWithName('constant', 'TYPE_ITALIAN')
                .find('.phpdocumentor-signature__type')
                .contains('mixed');
        });
    });
});

describe('Showing constants for an interface', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Product.html');
    });

    describe('Meta-data', function() {
        it('Can be marked "public" (visibility) to influence styling', function () {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "protected" (visibility) to influence styling', function() {
            getElementWithName('constant', 'PROTECTED_CONSTANT')
                .should('not.have.class', '-public')
                .and('have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "private" (visibility) to influence styling', function() {
            getElementWithName('constant', 'PRIVATE_CONSTANT')
                .should('not.have.class', '-public')
                .and('not.have.class', '-protected')
                .and('have.class', '-private');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .should('be.visible');
        });

        it('Show the file name where the constant is located in the project', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Product.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Product.php');
        });

        it('Links to the file documentation wherein the constant is', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Product.php')
                .should('have.attr', 'href', 'files/src-product.html');
        });

        it('Show the line number where the constant is located', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Can show the "protected" visibility specifier', function() {
            getElementWithName('constant', 'PROTECTED_CONSTANT')
                .find('.phpdocumentor-signature__visibility')
                .contains('protected');
        });

        it('Can show the "private" visibility specifier', function() {
            getElementWithName('constant', 'PRIVATE_CONSTANT')
                .find('.phpdocumentor-signature__visibility')
                .contains('private');
        });

        it('has the deprecated modifier', function() {
            getElementWithName('constant', 'PROTECTED_CONSTANT')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the name of the constant', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-signature__name')
                .contains('PUBLIC_CONSTANT');
        });

        it('Shows the type', function() {
            getElementWithName('constant', 'PROTECTED_CONSTANT')
                .find('.phpdocumentor-signature__type')
                .contains('string');
        });

        it('Type defaults to mixed when unable to infer', function() {
            getElementWithName('constant', 'PUBLIC_CONSTANT')
                .find('.phpdocumentor-signature__type')
                .contains('mixed');
        });
    });
});
