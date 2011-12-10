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
    $('a.gripper').click(function() {
        $(this).nextAll('div.code-tabs').slideToggle();
        $(this).children('img').toggle();
        return false;
    });

    $('div.method code span.highlight,div.function code span.highlight,div.constant code span.highlight,div.property code span.highlight').css('cursor', 'pointer');

    $('div.method code span.highlight,div.function code span.highlight,div.constant code span.highlight,div.property code span.highlight').click(function() {
        $(this).parent().nextAll('div.code-tabs').slideToggle();
        $(this).parent().prevAll('a.gripper').children('img').toggle();
        return false;
    });

//    $('div.code-tabs').hide();
//    $('a.gripper').show();
//    $('div.code-tabs:empty').prevAll('a.gripper').html('');

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

    // add the sliding behaviour to the file navigation and show it
    // it is initially hidden for non-JS users.
//    $("#file-nav-box").show().hover(function() {
//        $("#file-nav-container").slideDown(400);
//    }, function() {
//        $("#file-nav-container").slideUp(400);
//    });
});