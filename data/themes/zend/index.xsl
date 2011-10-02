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
      <body>

        <table id="page">
          <tr><td height="51">
            <div id="top">
              <div class="top">
                <h1 class="logo">
                  <a href="http://framework.zend.com/" title="ZF Zend Framework">ZF Zend Framework</a>
                </h1>
                  <ul id="top-nav" class="top-nav">
                      <li><a href="graph.html" target="content">Class diagram</a></li>
                      <li><a href="markers.html" target="content">Todo / Fixme</a></li>
                      <li><a href="parse_markers.html" target="content">Errors</a></li>
                  </ul>
              </div>
            </div>
          </td></tr>
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