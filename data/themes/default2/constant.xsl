<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="constant">
    <h3 class="constant"><xsl:value-of select="name"/></h3>
    <div class="constant">
      <p>
        <code><xsl:value-of select="docblock/tag[@name='var']/type"/> <xsl:value-of select="name" /> = '<xsl:value-of select="value"/>'</code>
      </p>

      <xsl:apply-templates select="docblock/description"/>
      <xsl:apply-templates select="docblock/long-description"/>

      <dl class="constant-info">
        <dt>value</dt>
        <dd><xsl:value-of select="value" /></dd>
      <xsl:apply-templates select="docblock/tag">
        <xsl:sort select="@name" />
      </xsl:apply-templates>
      </dl>
    </div>
  </xsl:template>

</xsl:stylesheet>