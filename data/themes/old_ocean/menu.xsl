<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/project" name="menu">
    <ul id="menu">
      <li>
        <a href="{$root}graph.html">Class diagram</a>
      </li>
      <!--<li>Packages</li>-->
      <!--<li>Files</li>-->
      <li>
        <a href="{$root}markers.html">TODO / Markers</a>
      </li>
      <li>
        <a href="{$root}parse_markers.html">Errors</a>
      </li>
    </ul>
    <!--<h4>Charts</h4>-->
  </xsl:template>

</xsl:stylesheet>