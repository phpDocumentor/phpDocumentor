export function getOnThisPage() {
    return cy.get('.phpdocumentor-on-this-page__content');
}

export function getSection(name) {
    return getOnThisPage()
        .find('.phpdocumentor-on-this-page-section__title')
        .contains(name)
        .next('li');
}

export function getSummaryEntry(name) {
    return getSection('Table Of Contents')
        .find('li').contains(name);
}

export function getEntryIn(section, name) {
    return getSection(section)
        .find('li').contains(name);
}

