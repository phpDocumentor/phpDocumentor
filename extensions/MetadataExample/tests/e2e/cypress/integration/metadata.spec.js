const element = (type, name) => cy.get(`.phpdocumentor-element.-${type}`)
    .find('.phpdocumentor-element__name')
    .contains(name)
    .parent();

describe('Extension metadata', function () {
    // Scenario: Metadata is rendered with the template of the extension that declares its key
    describe('On a class page', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Foo');
        });
    });

    describe('On an interface page', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Contract.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Contract');
        });
    });

    describe('On a trait page', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Reusable.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Reusable');
        });
    });

    describe('On an enum page', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Status.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Status');
        });
    });

    describe('On a function', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/namespaces/fixture.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            element('function', 'fixtureFunction()')
                .find('.metadata-example')
                .should('contain', 'metadata-for-fixtureFunction');
        });
    });

    describe('On a method', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            element('method', 'bar()')
                .find('.metadata-example')
                .should('contain', 'metadata-for-bar');
        });
    });

    describe('On a file page', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/files/src-foo.html');
        });

        it('Shows the metadata rendered by the extension template', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Foo.php');
        });
    });

    // Scenario: The template receives the metadata object and the descriptor
    describe('Template inputs', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Gives the template the metadata object and the descriptor', function () {
            cy.get('.metadata-example')
                .should('contain', 'metadata-for-Foo')
                .and('have.attr', 'data-node', 'Foo');
        });
    });

    // Scenario: Every metadata object of a descriptor is rendered with the template of its key
    describe('Several metadata keys on one descriptor', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Renders each metadata object with the template of its own key', function () {
            cy.get('.metadata-example').should('contain', 'metadata-for-Foo');
            cy.get('.metadata-second').should('contain', 'second-for-Foo');
        });
    });

    // Scenario: Metadata of an undeclared key is not rendered
    describe('Metadata of an undeclared key', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Is not shown while the other metadata is', function () {
            cy.get('.metadata-example').should('exist');
            cy.contains('undeclared-for-Foo').should('not.exist');
        });
    });

    // Scenario: Metadata of a declared key without a template is not rendered
    describe('Metadata of a declared key without a template', function () {
        beforeEach(function () {
            cy.visit('build/metadata-example/classes/Fixture-Foo.html');
        });

        it('Is not shown while the other metadata is', function () {
            cy.get('.metadata-example').should('exist');
            cy.contains('notemplate-for-Foo').should('not.exist');
        });
    });

    // Scenario: Metadata adds the entry of its extension to "On this page"
    describe('On this page', function () {
        [
            ['classes/Fixture-Foo.html', 'Foo'],
            ['classes/Fixture-Contract.html', 'Contract'],
            ['classes/Fixture-Reusable.html', 'Reusable'],
            ['classes/Fixture-Status.html', 'Status'],
            ['files/src-foo.html', 'Foo.php'],
        ].forEach(function ([page, name]) {
            it(`Links to the metadata on ${page}`, function () {
                cy.visit(`build/metadata-example/${page}`);
                cy.get('.phpdocumentor-on-this-page__content .metadata-example-entry a')
                    .should('have.attr', 'href')
                    .and('contain', `#metadata-example-${name}`);
                cy.get(`[id="metadata-example-${name}"]`).should('exist');
            });
        });
    });
});
