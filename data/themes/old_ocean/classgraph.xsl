<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template match="/project">
    <div id="content">
      <table width="100%" height="95%">
      <tr><td height="60px">
        <div style="font-size: 10px; white-space: normal;">
          The following actions are supported in this diagram:
          <ul>
            <li><b>Zoom</b>, you can use the scrollwheel to zoom in or out</li>
            <li><b>Move</b>, you can move around by dragging the Diagram</li>
            <li><b>Go to class</b>, you can view the details of a class by clicking on it</li>
          </ul>
        </div>
      </td></tr>
      <tr><td>
        <embed src="classes.svg" width="100%" height="100%" />
      </td></tr>
      </table>
    </div>
  </xsl:template>

</xsl:stylesheet>