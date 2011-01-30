<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />
  <xsl:include href="class.xsl" />

  <xsl:template match="/project">
    <xsl:apply-templates select="/project/file[@path=$file]" />
  </xsl:template>

  <xsl:template match="/project/file">
    <h1><xsl:value-of select="@path" /></h1>
    <xsl:if test="docblock/tag">
    <div class="properties">
      <h1>Properties</h1>
      <xsl:for-each select="docblock/tag">
        <xsl:sort select="@name" />
        <label class="property-key"><xsl:value-of select="@name" /></label>
        <div class="property-value"><a title="{.}" href="{@link}"><xsl:value-of select="." /></a></div>
      </xsl:for-each>
    </div>
    </xsl:if>

    <xsl:if test="docblock/description != '' or docblock/long-description != ''">
      <h3>Description</h3>
    </xsl:if>
    <xsl:if test="docblock/description != ''">
      <xsl:value-of select="docblock/description" /><br />
      <br />
    </xsl:if>
    <xsl:if test="docblock/long-description != ''">
      <xsl:value-of select="docblock/long-description" disable-output-escaping="yes"/><br />
    </xsl:if>

    <xsl:if test="include">
      <h4>Includes</h4>
      <xsl:for-each select="include">
        <xsl:value-of select="name" />&#160;<span class="nb-faded-text">(<xsl:value-of select="@type" />)</span><br />
      </xsl:for-each>
    </xsl:if>

    <xsl:if test="function">
    <h4>Functions</h4>
      <xsl:for-each select="function">
        <xsl:apply-templates select="." />
      </xsl:for-each>
    </xsl:if>

    <xsl:if test="constant">
    <h4>Constants</h4>
      <xsl:for-each select="constant"><xsl:apply-templates select="." /></xsl:for-each>
    </xsl:if>

    <xsl:if test="class">
      <h2>Classes</h2>
      <xsl:for-each select="class">
        <div id="{name}">
          <xsl:apply-templates select="." />
        </div>
      </xsl:for-each>
    </xsl:if>

    <xsl:if test="interface">
      <h2>Interfaces</h2>
      <xsl:for-each select="interface">
        <div id="{name}">
          <xsl:apply-templates select="." />
        </div>
      </xsl:for-each>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>