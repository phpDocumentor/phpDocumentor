<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
        <title>
            <xsl:choose>
                <xsl:when test="$title != ''"><xsl:value-of select="$title" /></xsl:when>
                <xsl:otherwise>DocBlox Documentation</xsl:otherwise>
            </xsl:choose>
        </title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.splitter.js"></script>
          <script type="text/javascript">
              $().ready(function() {
                  // to make the page work without JS we need to set a margin-left;
                  // this distorts the splitter plugin and thus we set margin
                  // to 0 when JS is enabled
                  $("#contents").attr('style', 'margin: 0px;');

                  $(".resizable").splitter({
                      sizeLeft: 250
                  });

              });
          </script>
      </head>
      <body class="chrome">
        <table id="page">
          <tr>
            <td id="db-header">
              <h1>
                <xsl:if test="/project/@title != ''">
                  <span><xsl:value-of select="/project/@title" disable-output-escaping="yes" /></span>
                 </xsl:if>
                <xsl:if test="/project/@title = ''">
                  <div class="docblox"><img src="{$root}images/logo.png" /></div>
                </xsl:if>
              </h1>

              <xsl:call-template name="search"/>
              <div id="menubar">
                <xsl:call-template name="menubar" />
              </div>
            </td>
          </tr>
          <tr>
            <td class="resizable">
                <div id="sidebar">
                    <iframe name="nav" id="nav" src="{$root}nav.html" frameBorder="0"></iframe>
                </div>
                <div id="contents">
                    <iframe name="content" id="content" src="{$root}content.html" frameBorder="0"></iframe>
                </div>
            </td>
          </tr>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>