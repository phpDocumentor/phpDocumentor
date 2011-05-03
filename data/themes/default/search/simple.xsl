<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template name="search">
    <xsl:param name="root" />

    <script type="text/javascript">
      $(function() {

        var is_chrome = /chrome/.test( navigator.userAgent.toLowerCase() );
        var is_local = /file:\/\//.test(document.location.href);
        if (is_chrome &amp;&amp; is_local)
        {
          // search is disabled on chrome with local files due to http://code.google.com/p/chromium/issues/detail?id=40787
          return;
        }

        $("#search_box").show();
        $("#search_box_icon").show();
        $("#search_box_clear").show();
        var search_index = {};
        $.ajax({
          url: "<xsl:value-of select="$root" />search_index.xml",
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
                value: $("value", this).text(),
                label: '<img src="{$root}images/icons/'+type+'.png" align="absmiddle" />'+$("value", this).text(),
                id: $("id", this).text(),
              };
            }).get();

            $("#search_box").autocomplete({
              source: search_index,
              select: function(event, ui) {
                // redirect to the documentation
                if ((parent) &amp;&amp; (parent.content))
                {
                  parent.content.document.location = '<xsl:value-of select="$root" />'+ui.item.id;
                }
                else
                {
                  jQuery(document).location = '<xsl:value-of select="$root" />'+ui.item.id;
                }
              }
            });
          }
        });
      });
    </script>

    <img src="{$root}images/search.gif" id="search_box_icon" border="0" onclick="$('#search_box').focus()"/>
    <input id="search_box" />
    <img src="{$root}images/clear_left.png" id="search_box_clear" border="0" onclick="$('#search_box').val('')"/>
  </xsl:template>

</xsl:stylesheet>