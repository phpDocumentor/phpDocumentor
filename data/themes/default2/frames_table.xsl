<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
        <title>
          <xsl:value-of select="$title" />
        </title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
      </head>
      <body class="chrome">
        <table id="page">
          <tr>
            <td colspan="2" id="header">
              <h1>

                <xsl:if test="/project/@title != ''">
                  <xsl:value-of select="/project/@title" disable-output-escaping="yes" />
                </xsl:if>
                <xsl:if test="/project/@title = ''">
                  <img src="{$root}images/logo.png" />
                </xsl:if>

              </h1>
            </td>
          </tr>
          <tr>
            <td colspan="2" id="menubar"><xsl:call-template name="menubar"/></td>
          </tr>
          <tr>
            <td id="sidebar">
              <xsl:call-template name="search" />
              <iframe name="nav" id="nav" src="{$root}nav.html"></iframe>
            </td>
            <td id="contents">
              <iframe name="content" id="content" src="{$root}content.html"></iframe>
            </td>
          </tr>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>