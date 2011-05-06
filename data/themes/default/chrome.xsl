<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <!-- Concatenate items with a given separator: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
  <xsl:template name="implode">
    <xsl:param name="items" />
    <xsl:param name="separator" select="', '" />

    <xsl:for-each select="$items">
      <xsl:if test="position() &gt; 1">
        <xsl:value-of select="$separator" />
      </xsl:if>

      <xsl:value-of select="." />
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/jquery.treeview.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $(".filetree").treeview({
                    collapsed: true,
                    persist: "cookie"
                });

                $("#accordion").accordion({
                    collapsible: true,
                    autoHeight:  false,
                    fillSpace:   true
                });

                $(".tabs").tabs();
            });
        </script>
      </head>
      <body>

        <xsl:apply-templates />

      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>