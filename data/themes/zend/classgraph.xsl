<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="../frames/classgraph.xsl" />
  <xsl:include href="page_header.xsl" />

  <xsl:template match="/project">
      <xsl:call-template name="page_header" />
      <xsl:call-template name="frames_classgraph" />
  </xsl:template>

</xsl:stylesheet>