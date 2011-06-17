<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/jquery.treeview.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/default.css" type="text/css" />
        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.6.1.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
        <base target="content" />
      </head>
      <body>
        <script type="text/javascript">
          $(document).ready(function()
          {
            $(".filetree").treeview();
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

        <xsl:apply-templates />

      </body>
    </html>

  </xsl:template>

</xsl:stylesheet>