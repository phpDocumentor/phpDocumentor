<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project">
    <div class="nb-right-of-sidebar">
      <a href="classes.svg">Click here to view the full version</a><br />
      <embed src="classes.svg" width="100%" height="92%" />
    </div>
  </xsl:template>

</xsl:stylesheet>