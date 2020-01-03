describe('Frontpage', function() {
    beforeEach(function(){
        cy.visit('build/clean/index.html');
    });

    it('Has the "Marios" namespace in the main content', function() {
        cy.get('.content.namespace').contains("Marios");
    });
});
