import {getElementWithName} from "./helpers/elements.lib";

describe('Showing functions in a namespace', function() {
    beforeEach(function(){
        cy.visit('build/default/namespaces/marios.html');
    });

    describe('Meta-data', function() {
        it('Is not marked as deprecated by default', function () {
            getElementWithName('function', 'heatOven()')
                .should('not.have.class', '-deprecated');
        });

        it('Can be marked as deprecated', function() {
            getElementWithName('function', 'populateTemperature()')
                .should('have.class', '-deprecated');
        });
    });

    describe('Synopsis', function() {
        it('Show the name', function() {
            getElementWithName('function', 'heatOven()')
                .should('be.visible');
        });

        it('Show the file name where the function is located in the project', function() {
            getElementWithName('function', 'heatOven()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'functions.php')
                .find('abbr')
                .should('have.attr', 'title', 'src/functions.php');
        });

        it('Links to the file documentation wherein the function is', function() {
            getElementWithName('function', 'heatOven()')
                .find('.phpdocumentor-element-found-in__file')
                .contains('a', 'functions.php')
                .should('have.attr', 'href', 'files/src-functions.html');
        });

        it('Show the line number where the function is located', function() {
            getElementWithName('function', 'heatOven()')
                .find('.phpdocumentor-element-found-in__line')
                .should((element) => {
                    expect(parseInt(element.text())).to.be.at.least(1);
                });
        });
    });

    describe('Signature', function() {
        it('has the deprecated modifier', function() {
            getElementWithName('function', 'populateTemperature()')
                .find('.phpdocumentor-signature')
                .should('have.class', '-deprecated');
        });

        it('Shows the name of the function', function() {
            getElementWithName('function', 'heatOven()')
                .find('.phpdocumentor-signature__name')
                .contains('heatOven');
        });

        it('Shows the return value', function() {
            getElementWithName('function', 'coolOven()')
                .find('.phpdocumentor-signature__response_type')
                .contains('bool');
        });

        describe ('Arguments', function() {
            it('Show the name of argument $degrees', function () {
                getElementWithName('function', 'coolOven()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                    .contains('$degrees');
            });

            it('Show the default value of argument $degrees', function () {
                getElementWithName('function', 'coolOven()')
                    .find('.phpdocumentor-signature .phpdocumentor-signature__argument__name')
                    .contains('$degrees')
                    .parent()
                    .find('.phpdocumentor-signature__argument__default-value')
                    .contains('42')
            });
        });
    });

    describe ('Shows the parameters for a function', function () {
        it('Can have a parameters section', function () {
            getElementWithName('function', 'coolOven()')
                .find('.phpdocumentor-argument-list__heading')
                .contains('Parameters');
        })
    });

    describe ('Shows what a function returns', function () {
        it('Can have a return values section', function () {
            getElementWithName('function', 'coolOven()')
                .find('.phpdocumentor-return-value__heading')
                .contains('Return values');
        })

        it('Will show the type and description', function () {
            getElementWithName('function', 'coolOven()')
                .find('.phpdocumentor-return-value__heading')
                .next()
                .should('have.class', 'phpdocumentor-signature__response_type')
                .contains('bool')
                .next('.phpdocumentor-description')
                .contains('whether cooling succeeded')
        })
    });
});
