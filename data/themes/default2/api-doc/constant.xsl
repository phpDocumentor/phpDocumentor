<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="class/constant/name">
    <h4 class="constant">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#{../../name}">jump to class</a></div>
    </h4>
  </xsl:template>

  <xsl:template match="file/constant/name">
    <h3 class="constant">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#top">jump to top</a></div>
    </h3>
  </xsl:template>

  <xsl:template match="constant">
    <a id="{../full_name}::{name}" />
    <xsl:apply-templates select="name"/>

    <div class="constant">
      <code>
        <xsl:value-of select="docblock/tag[@name='var']/type"/>&#160;<span class="highlight"><xsl:value-of select="name" /></span> = '<xsl:value-of select="value"/>'
      </code>

      <xsl:apply-templates select="docblock/description"/>
      <xsl:apply-templates select="docblock/long-description"/>

      <div class="api-section">
        <h4 class="info"><img src="{$root}images/arrow_right.gif" /> Details</h4>
        <dl class="constant-info">
          <dt>value</dt>
          <dd><xsl:value-of select="value" /></dd>
        <xsl:apply-templates select="docblock/tag">
          <xsl:sort select="@name" />
        </xsl:apply-templates>
        </dl>
        <div class="clear"></div>
      </div>
    </div>
  </xsl:template>

</xsl:stylesheet>