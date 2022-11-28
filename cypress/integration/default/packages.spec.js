import {shouldVisitPageWithTitle} from "./helpers/pages.lib";
import sidebar from "./sidebar.inc";
import search from "./search.inc";
import {getEntryIn, getSummaryEntry} from "./helpers/onThisPage.lib";
import {getToc, getTocEntry} from "./helpers/tableOfContents.lib";

describe('Packages', function() {
    beforeEach(function() {
        cy.visit('build/default/packages/Marios.html');
    });

    describe('Search', search);
    describe('In the sidebar', sidebar);

    describe('Synopsis', function() {
        it('Has a title', function() {
            cy.get('.phpdocumentor-content__title')
                .contains("Marios");
        });
    });

    describe('Table of Contents', function() {
        it('Features the "Product" interface', function () {
            getTocEntry(getToc('interfaces', 'Interfaces'), 'Product')
                .should('have.class', '-interface');
        });

        it('Features the "Pizzeria" class and its description', function () {
            const title = 'Pizzeria';
            const description = 'Entrypoint for this pizza ordering application.';

            getTocEntry(getToc('classes', 'Classes'), title)
                .should('have.class', '-class')
                .next('dd')
                .should('have.text', description)
        });

        it('Goes to "Pizzeria" its detail page when you click on it', function () {
            const title = 'Pizzeria';

            getTocEntry(getToc('classes', 'Classes'), title)
                .find('a').click();

            shouldVisitPageWithTitle('/classes/Marios-Pizzeria.html', title);
        });

        it('Features the "SharedTrait" trait with its description', function () {
            const title = 'SharedTrait';

            getTocEntry(getToc('traits', 'Traits'), title)
                .should('have.class', '-trait')
                .next('dd')
                .should('have.text', 'Trait that all pizza\'s could share.');
        });
    });

    describe('On This Page', function() {
        it('renders links to the summary items', function() {
            getSummaryEntry('Interfaces').should('exist');
            getSummaryEntry('Classes').should('exist');
            getSummaryEntry('Traits').should('exist');
            getSummaryEntry('Enums').should('exist');
            getSummaryEntry('Constants').should('exist');
            getSummaryEntry('Functions').should('exist');
        });
        it('renders references to constants on this page', function() {
            getEntryIn('Constants', 'OVEN_TEMPERATURE').should('exist');
        });
        it('renders references to functions on this page', function() {
            getEntryIn('Functions', 'coolOven').should('exist');
            getEntryIn('Functions', 'heatOven').should('exist');
        });
    });

    describe('Shows details on constants', function() {
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

    describe('Show details on functions', function() {
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
