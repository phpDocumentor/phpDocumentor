$().ready(function() {    

    /* Splitter */
    $(".resizable").splitter({
        sizeLeft: 250
    });
    
    /*
     * Tabs
     */
    $tabs = $("#tabs").tabs({
        tabTemplate: '<li><a href="#{href}">#{label}</a> <span class="ui-icon ui-icon-close">Remove Tab</span></li>',
        load: function(event, ui) {
            AttachLinkEvents();
        },
        add: function (event, ui) {
            
            $( ui.panel ).append( '<iframe src="'+tab_url+'" frameBorder="0"></iframe>' );
            
            $tabs.tabs('select', '#' + ui.panel.id);
        },
        ajaxOptions: {
            error: function( xhr, status, index, anchor ) {
                $( anchor.hash ).html("Couldn't load this tab. We'll try to fix this as soon as possible. ");
            }
        }	
    });
	
    $('#tabs span.ui-icon-close').live('click', function() {
        var index = $('li',$tabs).index($(this).parent());
        $tabs.tabs('remove', index);
    });
    
    /** Tabs */
    $(".tabs").tabs();
    
    /*
     * Tree
     */
    $("#tree").jstree({
        "core" : {
            'animation':0
        },
        "plugins" : [ "html_data", "themeroller", "ui", "cookies", "search"],
        "cookies" : {
            "save_opened" : "evolved_tree",
            "save_selected" : "evolved_selected",
            "auto_save" : true
        },
        "search": {
            "case_insensitive" : true,
            "ajax": false
        },
        "themeroller": {
            "opened" : "ui-icon-triangle-1-s",
            "item" : "tree-item",
            "item_h" : "tree-item-hover",
            "item_a" : "tree-item-active",
            "item_leaf" : false
        }
    });
    
    this_domain = getDomain(window.location);
    
    $("#tree").bind('click.jstree', function(event){
        var trgt = event.target;
        
        if($(trgt).get(0).tagName == 'A'){
            var href = $(trgt).attr('href');
            
            if(href.charAt(0) != '#'){
                var todomain = getDomain(trgt.href);
                if(this_domain == todomain){
                    var classs = $(trgt).attr('class');
                    var title = '<span class="' + classs + '">' + $(trgt).text() + '</span>';

                    showPageUrl(trgt.href, title)
                }
            } else {
                $(this).jstree("toggle_node",trgt);
            }
        }

    });
    
    attachLinkEvents();
    
    /*
     * Search
     */
    $("#searchbar .ui-icon-close").click(function(){
        searchFor();
        $("#search").val('');
    })
    $("#search").autocomplete({
        minLength: 0,
        delay:300,
        search: function(event, ui){
            searchFor(this.value)
            return false;
        }
    });
    searchFor($("#search").val());
    
    /* Window Size */
    $(window).resize(updateViewport).trigger('resize');
});

function updateViewport(event){
    var height = ($(window).height()-$('#db-header').outerHeight(true))
    
    document.getElementById("content-container").style.height = height+'px';
    $("#tabs > .ui-tabs-panel").css('height', height-$('#tabs .ui-tabs-nav').outerHeight(true) );
    
    $("#content-container").trigger('resize.splitter');
}

function attachLinkEvents() {
    this_domain = getDomain(window.location);
    
    $('#db-header a[href]').once('attach-link', function(){
        if($(this).attr('href').indexOf('javascript') != 0){
            var todomain = getDomain(this.href);
            if($(this).attr('href').charAt(0) != '#'  && this_domain == todomain){
                $(this).click(showPage);
            }
        }
    });
}

function getDomain(pUrl) {
    var url = new String(pUrl);
    return url.match(/:\/\/(www\.)?(.[^/:]+)/)[2];
}

tab_counter = 2;

/** Puts the content of a page in the page div + rescan the page for the href's **/
function showPage(pUrl) {
    var classs = $(this).parent().attr('class');
    var title = '<span class="' + classs + '">' + $(this).text() + '</span>';

    showPageUrl(this.href, title)
    
    return false;
}

function showPageUrl(url, title){
    tab_url = url;
    $tabs.tabs('add','#tabs-'+tab_counter, title);
    
    tab_counter++;
    $tabs.tabs('select', $tabs.tabs('length') - 1 );
}

function searchAjax(root){
    $("#search_box").show().autocomplete({
        source: root + "ajax_search.php",
        minLength: 2,
        search: function (event, ui) {
            if($("#search_box").val() == ''){
                $("#search_container .ui-icon-close").hide();
            } else {
                $("#search_container .ui-icon-close").show();
            }
        },
        select: function (event, ui) {
            showPageUrl(ui.item.id, ui.item.value);
        },
        open: function(event, ui) {
                    
            $('ul.ui-autocomplete a').removeClass('ui-corner-all');
            $('ul.ui-autocomplete')
            .removeAttr('style')
            .hide()
            .removeClass('ui-corner-all')
            .addClass('ui-corner-bottom')
            .css('width', $("#search_container").width()-2)
            .appendTo("#search_results")
            .show();
        }
    })
    .data("autocomplete")
    ._renderItem = function( ul, item ) {
        return $( '<li></li>' )
        .data( "item.autocomplete", item )
        .append( '<a><img src="images/icons/'+item.type+'.png" align="absmiddle" />'+ item.label + '</a>' )
        .appendTo( ul );
    };
                
    searchBasic();
}

function searchLocal(root){
    var is_chrome = /chrome/.test( navigator.userAgent.toLowerCase() );
    var is_local = /file:\/\//.test(document.location.href);
    if (is_chrome && is_local)
    {
        // search is disabled on chrome with local files due to http://code.google.com/p/chromium/issues/detail?id=40787
        $("#search_box_icon").hide();

        return;
    }

    $("#search_box").show();
    var search_index = {};
    $.ajax({
        url: root + "search_index.xml",
        dataType: ($.browser.msie) ? "text" : "xml",
        error: function(data) {
            alert('An error occurred using the search data');
        },
        success: function( data ) {
            var xml;
            if (typeof data == "string") {
                xml = new ActiveXObject("Microsoft.XMLDOM");
                xml.async = false;
                xml.loadXML(data);
            } else {
                xml = data;
            }

            search_index = $("node", xml).map(function() {
                type = $("type", this).text();
                return {
                    type: type,
                    value: $("value", this).text(),
                    label: $("value", this).text(),
                    id: $("id", this).text()
                };
            }).get();

            $("#search_box").autocomplete({
                source: search_index,
                select: function(event, ui) {
                    showPageUrl(ui.item.id, ui.item.value);
                },
                open: function(event, ui) {
                    
                    $('ul.ui-autocomplete a').removeClass('ui-corner-all');
                    $('ul.ui-autocomplete')
                    .removeAttr('style')
                    .hide()
                    .removeClass('ui-corner-all')
                    .addClass('ui-corner-bottom')
                    .css('width', $("#search_container").width()-2)
                    .appendTo("#search_results")
                    .show();
                }
            })
            .data("autocomplete")
            ._renderItem = function( ul, item ) {
                return $( '<li></li>' )
                .data( "item.autocomplete", item )
                .append( '<a><img src="images/icons/'+item.type+'.png" align="absmiddle" />'+ item.label + '</a>' )
                .appendTo( ul );
            };
            

        }
    });
    
    searchBasic();
}

function searchBasic(){
    $("#search_box_icon").click(function(){
        $("#search_container").show();
        $(this).addClass('active');
    })
                
    $("#search_container").mouseleave(function(){
        $("#search_container").hide();
        $("#search_box_icon").removeClass('active');
    });
    
    $("#search_container .ui-icon-close").hide().click(function(){
        $("#search_box").val('');
        $("#search_results").empty();
    });
}

function searchFor(search) {
    if(search && search.length)
    {
        $("#tree")
        .jstree('search', search)
        .find('li:not(:has(.jstree-search))')
        .css('display', 'none')
        .end()
        .find('li:has(.jstree-search)')
        .css('display', 'block')
        .end();
        $("#searchbar .ui-icon-close").show();
    }
    else
    {
            
        $("#tree")
        .find('li')
        .css('display', '')
        .end()
        .find('.jstree-open')
        .removeClass('jstree-open')
        .addClass('jstree-closed')
        .end()
        .find('.jstree-search')
        .removeClass('jstree-search')
        .end();
        $("#tree").jstree("clear_search");
        $("#searchbar .ui-icon-close").hide();
    }
}