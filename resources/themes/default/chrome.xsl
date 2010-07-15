<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="{$root}/css/black-tie/jquery-ui-1.7.3.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}/css/default.css" type="text/css" />
        <script type="text/javascript" src="{$root}/js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="{$root}/js/plugins/jquery-ui-1.7.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}/js/plugins/jquery.autocomplete.min.js"></script>
      </head>
      <body>
        <script type="text/javascript">
          jQuery(function()
          {
            jQuery(".tabs").tabs();
          });
        </script>

        <xsl:call-template name="header">
          <xsl:with-param name="title" select="$title" />
        </xsl:call-template>

        <xsl:apply-templates />

      </body>
    </html>

  </xsl:template>


  <xsl:template name="header">
    <xsl:param name="title" />

    <script type="text/javascript">
      $(function() {
        $("#search_box").autocomplete({
          source: ["test", "test2", "class", "function"],//"search_index.json",
          minLength: 0//,
//          select: function(event, ui) {
//            console.debug(ui.item ? ("Selected: " + ui.item.value + ", name: " + ui.item.id) : "Nothing selected, input was " + this.value);
//          }
        });
      });
    </script>

    <div id="nb-header">
      <xsl:value-of select="$title" />

      <div class="ui-widget" style="display: inline; float: right; font-size: 0.5em; margin-top: 8px;">
        <label for="search_box">Search</label>
        <input id="search_box" />
      </div>
    </div>
  </xsl:template>

  <xsl:template name="footer">

  </xsl:template>

</xsl:stylesheet>