<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="property">
    <a id="{../full_name}::{name}" />
    <h4 class="property">
      <xsl:value-of select="name" />
      <div class="to-top"><a href="#{../full_name}">jump to class</a></div>
    </h4>

    <div class="property">
      <code>
        <xsl:value-of select="docblock/tag[@name='var']/type" />&#160;<span class="highlight"><xsl:value-of select="name" />
      </span><xsl:if test="default"> = '<xsl:value-of select="default" />'</xsl:if>
      </code>

      <xsl:apply-templates select="docblock/description" />
      <xsl:apply-templates select="docblock/long-description" />

      <div class="api-section">
        <h4 class="info"><img src="{$root}images/arrow_right.gif" /> Details</h4>
        <dl class="property-info">
          <xsl:apply-templates select="docblock/tag[@name='var']">
            <xsl:sort select="@name" />
          </xsl:apply-templates>
          <dt>visibility</dt>
          <dd><xsl:value-of select="@visibility" /></dd>
          <dt>default</dt>
          <dd><xsl:value-of select="default" /></dd>
          <dt>final</dt>
          <dd><xsl:value-of select="@final" /></dd>
          <dt>static</dt>
          <dd><xsl:value-of select="@static" /></dd>
          <xsl:apply-templates select="docblock/tag[@name!='var']">
            <xsl:sort select="@name" />
          </xsl:apply-templates>
        </dl>
        <div class="clear"></div>
      </div>
    </div>
  </xsl:template>

</xsl:stylesheet>