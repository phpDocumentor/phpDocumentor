<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="themes/default/chrome.xsl" />
  <xsl:include href="class.xsl" />

  <xsl:template match="/project">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Docs for file
          <xsl:value-of select="@path" />
        </title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="../css/black-tie/jquery-ui-1.7.3.custom.css" type="text/css" />
        <link rel="stylesheet" href="../css/default.css" type="text/css" />
        <script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="../js/plugins/jquery-ui-1.7.2.custom.min.js"></script>
      </head>
      <body>
        <script type="text/javascript">
          jQuery(function()
          {
            jQuery(".tabs").tabs();
          });
        </script>

        <xsl:call-template name="header">
          <xsl:with-param name="title" select="$title"/>
        </xsl:call-template>

        <xsl:apply-templates select="/project/file[@path=$file]" />

      </body>
    </html>

  </xsl:template>

  <xsl:template match="/project/file">
    <div class="tabs">
      <ul>
        <xsl:if test="function">
        <li><a href="#file_functions">Functions</a></li>
        </xsl:if>
        <xsl:if test="constant">
        <li><a href="#file_constants">Constants</a></li>
        </xsl:if>
        <xsl:for-each select="class">
        <li><a href="#{name}"><xsl:value-of select="name" /></a></li>
        </xsl:for-each>
      </ul>

      <xsl:if test="function">
      <div id="file_functions">
        <div class="nb-sidebar">
          <xsl:for-each select="function">
            <a href="#{../name}::{name}()">
              <xsl:value-of select="name" />
            </a>
            <br />
          </xsl:for-each>
        </div>

        <div class="nb-right-of-sidebar">
          <h2>Functions</h2>
          <xsl:for-each select="function">
            <xsl:apply-templates select="." />
          </xsl:for-each>
        </div>
      </div>
      </xsl:if>

      <xsl:if test="constant">
      <div id="file_constants">
        <h2>Constants</h2>
        <xsl:for-each select="constant">
          <xsl:apply-templates select="." />
        </xsl:for-each>
      </div>
      </xsl:if>

      <xsl:for-each select="class">
      <div id="{name}">
        <xsl:apply-templates select="."/>
      </div>
      </xsl:for-each>
    </div>
  </xsl:template>

</xsl:stylesheet>