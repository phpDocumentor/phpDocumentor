<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="themes/default/chrome.xsl" />

  <xsl:template match="/project">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Class overview</title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="css/black-tie/jquery-ui-1.7.3.custom.css" type="text/css" />
        <link rel="stylesheet" href="css/default.css" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery-ui-1.7.2.custom.min.js"></script>
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

        <div class="nb-sidebar">
          <xsl:apply-templates select="/project/file/class">
            <xsl:sort select="name" />
          </xsl:apply-templates>
        </div>

        <div class="nb-right-of-sidebar">
          <a href="classes.svg">Click here to view the full version</a><br />
          <embed src="classes.svg" width="700" />
        </div>
      </body>
    </html>

  </xsl:template>

  <xsl:template match="class">
    <a href="{../@generated-path}#{name}"><xsl:value-of select="name" /></a><br />
  </xsl:template>

</xsl:stylesheet>