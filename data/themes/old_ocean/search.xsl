<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template name="search">
    <xsl:param name="search_template" />
    <xsl:param name="root" />

    <xsl:choose>
      <xsl:when test="$search_template='xmljs'">
        <xsl:call-template name="search_xmljs">
          <xsl:with-param name="root" select="$root" />
        </xsl:call-template>
      </xsl:when>
      <xsl:when test="$search_template='ajax'">
        <xsl:call-template name="search_ajax">
          <xsl:with-param name="root" select="$root" />
        </xsl:call-template>
      </xsl:when>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="search_xmljs">
    <xsl:param name="root" />

    <script type="text/javascript">
      $(function() {

        var is_chrome = /chrome/.test( navigator.userAgent.toLowerCase() );
        var is_local = /file:\/\//.test(document.location.href);
        if (is_chrome &amp;&amp; is_local)
        {
          // search is disabled on chrome with local files due to http://code.google.com/p/chromium/issues/detail?id=40787
          return;
        }

        $("#search_box").show();
        var search_index = {};
        $.ajax({
          url: "<xsl:value-of select="$root" />search_index.xml",
          dataType: ($.browser.msie) ? "text" : "xml",
          error: function(data) {
            alert('An error occurred using the search data');
          },
          success: function( data ) {
            var xml;
            if (typeof data == "string") {
              xml = new ActiveXObject("Microsoft.XMLDOM");
              xml.async = false;
              xml.loadXML(data);
            } else {
              xml = data;
            }

            search_index = $("node", xml).map(function() {
              type = $("type", this).text();
              return {
                value: $("value", this).text(),
                label: '<img src="{$root}images/icons/'+type+'.png" align="absmiddle" />'+$("value", this).text(),
                id: $("id", this).text(),
              };
            }).get();

            $("#search_box").autocomplete({
              source: search_index,
              select: function(event, ui) {
                // redirect to the documentation
                if ((parent) &amp;&amp; (parent.content))
                {
                  parent.content.document.location = '<xsl:value-of select="$root" />'+ui.item.id;
                }
                else
                {
                  jQuery(document).location = '<xsl:value-of select="$root" />'+ui.item.id;
                }
                applySearchHash();
              }
            });
          }
        });
      });
    </script>
  </xsl:template>

  <xsl:template name="search_ajax">
    <xsl:param name="root" />

    <script type="text/javascript">
      $(function() {
        $("#search_box").show();
        $("#search_box").autocomplete({
          source: "<xsl:value-of select="$root" />ajax_search.php",
          minLength: 2,
          select: function(event, ui) {
            $(location).attr('href', '<xsl:value-of select="$root" />'+ui.item.id);
            applySearchHash();
          }
        });
      });
    </script>
  </xsl:template>

</xsl:stylesheet>