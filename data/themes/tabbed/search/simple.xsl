<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template name="search">
    <xsl:param name="root" />

    <script type="text/javascript">
      $(function() {
        searchLocal("<xsl:value-of select="$root"/>");
      });
    </script>
    
    <div id="search_box_icon" border="0" onclick="$('#search_container').show();$('#search_box').focus();">
        <span> </span>
    </div>
    <div id="search_container">
        <div class="inner">
            <input id="search_box" />
            <div class="ui-icon ui-icon-close"> </div>
        </div>
        <div id="search_results">
        </div>
    </div>
  </xsl:template>

</xsl:stylesheet>