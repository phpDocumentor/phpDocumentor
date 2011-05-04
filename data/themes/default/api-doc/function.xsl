<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="method/name">
    <h4 class="method">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#{../../name}">jump to class</a></div>
    </h4>
  </xsl:template>

  <xsl:template match="function/name">
    <h3 class="function">
      <xsl:value-of select="." />
      <div class="to-top"><a href="#top">jump to top</a></div>
    </h3>
  </xsl:template>

    <xsl:template match="argument">
        <xsl:variable name="name" select="name"/>
        <dt>
            <xsl:value-of select="$name"/>
        </dt>
        <dd>
            <xsl:if test="../docblock/tag[@name='param' and @variable=$name]/type">
                <xsl:apply-templates select="../docblock/tag[@name='param' and @variable=$name]/type"/>
                <br/>
            </xsl:if>
            <em>
                <xsl:value-of select="../docblock/tag[@name='param' and @variable=$name]/@description"/>
            </em>
        </dd>
    </xsl:template>

  <xsl:template match="function|method">
    <a id="{../full_name}::{name}()" />
    <xsl:apply-templates select="name" />
    <div class="{name()}">
      <code>
        <span class="highlight"><xsl:value-of select="name" /></span>

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
        <xsl:call-template name="implodeTypes">
          <xsl:with-param name="items" select="docblock/tag[@name='return']/type" />
        </xsl:call-template>
      </code>

      <xsl:apply-templates select="docblock/description" />
      <xsl:apply-templates select="docblock/long-description" />

      <xsl:if test="count(argument) > 0">
        <div class="api-section">
          <xsl:if test="name() = 'function'">
            <h4 class="arguments">Arguments</h4>
          </xsl:if>
          <xsl:if test="name() = 'method'">
            <h5 class="arguments">Arguments</h5>
          </xsl:if>
          <dl class="argument-info">
            <xsl:apply-templates select="argument" />
          </dl>
          <div class="clear"></div>
        </div>
      </xsl:if>

      <xsl:if test="docblock/tag[@name = 'return'] != '' and docblock/tag[@name = 'return']/@type != 'void'">
        <div class="api-section">
          <xsl:if test="name() = 'function'">
            <h4 class="output">Output</h4>
          </xsl:if>
          <xsl:if test="name() = 'method'">
            <h5 class="output">Output</h5>
          </xsl:if>
          <dl class="return-info">
            <xsl:apply-templates select="docblock/tag[@name='return']" />
          </dl>
          <div class="clear"></div>
        </div>
      </xsl:if>

      <xsl:if test="name() = 'method' or (docblock/tag[@name != 'param' and @name != 'return'])">
        <div class="api-section">
          <xsl:if test="name() = 'function'">
            <h4 class="info"><img src="{$root}images/arrow_right.gif" /> Details</h4>
          </xsl:if>
          <xsl:if test="name() = 'method'">
            <h5 class="info"><img src="{$root}images/arrow_right.gif" /> Details</h5>
          </xsl:if>
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