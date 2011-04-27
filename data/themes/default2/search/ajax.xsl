<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template name="search">
    <xsl:param name="root" />

    <script type="text/javascript">
      $(function() {
        $("#search_box_icon").show();
        $("#search_box").show();
        $("#search_box_clear").show();
        $("#search_box").autocomplete({
          source: "<xsl:value-of select="$root" />ajax_search.php",
          minLength: 2,
          select: function(event, ui) {
            $(location).attr('href', '<xsl:value-of select="$root" />'+ui.item.id);
          }
        });
      });
    </script>

    <img src="{$root}images/search.gif" id="search_box_icon" border="0" onclick="$('#search_box').focus()" style="display: none; position: absolute; top: 5px;" />
    <input id="search_box" style="display: none; margin: 0px 20px; height: 2em; border: 1px solid silver; border-radius: 5px; -moz-border-radius: 5px; padding-right: 20px;" />
    <img src="{$root}images/clear_left.png" id="search_box_clear" border="0" onclick="$('#search_box').val('')" style="position: absolute; right: 3px; top: 5px; display: none; cursor: pointer" />
  </xsl:template>

</xsl:stylesheet>