<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template name="search">
    <xsl:param name="root" />

    <script type="text/javascript">
        $(function() {
            $("#search_box_icon, #search_box_clear").show();
            $("#search_box").show().autocomplete({
                source: "<xsl:value-of select="$root"/>ajax_search.php",
                minLength: 2,
                select: function (event, ui) {
                    if ((parent) &amp;&amp; (parent.content)) {
                        parent.content.document.location = '' + ui.item.id;
                    } else {
                        jQuery(document).location = '' + ui.item.id;
                    }
                }
            });
        });
    </script>

    <img src="{$root}images/search.gif" id="search_box_icon" border="0" onclick="$('#search_box').focus()" />
    <input id="search_box" />
    <img src="{$root}images/clear_left.png" id="search_box_clear" border="0" onclick="$('#search_box').val('')" />
  </xsl:template>

</xsl:stylesheet>