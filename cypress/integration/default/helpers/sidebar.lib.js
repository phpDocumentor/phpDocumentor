export function getSidebarHeaderByTitle(title) {
    return cy.get('.phpdocumentor-sidebar__category-header')
        .contains(title)
}

export function getSidebarItemByTitle(sectionTitle, entryTitle, type = 'package') {
    return getSidebarHeaderByTitle(sectionTitle)
        .siblings('.phpdocumentor-sidebar__root-' + type)
        .contains(entryTitle);
}
