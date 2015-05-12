<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="menu.xsl" />
  <xsl:include href="search.xsl" />

  <!-- Concatenate items with a given separator: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
  <xsl:template name="implode">
    <xsl:param name="items" />
    <xsl:param name="separator" select="', '" />

    <xsl:for-each select="$items">
      <xsl:if test="position() &gt; 1">
        <xsl:value-of select="$separator" />
      </xsl:if>

      <xsl:value-of select="." />
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/jquery.treeview.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/default.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
      </head>
      <body>
        <script type="text/javascript">
          $(document).ready(function()
          {
            $(".filetree").treeview({
              animated: "fast",
              collapsed: true,
              persist: "cookie"
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
            <h1>
              <xsl:if test="//@title != ''">
                <xsl:value-of select="//@title" disable-output-escaping="yes" />
              </xsl:if>
              <xsl:if test="//@title = ''">
                phpDocumentor
              </xsl:if>
              <img src="{$root}images/top-stopper.png" />
            </h1>
          </div>

          <xsl:call-template name="menu" />

          <div id="content_container">
            <xsl:apply-templates />
          </div>

          <div id="index">
            <div class="padder">
              <xsl:call-template name="search">
                <xsl:with-param name="root" select="$root" />
                <xsl:with-param name="search_template" select="$search_template" />
              </xsl:call-template>
              <input id="search_box" />
              <xsl:if test="$object-index">
              <xsl:value-of select="$object-index" disable-output-escaping="yes"/>
              </xsl:if>
            </div>
          </div>

          <div id="footer">
            <div class="padder"></div>
          </div>

        </div>

      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>