<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template name="header">
    <xsl:param name="title" />

    <div id="nb-header"><xsl:value-of select="$title" /></div>
  </xsl:template>

  <xsl:template name="footer">

  </xsl:template>

</xsl:stylesheet>