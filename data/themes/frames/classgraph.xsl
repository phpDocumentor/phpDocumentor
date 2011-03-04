<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project" name="frames_classgraph">
    <div id="content">
      <h1>Class Diagram</h1>
      <a href="classes.svg">Click here to view the full version</a><br />
      <embed src="classes.svg" width="100%" height="92%" />
    </div>
  </xsl:template>

</xsl:stylesheet>