<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="method/name">
    <h4 class="method">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#{../../name}">Jump to class</a></div>
    </h4>
  </xsl:template>

  <xsl:template match="function/name">
    <h3 class="function">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#top">Jump to top</a></div>
    </h3>
  </xsl:template>

  <xsl:template match="function|method">
    <a id="{../name}::{name}()" />
    <xsl:apply-templates select="name" />
    <div class="{name()}">
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

      <xsl:apply-templates select="docblock/description" />
      <xsl:apply-templates select="docblock/long-description" />

      <xsl:if test="count(argument) > 0">
        <div class="api-section">
          <h4 class="arguments">Arguments</h4>
          <dl class="function-info">
            <xsl:apply-templates select="docblock/tag[@name = 'param']">
              <xsl:sort select="@name" />
            </xsl:apply-templates>
          </dl>
          <div class="clear"></div>
        </div>
      </xsl:if>

      <xsl:if test="docblock/tag[@name = 'return'] != '' and docblock/tag[@name = 'return']/@type != 'void'">
        <div class="api-section">
          <h4 class="arguments">Output</h4>
          <dl class="function-info">
            <xsl:apply-templates select="docblock/tag[@name = 'return']">
              <xsl:sort select="@name" />
            </xsl:apply-templates>
          </dl>
          <div class="clear"></div>
        </div>
      </xsl:if>

      <xsl:if test="name() = 'method' or (docblock/tag[@name != 'param' and @name != 'return'])">
        <div class="api-section">
          <h4 class="info">Details</h4>
          <dl class="function-info">
            <xsl:if test="name() = 'method'">
              <dt>visibility</dt>
              <dd><xsl:value-of select="@visibility" /></dd>
              <dt>final</dt>
              <dd><xsl:value-of select="@final" /></dd>
              <dt>static</dt>
              <dd><xsl:value-of select="@static" /></dd>
            </xsl:if>
            <xsl:if test="docblock/tag[@name != 'param' and @name != 'return']">
            <xsl:apply-templates select="docblock/tag[@name != 'param' and @name != 'return']">
              <xsl:sort select="@name" />
            </xsl:apply-templates>
            </xsl:if>
          </dl>
          <div class="clear"></div>
        </div>
      </xsl:if>

    </div>

  </xsl:template>

</xsl:stylesheet>