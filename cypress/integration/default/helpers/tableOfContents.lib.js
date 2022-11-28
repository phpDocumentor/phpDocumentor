export function getToc(tocType, tocName) {
    return cy.get('h4#toc-' + tocType).contains(tocName).next('.phpdocumentor-table-of-contents');
}

export function getTocEntry(toc, name) {
    return toc.find('.phpdocumentor-table-of-contents__entry')
        .contains(name)
        .parents('.phpdocumentor-table-of-contents__entry');
}

