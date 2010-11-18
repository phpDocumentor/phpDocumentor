<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="{$root}/css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}/css/default.css" type="text/css" />
        <script type="text/javascript" src="{$root}/js/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="{$root}/js/jquery-ui-1.8.2.custom.min.js"></script>
      </head>
      <body>
        <script type="text/javascript">
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

        <xsl:call-template name="header">
          <xsl:with-param name="title" select="$title" />
        </xsl:call-template>

        <xsl:apply-templates />

      </body>
    </html>

  </xsl:template>


  <xsl:template name="header">
    <xsl:param name="title" />

    <script type="text/javascript">
      $(function() {
        var search_index = {};
        $.ajax({
          url: "<xsl:value-of select="$root" />/search_index.xml",
          dataType: "xml",
          error: function(data) {
            console.debug('an error occurred, ');
            console.debug(data);
          },
          success: function( data ) {
            search_index = $("node", data).map(function() {
              type = $("type", this).text();
              return {
                value: $("value", this).text(),
                label: '<img src="{$root}/images/icons/'+type+'.png" align="absmiddle" />'+$("value", this).text(),
                id: $("id", this).text(),
              };
            }).get();

            $("#search_box").autocomplete({
              source: search_index,
              select: function(event, ui) {
                // redirect to the documentation
                $(location).attr('href', '<xsl:value-of select="$root" />/'+ui.item.id);
                applySearchHash();
              }
            });
          }
        });
      });
    </script>

    <div id="nb-header">
      <xsl:value-of select="$title" />

      <div class="ui-widget" style="display: inline; float: right; font-size: 0.5em; margin-top: 8px;">
        <label for="search_box">Search</label>
        <input id="search_box" />
      </div>
    </div>
  </xsl:template>

  <xsl:template name="footer">

  </xsl:template>

</xsl:stylesheet>