<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="../default/search/ajax.xsl" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Language" content="en" />
        <title>Zend Framework: API Documentation</title>
        <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/jquery.treeview.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
      </head>
      <body>

        <table id="page">
          <tr><td colspan="2" height="51">
            <div id="top">
              <div class="top">
                <h1 class="logo">
                  <a href="http://framework.zend.com/" title="ZF Zend Framework">ZF Zend Framework</a>
                </h1>
                  <ul id="top-nav" class="top-nav">
                      <li><a href="graph.html" target="content">Class diagram</a></li>
                      <li><a href="markers.html" target="content">Todo / Fixme</a></li>
                  </ul>
              </div>
            </div>
          </td></tr>
          <tr>
            <td id="sidebar">
              <xsl:call-template name="search" />
              <iframe name="nav" id="nav" src="{$root}nav.html" />
            </td>
            <td id="contents">
              <iframe name="content" id="content" src="{$root}content.html" />
            </td>
          </tr>
        </table>

      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>