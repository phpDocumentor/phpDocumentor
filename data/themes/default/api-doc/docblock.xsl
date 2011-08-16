<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- Concatenate items with links with a given separator, based on: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
  <xsl:template name="implodeTypes">
    <xsl:param name="items" />
    <xsl:param name="separator" select="'|'" />

    <xsl:for-each select="$items">
      <xsl:if test="position() &gt; 1">
        <xsl:value-of select="$separator" />
      </xsl:if>

      <xsl:if test="@link">
        <a href="{$root}{@link}">
          <xsl:value-of select="." />
        </a>
      </xsl:if>
      <xsl:if test="not(@link)">
        <xsl:value-of select="." />
      </xsl:if>
    </xsl:for-each>
  </xsl:template>


  <xsl:template match="docblock/description">
    <p class="short-description">
      <xsl:value-of select="." />
    </p>
  </xsl:template>

  <xsl:template match="docblock/long-description">
    <div class="long-description">
      <xsl:value-of select="." disable-output-escaping="yes" />
    </div>
  </xsl:template>

  <xsl:template match="docblock/tag">
    <dt>
      <xsl:value-of select="@name" />
    </dt>
    <dd>
        <xsl:if test="@link">
            <a href="{$root}{@link}">
                <xsl:value-of select="@description" disable-output-escaping="yes"/>
            </a>
        </xsl:if>

        <xsl:if test="not(@link)">
            <xsl:value-of select="@description" disable-output-escaping="yes"/>
        </xsl:if>
        &#160;
    </dd>
  </xsl:template>

  <xsl:template match="docblock/tag[@name='var']|docblock/tag[@name='param']">
    <dt>
      <xsl:if test="@variable">
        <xsl:value-of select="@variable" />
      </xsl:if>
      <xsl:if test="@variable = ''">
        <xsl:value-of select="../../name" />
      </xsl:if>
    </dt>
    <dd>
      <xsl:if test="@type != ''">
        <xsl:apply-templates select="@type" /><br />
      </xsl:if>
      <em><xsl:value-of select="@description" disable-output-escaping="yes" /></em>
    </dd>
  </xsl:template>

  <xsl:template match="docblock/tag[@name='return']">
      <xsl:if test="type = ''">void</xsl:if>
      <xsl:if test="type != ''">
          <xsl:call-template name="implodeTypes">
              <xsl:with-param name="items" select="type" />
          </xsl:call-template>
      </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag[@name='throws']">
      <tr>
        <td>
            <xsl:if test="type != ''">
                <xsl:call-template name="implodeTypes">
                    <xsl:with-param name="items" select="type" />
                </xsl:call-template>
            </xsl:if>
        </td>
        <td>
            <em><xsl:value-of select="@description" disable-output-escaping="yes" /></em>
        </td>
    </tr>
  </xsl:template>

  <xsl:template match="docblock/tag/@type">
    <xsl:if test="not(.)">n/a</xsl:if>
    <xsl:if test=".">
      <xsl:if test="../@link">
        <a href="{$root}{../@link}">
          <xsl:value-of select="." />
        </a>
      </xsl:if>
      <xsl:if test="not(../@link)">
        <xsl:if test=". = ''">void</xsl:if>
        <xsl:if test=". != ''"><xsl:value-of select="." /></xsl:if>
      </xsl:if>
    </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag/type">
    <xsl:if test="not(.)">n/a</xsl:if>
    <xsl:if test=".">
        <xsl:call-template name="implodeTypes">
            <xsl:with-param name="items" select="."/>
        </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag/@link">
    <xsl:if test="not(../@description)">n/a</xsl:if>
    <xsl:if test="../@description">
      <xsl:if test="../@link">
        <a href="{$root}{../@link}">
          <xsl:value-of select="../@description" disable-output-escaping="yes"/>
        </a>
      </xsl:if>
      <xsl:if test="not(../@link)">
        <xsl:value-of select="../@description" disable-output-escaping="yes" />
      </xsl:if>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>