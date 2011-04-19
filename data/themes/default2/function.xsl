<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="method|function">
    <!--<xsl:variable name="node" select="tagName()"/>-->

    <a id="{../name}::{name}()" />
    <h3 class="function">
      <xsl:value-of select="name" />
    </h3>
    <div class="function">
      <p>
        <code>
          <xsl:value-of select="name" />

          <span class="nb-faded-text">(
            <xsl:for-each select="argument">
              <xsl:if test="position() &gt; 1">, </xsl:if>

              <xsl:variable name="variable_name" select="name" />

              <xsl:call-template name="implodeTypes">
                <xsl:with-param name="items" select="../docblock/tag[@name='param' and @variable=$variable_name]/type" />
              </xsl:call-template>&#160;<xsl:value-of select="$variable_name" />

              <xsl:if test="default != ''">
                =
                <xsl:value-of select="default" disable-output-escaping="yes" />
              </xsl:if>
            </xsl:for-each>
            )
          </span>
          :
          <span class="nb-faded-text">
            <xsl:apply-templates select="docblock/tag[@name='return']/@type"/>
          </span>
        </code>
      </p>

      <xsl:apply-templates select="docblock/description" />
      <xsl:apply-templates select="docblock/long-description" />

      <dl class="function-info">
        <xsl:apply-templates select="docblock/tag">
          <xsl:sort select="@name" />
        </xsl:apply-templates>
      </dl>

      <xsl:if test="count(argument) > 0">
        <h4 class="arguments">Arguments</h4>
        <div class="arguments">
          <xsl:for-each select="argument">
            <xsl:variable name="variable_name" select="name" />
            <xsl:variable name="variable_description" select="../docblock/tag[@name='param' and @variable=$variable_name]/@description" />

            <h5 class="argument">
              <xsl:value-of select="$variable_name" />
            </h5>

            <xsl:if test="$variable_description != ''">
            <p>
              <xsl:value-of select="$variable_description" disable-output-escaping="yes" />
            </p>
            </xsl:if>

            <dl class="argument-info">
              <dt>default</dt>
              <dd>
                <xsl:value-of select="default" disable-output-escaping="yes" />
              </dd>
              <xsl:apply-templates select="docblock/tag">
                <xsl:sort select="@name" />
              </xsl:apply-templates>
            </dl>
          </xsl:for-each>
        </div>
      </xsl:if>

    </div>

  </xsl:template>

</xsl:stylesheet>