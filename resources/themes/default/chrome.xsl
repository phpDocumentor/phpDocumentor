<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="search.xsl" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}/css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}/css/jquery.treeview.css" type="text/css" />
        <link rel="stylesheet" href="{$root}/css/default.css" type="text/css" />
        <script type="text/javascript" src="{$root}/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}/js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}/js/jquery.treeview.js"></script>
      </head>
      <body>
        <script type="text/javascript">
          $(document).ready(function()
          {
            $(".treeview-docblox").treeview({
              animated: 'fast'
            });
          });

          function jq_escape(myid)
          {
            return '#' + myid.replace(/(#|\$|:|\.|\(|\))/g, '\\$1');
          }

          function applySearchHash()
          {
            hashes = document.location.hash.substr(1, document.location.hash.length);
            if (hashes != "")
            {
              hashes = hashes.split('/');
              $.each(hashes, function(index, hash)
              {
                node = $(jq_escape(hash));
                switch (node[0].nodeName)
                {
                  case 'DIV':
                    tabs = node.parents('.tabs');
                    $(tabs[0]).tabs('select', '#' + hash)
                    break;
                  case 'A':
                    window.scrollTo(0, node.offset().top);
                    break;
                }
              });
            }
          }

          jQuery(function()
          {
            jQuery(".tabs").tabs();
            applySearchHash();
          });
        </script>

        <div id="maincontainer">

          <div id="header">
            <div class="padder"></div>
          </div>

          <div id="content_container">
            <div id="content">
              <div class="padder">
                <xsl:apply-templates />
              </div>
            </div>
          </div>

          <div id="menu">
            <div class="padder">
              <h1><img src="{$root}/images/logo.png" alt="" /></h1>

              <div id="search-box">
                <input type="text" name="search" id="search" />
              </div>
              <h4>Documentation</h4>
                <ul>
                  <li>Namespaces</li>
                  <li>Packages</li>
                  <li>Files</li>
                  <li><a href="{$root}/markers.html">TODO / Markers</a></li>
              </ul>
              <br />
              <h4>Charts</h4>
            </div>
          </div>

          <div id="index">
            <div class="padder">

              <xsl:value-of select="$object-index" disable-output-escaping="yes"/>

            </div>
          </div>

          <div id="footer">
            <div class="padder"></div>
          </div>

        </div>

      </body>
    </html>

  </xsl:template>


  <xsl:template name="header">
    <xsl:param name="title" />

    <xsl:call-template name="search">
      <xsl:with-param name="search_template" select="$search_template" />
      <xsl:with-param name="root" select="$root" />
    </xsl:call-template>

    <div id="nb-header">
      <xsl:value-of select="$title" />

      <div class="ui-widget" style="display: inline; float: right; font-size: 0.5em; margin-top: 8px;">
        <label for="search_box">Search</label>
        <input id="search_box" style="display: none"/>
      </div>
    </div>
  </xsl:template>

</xsl:stylesheet>