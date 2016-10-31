$.browser.chrome = /chrome/.test(navigator.userAgent.toLowerCase());
$.browser.ipad   = /ipad/.test(navigator.userAgent.toLowerCase());

/*!
 * JavaScript Cookie v2.1.3
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
 * Released under the MIT license
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}(';(7(a){3 b=1z;6(r v===\'7\'&&v.1u){v(a);b=x}6(r N===\'1s\'){1r.N=a();b=x}6(!b){3 c=F.u;3 d=F.u=a();d.1p=7(){F.u=c;8 d}}}(7(){7 C(){3 i=0;3 a={};A(;i<o.y;i++){3 b=o[i];A(3 c 1o b){a[c]=b[c]}}8 a}7 w(m){7 4(a,b,c){3 d;6(r p===\'1n\'){8}6(o.y>1){c=C({q:\'/\'},4.I,c);6(r c.9===\'1l\'){3 f=10 1j();f.1i(f.1h()+c.9*1f+5);c.9=f}B{d=K.1c(b);6(/^[\\{\\[]/.1b(d)){b=d}}D(e){}6(!m.P){b=Q(H(b)).n(/%(T|U|V|W|18|17|11|1k|12|13|14|15|16|Y|X|19|1a|M)/g,s)}1d{b=m.P(b,a)}a=Q(H(a));a=a.n(/%(T|U|V|W|Y|X|M)/g,s);a=a.n(/[\\(\\)]/g,1e);8(p.t=[a,\'=\',b,c.9?\'; 9=\'+c.9.1g():\'\',c.q?\'; q=\'+c.q:\'\',c.z?\'; z=\'+c.z:\'\',c.S?\'; S\':\'\'].R(\'\'))}6(!a){d={}}3 g=p.t?p.t.O(\'; \'):[];3 h=/(%[0-1m-Z]{2})+/g;3 i=0;A(;i<g.y;i++){3 j=g[i].O(\'=\');3 k=j.E(1).R(\'=\');6(k.1q(0)===\'"\'){k=k.E(1,-1)}B{3 l=j[0].n(h,s);k=m.G?m.G(k,l):m(k,l)||k.n(h,s);6(1t.J){B{k=K.1v(k)}D(e){}}6(a===l){d=k;1w}6(!a){d[l]=k}}D(e){}}8 d}4.1x=4;4.1y=7(a){8 4.L(4,a)};4.1A=7(){8 4.1B({J:x},[].E.L(o))};4.I={};4.1C=7(a,b){4(a,\'\',C(b,{9:-1}))};4.1D=w;8 4}8 w(7(){})}));',62,102,'|||var|api||if|function|return|expires||||||||||||||replace|arguments|document|path|typeof|decodeURIComponent|cookie|Cookies|define|init|true|length|domain|for|try|extend|catch|slice|window|read|String|defaults|json|JSON|call|7C|exports|split|write|encodeURIComponent|join|secure|23|24|26|2B|60|5E||new|3E|2F|3F|40|5B|5D|3C|3A|7B|7D|test|stringify|else|escape|864e|toUTCString|getMilliseconds|setMilliseconds|Date|3D|number|9A|undefined|in|noConflict|charAt|module|object|this|amd|parse|break|set|get|false|getJSON|apply|remove|withConverter'.split('|'),0,{}));

/**
 * Initializes page contents for progressive enhancement.
 */
function initializeContents()
{
    // hide all more buttons because they are not needed with JS
    $(".element a.more").hide();

    $(".clickable.class,.clickable.interface,.clickable.trait").click(function() {
        document.location = $("a.more", this).attr('href');
    });

    // change the cursor to a pointer to make it more explicit that this it clickable
    // do a background color change on hover to emphasize the clickability eveb more
    // we do not use CSS for this because when JS is disabled this behaviour does not
    // apply and we do not want the hover
    $(".element.method,.element.function,.element.class.clickable,.element.interface.clickable,.element.trait.clickable,.element.property.clickable")
        .css("cursor", "pointer")
        .hover(function() {
            $(this).css('backgroundColor', '#F8FDF6')
        }, function(){
            $(this).css('backgroundColor', 'white')}
        );

    $("ul.side-nav.nav.nav-list li.nav-header").contents()
        .filter(function(){return this.nodeType == 3 && $.trim($(this).text()).length > 0})
        .wrap('<span class="side-nav-header" />');

    $("ul.side-nav.nav.nav-list li.nav-header span.side-nav-header")
        .css("cursor", "pointer");

    // do not show tooltips on iPad; it will cause the user having to click twice
    if (!$.browser.ipad) {
        $('.btn-group.visibility,.btn-group.view,.btn-group.type-filter,.icon-custom')
            .tooltip({'placement':'bottom'});
        $('.element').tooltip({'placement':'left'});
    }

    $('.btn-group.visibility,.btn-group.view,.btn-group.type-filter')
        .show()
        .css('display', 'inline-block')
        .find('button')
        .find('i').click(function(){ $(this).parent().click(); });

    // set the events for the visibility buttons and enable by default.
    function toggleVisibility(event)
    {
        // because the active class is toggled _after_ this event we toggle it for the duration of this event. This
        // will make the next piece of code generic
        if (event) {
            $(this).toggleClass('active');
        }

        $('.element.public,.side-nav li.public').toggle($('.visibility button.public').hasClass('active'));
        $('.element.protected,.side-nav li.protected').toggle($('.visibility button.protected').hasClass('active'));
        $('.element.private,.side-nav li.private').toggle($('.visibility button.private').hasClass('active'));
        $('.element.public.inherited,.side-nav li.public.inherited').toggle(
            $('.visibility button.public').hasClass('active') && $('.visibility button.inherited').hasClass('active')
        );
        $('.element.protected.inherited,.side-nav li.protected.inherited').toggle(
            $('.visibility button.protected').hasClass('active') && $('.visibility button.inherited').hasClass('active')
        );
        $('.element.private.inherited,.side-nav li.private.inherited').toggle(
            $('.visibility button.private').hasClass('active') && $('.visibility button.inherited').hasClass('active')
        );

        // and untoggle the active class again so that bootstrap's default handling keeps working
        if (event) {
            $(this).toggleClass('active');
        }
    }
    $('.visibility button.public').on("click", toggleVisibility);
    $('.visibility button.protected').on("click", toggleVisibility);
    $('.visibility button.private').on("click", toggleVisibility);
    $('.visibility button.inherited').on("click", toggleVisibility);
    toggleVisibility();

    $('.type-filter button.critical').click(function() {
        packageContentDivs = $('.package-contents');
        packageContentDivs.show();
        $('tr.critical').toggle($(this).hasClass('active'));
        packageContentDivs.each(function() {
            var rowCount = $(this).find('tbody tr:visible').length;

            $(this).find('.badge-info').html(rowCount);
            $(this).toggle(rowCount > 0);
        });
    });
    $('.type-filter button.error').click(function(){
        packageContentDivs = $('.package-contents');
        packageContentDivs.show();
        $('tr.error').toggle($(this).hasClass('active'));
        packageContentDivs.each(function() {
            var rowCount = $(this).find('tbody tr:visible').length;

            $(this).find('.badge-info').html(rowCount);
            $(this).toggle(rowCount > 0);
        });
    });
    $('.type-filter button.notice').click(function(){
        packageContentDivs = $('.package-contents');
        packageContentDivs.show();
        $('tr.notice').toggle($(this).hasClass('active'));
        packageContentDivs.each(function() {
            var rowCount = $(this).find('tbody tr:visible').length;

            $(this).find('.badge-info').html(rowCount);
            $(this).toggle(rowCount > 0);
        });
    });

    $('.view button.details').click(function(){
        $('.side-nav li.view-simple').removeClass('view-simple');
    }).button('toggle').click();

    $('.view button.simple').click(function(){
        $('.side-nav li').addClass('view-simple');
    });
    
    $('ul.side-nav.nav.nav-list li.nav-header span.side-nav-header').click(function(){
        $(this).siblings('ul').collapse('toggle');
    });

// sorting example
//    $('ol li').sort(
//        function(a, b) { return a.innerHTML.toLowerCase() > b.innerHTML.toLowerCase() ? 1 : -1; }
//    ).appendTo('ol');
}

$(document).ready(function() {
    prettyPrint();

    initializeContents();

    // do not show tooltips on iPad; it will cause the user having to click twice
    if(!$.browser.ipad) {
        $(".side-nav a").tooltip({'placement': 'top'});
    }

    // chrome cannot deal with certain situations; warn the user about reduced features
    if ($.browser.chrome && (window.location.protocol == 'file:')) {
        $("body > .container").prepend(
            '<div class="alert alert-error"><a class="close" data-dismiss="alert">×</a>' +
            'You are using Google Chrome in a local environment; AJAX interaction has been ' +
            'disabled because Chrome cannot <a href="http://code.google.com/p/chromium/issues/detail?id=40787">' +
            'retrieve files using Ajax</a>.</div>'
        );
    }

    $('ul.nav-namespaces li a, ul.nav-packages li a').click(function(){
        // Google Chrome does not do Ajax locally
        if ($.browser.chrome && (window.location.protocol == 'file:'))
        {
            return true;
        }

        $(this).parents('.side-nav').find('.active').removeClass('active');
        $(this).parent().addClass('active');
        $('div.namespace-contents').load(
            this.href + ' div.namespace-contents', function(){
                initializeContents();
                $(window).scrollTop($('div.namespace-contents').position().top);
            }
        );
        $('div.package-contents').load(
            this.href + ' div.package-contents', function(){
                initializeContents();
                $(window).scrollTop($('div.package-contents').position().top);
            }
        );

        return false;
    });

    function filterPath(string)
    {
        return string
            .replace(/^\//, '')
            .replace(/(index|default).[a-zA-Z]{3,4}$/, '')
            .replace(/\/$/, '');
    }

    var locationPath = filterPath(location.pathname);

    // the ipad already smoothly scrolls and does not detect the scrollable
    // element if top=0; as such we disable this behaviour for the iPad
    if (!$.browser.ipad) {
        $('a[href*=#]').each(function ()
        {
            var thisPath = filterPath(this.pathname) || locationPath;
            if (locationPath == thisPath && (location.hostname == this.hostname || !this.hostname) && this.hash.replace(/#/, ''))
            {
                var target = decodeURIComponent(this.hash.replace(/#/,''));
                // note: I'm using attribute selector, because id selector can't match elements with '$' 
                var $target = $('[id="'+target+'"]');

                if ($target.length > 0)
                {
                    $(this).click(function (event)
                    {
                        var scrollElem = scrollableElement('html', 'body');
                        var targetOffset = $target.offset().top;

                        event.preventDefault();
                        $(scrollElem).animate({scrollTop:targetOffset}, 400, function ()
                        {
                            location.hash = target;
                        });
                    });
                }
            }
        });
    }

    // use the first element that is "scrollable"
    function scrollableElement(els)
    {
        for (var i = 0, argLength = arguments.length; i < argLength; i++)
        {
            var el = arguments[i], $scrollElement = $(el);
            if ($scrollElement.scrollTop() > 0)
            {
                return el;
            }
            else
            {
                $scrollElement.scrollTop(1);
                var isScrollable = $scrollElement.scrollTop() > 0;
                $scrollElement.scrollTop(0);
                if (isScrollable)
                {
                    return el;
                }
            }
        }
        return [];
    }

    // Hide API Documentation menu if it's empty
    $('.nav .dropdown a[href=#api]').next().filter(function(el) {
        if ($(el).children().length == 0) {
            return true;
        }
    }).parent().hide();
    
    
    
	var all=Cookies.getJSON('activeAccordionGroup');
	if (all!=null) {
		for(href in all){
			$("a[href=\""+all[href]+'"]').parents('div.accordion-body:first').collapse("show");
		}
	}
	
	//when a group is shown, save it as the active accordion group
	$(".accordion:first").on('shown.bs.collapse', function(e) {
		var all = Cookies.getJSON('activeAccordionGroup');
		if(all == null){all = {};}
		var href = $('.accordion-heading a[href]:first',e.target).attr('href');
		if(href==undefined){
			href = $('.accordion-heading a[href]:first',e.target.parentNode).attr('href');
		}
		if(href != undefined){
			all[href.replace(/\-\.\//gi,'')] = href;
			Cookies.set('activeAccordionGroup', all);
		}
	}).on('hidden.bs.collapse',function(e){
		var all = Cookies.getJSON('activeAccordionGroup');
		if(all == null){all = {};}
		var href = $('.accordion-heading a[href]:first',e.target).attr('href');
		if(href==undefined){
			href = $('.accordion-heading a[href]:first',e.target.parentNode).attr('href');
		}
		if(href != undefined){
			href = href.replace(/\-\.\//gi,'');
			if(all[href] != undefined){
				delete all[href];
			}
			Cookies.set('activeAccordionGroup', all);
		}
	}) ;
});
