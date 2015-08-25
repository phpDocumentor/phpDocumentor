jQuery(document).ready(function($) {
	var tab = getCookie("navMod");
	if (!tab){
		tab = defaultTab;
	}
	setTab(tab);

	$('.navmod a').click(function(event) {
		event.preventDefault();
		event.stopPropagation();
		setTab($(this).attr('href'));
		return false;
	});
	function setTab(tab){
		$('#menuPackage, #menuNamespace').css('display', 'none');
		$(tab).css('display', 'block');
		setCookie("navMod", tab, 365);
	}

	$.ajax({
		url: searchXML,
		dataType: "xml",
		success: function( xmlResponse ) {
			var data = $( "element", xmlResponse ).map(function() {
				return {
					id: $( "url", this ).text(),
					text: $( "name", this ).text(),
					label: $( "label", this ).text()
				};
			}).get();

			$(".search").select2({
				data: data,
				placeholder: "Search",
				templateResult: formatState
			});
		}
	});
	$(".search").on("change", function (e) {
		var arr = [];
		for (var i=0; i < $(this).val().length; i++) {
			if($(this).val()[i]!=""){
				arr.push($(this).val()[i]);
			}
		};
		if(arr.length>0){
			document.location.href = searchXML.replace("search.xml", "") + arr[0];
			$(this).val(null).trigger("change");
		}
	});
	function formatState (node) {
		if (!node.id) { return node.text; }
		var $node = $(node.label);
		return $node;
	};
});

function setCookie(name,value,days){if(days){var date=new Date();date.setTime(date.getTime()+(days*86400000));var expires=';expires='+date.toGMTString()}else{var expires=''};document.cookie=name+'='+value+expires+';path=/;'}
function getCookie(name){var nameEQ=name+'=';var ca=document.cookie.split(';');for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' ')c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0)return c.substring(nameEQ.length, c.length)};return null}