export function getElementWithName(type, name) {
    return cy.get(`.phpdocumentor-element.-${type}`)
        .find('.phpdocumentor-element__name')
        .contains(name)
        .parent();
}
