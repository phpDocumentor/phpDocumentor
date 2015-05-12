function filterElements() {
    inherited = !$('#show-inherited').hasClass('deselected');
    public = !$('#show-public').hasClass('deselected');
    protected = !$('#show-protected').hasClass('deselected');
    private = !$('#show-private').hasClass('deselected');

    $('div.public').each(function(index, val) {
        $(val).toggle(public && !($(val).hasClass('inherited_from') && !inherited));
    });
    $('div.protected').each(function(index, val) {
        $(val).toggle(protected && !($(val).hasClass('inherited_from') && !inherited));
    });
    $('div.private').each(function(index, val) {
        $(val).toggle(private && !($(val).hasClass('inherited_from') && !inherited));
    });
}

$(document).ready(function() {
    $('#show-public, #show-protected, #show-private, #show-inherited')
            .css('cursor', 'pointer')
            .click(function() {
                $(this).toggleClass('deselected');
                if ($(this).hasClass('deselected')) {
                    $(this).fadeTo('fast', '0.4');
                } else {
                    $(this).fadeTo('fast', '1.0');
                }
                filterElements();
                return false;
            });
    $('#show-protected, #show-private').click();
});