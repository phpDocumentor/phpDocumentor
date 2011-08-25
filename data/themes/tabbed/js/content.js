$(document).ready(function() {
    $("#accordion").accordion({
        collapsible: true,
        autoHeight:  false,
        fillSpace:   true
    });

    $(".tabs").tabs();
                
    attachLinkEvents();
});
            
function attachLinkEvents() {
    this_domain = getDomain(window.location);
    
    $('a[href]').once('attach-link', function(){
        if($(this).attr('href').indexOf('javascript') != 0 && $(this).attr('href').charAt(0) != '#'){
            var todomain = getDomain(this.href);
            if(this_domain == todomain){
                $(this).click(window.parent.showPage);
            } else {
                $(this).click(function(){
                    window.open(this.href);
                    return false;
                })
            }
        }
    });
}

function getDomain(pUrl) {
    var url = new String(pUrl);
    return url.match(/:\/\/(www\.)?(.[^/:]+)/)[2];
}