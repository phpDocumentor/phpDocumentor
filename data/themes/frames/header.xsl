<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/">
    <style>
      body
      {
        background: black url('images/top.png') repeat-x top left;
      }
    </style>

<!--
    <div id="search-box">
      <xsl:call-template name="search">
        <xsl:with-param name="search_template" select="$search_template" />
        <xsl:with-param name="root" select="$root" />
      </xsl:call-template>
      <input id="search_box" style="display: none" />
    </div>
-->
  </xsl:template>

</xsl:stylesheet>