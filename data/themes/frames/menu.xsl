<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />
  <xsl:include href="../old_ocean/menu.xsl" />

  <xsl:template match="/project" name="frames_menu">
    <xsl:call-template name="menu" />
  </xsl:template>

</xsl:stylesheet>