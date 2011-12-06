<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project">
    <style>
      body
      {
        background: black url('images/top.png') repeat-x top left;
      }
    </style>

    <div id="header">
      <h1>
        <xsl:if test="//@title != ''">
          <xsl:value-of select="//@title" disable-output-escaping="yes" />
        </xsl:if>
        <xsl:if test="//@title = ''">
          <img src="{$root}images/logo.png" />
        </xsl:if>
        <img src="{$root}images/top-stopper.png" /></h1>
    </div>

  </xsl:template>

</xsl:stylesheet>