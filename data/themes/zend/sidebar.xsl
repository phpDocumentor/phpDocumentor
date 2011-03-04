<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="../frames/sidebar.xsl" />

  <xsl:template match="/project">
    <div class="section">
      <h1>Pages</h1>
      <ul class="filetree">
        <li>
          <span class="file"><a href="{$root}graph.html" target="content">Class diagram</a></span>
        </li>
        <li>
          <span class="file"><a href="{$root}markers.html" target="content">TODO / Markers</a></span>
        </li>
      </ul>

    </div>

    <xsl:call-template name="frames_sidebar" />
  </xsl:template>

</xsl:stylesheet>