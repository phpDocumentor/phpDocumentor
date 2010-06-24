<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Docs for file
          <xsl:value-of select="@path" />
        </title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="./css/black-tie/jquery-ui-1.7.3.custom.css" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery-ui-1.7.2.custom.min.js"></script>
      </head>
      <body>

        <xsl:apply-templates select="/project/header" />
        <xsl:apply-templates select="/project/class[name=$class]" />

      </body>
    </html>

  </xsl:template>

  <xsl:template match="class">
    <div class="sidebar" style="width: 250px; margin-right: 10px; border-right: 1px solid silver; overflow: auto; font-size: 0.8em; float: left;">
      <xsl:for-each select="method">
        <a href="#{../name}::{name}"><xsl:value-of select="name" /></a><br />
      </xsl:for-each>
    </div>

    <div style="margin-left: 260px">
      <h2>Description</h2>
      <strong><xsl:value-of select="docblock/description" /></strong>
      <br />
      <xsl:value-of disable-output-escaping="yes" select="docblock/long-description" /><br />
      <xsl:for-each select="docblock/tag">
        <xsl:value-of select="@name" />:
        <xsl:value-of select="." />
        <br />
      </xsl:for-each>

      <h2>Methods</h2>
      <xsl:for-each select="method">
        <xsl:apply-templates select="." />
      </xsl:for-each>
    </div>
  </xsl:template>

  <xsl:template match="method|function">
    <a id="{../name}::{name}" />
    <h3>
      <span style="color: silver;">
        <xsl:if test="not(docblock/tag[@name='return']/@type)">n/a</xsl:if><xsl:value-of select="docblock/tag[@name='return']/@type" />
      </span>
      &#160;<xsl:value-of select="name" />
      <span style="color: silver;">(
        <xsl:for-each select="argument">
          <xsl:variable name="variable_name" select="name" />
          <xsl:value-of select="../docblock/tag[@name='param' and @variable=$variable_name]/@type" />&#160;<xsl:value-of select="$variable_name" /> =
          <xsl:value-of select="default" disable-output-escaping="yes"/>,
        </xsl:for-each>
        )
      </span>
    </h3>
    <em><xsl:value-of select="docblock/description" /></em><br />
    <xsl:if test="docblock/long-description"><small><xsl:value-of select="docblock/long-description" /></small><br /></xsl:if>
    <br />
    <hr />
  </xsl:template>

</xsl:stylesheet>