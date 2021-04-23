import {getElementWithName} from "./helpers/elements.lib";

describe('Showing properties for a class', function() {
    beforeEach(function(){
        cy.visit('build/default/classes/Marios-Pizza.html');
    });

    describe('Meta-data', function() {
        it('Can be marked "public" (visibility) to influence styling', function () {
            getElementWithName('property', '$description')
                .should('have.class', '-public')
                .and('not.have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "protected" (visibility) to influence styling', function() {
            getElementWithName('property', '$extra')
                .should('not.have.class', '-public')
                .and('have.class', '-protected')
                .and('not.have.class', '-private');
        });

        it('Can be marked "private" (visibility) to influence styling', function() {
            getElementWithName('property', '$secretIngredient')
                .should('not.have.class', '-public')
                .and('not.have.class', '-protected')
                .and('have.class', '-private');
        });

        it('Is not marked as static or abstract by default', function () {
            getElementWithName('property', '$name')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract');
        });

        it('Can be marked as static, but not as deprecated or abstract', function() {
            getElementWithName('property', '$description')
                .and('have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('not.have.class', '-deprecated');
        });

        it('Can be marked as deprecated, but not as static or abstract', function() {
            getElementWithName('property', '$extra')
                .and('not.have.class', '-static')
                .and('not.have.class', '-abstract')
                .and('have.class', '-deprecated');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('property', '$name')
                .should('be.visible');
        });

        it('Show the file name where the property is located in the project', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/Pizza.php');
        });

        it('Links to the file documentation wherein the property is', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'Pizza.php')
                .should('have.attr', 'href', 'files/src-pizza.html');
        });

        it('Show the line number where the property is located', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('Can show the "public" visibility specifier', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__visibility')
                .contains('public');
        });

        it('Can show the "protected" visibility specifier', function() {
            getElementWithName('property', '$extra')
                .find('.phpdocumentor-signature__visibility')
                .contains('protected');
        });

        it('Can show the "private" visibility specifier', function() {
            getElementWithName('property', '$secretIngredient')
                .find('.phpdocumentor-signature__visibility')
                .contains('private');
        });

        it('has the deprecated modifier', function() {
            getElementWithName('property', '$extra')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the "static" keyword', function() {
            getElementWithName('property', '$description')
                .find('.phpdocumentor-signature .phpdocumentor-signature__static')
                .should('exist')
                .and('contain', 'static');
        });

        it('Shows the name of the property', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__name')
                .contains('$name');
        });

        it('Shows the type', function() {
            getElementWithName('property', '$name')
                .find('.phpdocumentor-signature__type')
                .contains('string');
        });

        // FIXME: This is a bug in phpDocumentor; the following test should be green
        it.skip('Var tag overrides the type-hint', function() {
            getElementWithName('property', '$alwaysTrue')
                .find('.phpdocumentor-signature__type')
                .contains('true');
        });

        it('Var tag description is shown', function() {
            getElementWithName('property', '$secretIngredient')
                .find('.phpdocumentor-description')
                .contains('Even the type of this is secret!');
        });
    });
});
