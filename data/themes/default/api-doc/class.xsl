<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="class|interface">
    <a id="{full_name}" />
    <h2 class="{name()}">
      <xsl:value-of select="full_name" />
      <div class="to-top"><a href="#top">jump to top</a></div>
    </h2>

    <div class="class">
      <dl class="class-info">
          <xsl:if test="extends != ''">
          <dt>Extends from</dt>
          <dd>
              <xsl:if test="extends[not(@link)]">
                  <xsl:value-of select="extends"/>
              </xsl:if>
              <xsl:if test="extends[@link != '']">
                  <a href="{extends/@link}">
                      <xsl:value-of select="extends"/>
                  </a>
              </xsl:if>
          </dd>
          </xsl:if>
          <xsl:if test="implements">
          <dt>Implements</dt>
          <xsl:for-each select="implements">
              <dd>
                  <xsl:if test="@link = ''">
                      <xsl:value-of select="."/>
                  </xsl:if>
                  <xsl:if test="@link != ''">
                      <a href="{@link}">
                          <xsl:value-of select="."/>
                      </a>
                  </xsl:if>
              </dd>
          </xsl:for-each>
          </xsl:if>
        <xsl:apply-templates select="docblock/tag">
          <xsl:sort select="@name" />
        </xsl:apply-templates>

        <xsl:if test="count(constant) > 0">
          <dt>Constants</dt>
          <xsl:for-each select="constant">
            <xsl:sort select="name" />
            <dd>
              <a class="constant" href="#{../full_name}::{name}">
                <xsl:value-of select="name" />
              </a>
            </dd>
          </xsl:for-each>
        </xsl:if>

        <xsl:if test="count(property) > 0">
          <dt>Properties</dt>
          <xsl:for-each select="property">
            <xsl:sort select="name" />
            <dd>
              <a class="property {@visibility}" href="#{../full_name}::{name}">
                <xsl:value-of select="name" />
              </a>
            </dd>
          </xsl:for-each>
        </xsl:if>

        <xsl:if test="count(method) > 0">
          <dt>Methods</dt>
          <xsl:for-each select="method">
            <xsl:sort select="name" />
            <dd>
              <a class="method {@visibility}" href="#{../full_name}::{name}()">
                <xsl:value-of select="name" />
              </a>
            </dd>
          </xsl:for-each>
        </xsl:if>
      </dl>

      <xsl:if test="docblock/description|docblock/long-description">
        <h3>Description</h3>
        <xsl:apply-templates select="docblock/description" />
        <xsl:apply-templates select="docblock/long-description" />
      </xsl:if>

      <xsl:if test="count(constant) > 0">
        <h3>Constants</h3>
        <div>
          <xsl:apply-templates select="constant" />
        </div>
      </xsl:if>

      <xsl:if test="count(property) > 0">
        <h3>Properties</h3>
        <div>
          <xsl:apply-templates select="property">
            <xsl:sort select="name" />
          </xsl:apply-templates>
        </div>
      </xsl:if>

      <xsl:if test="count(method) > 0">
        <h3>Methods</h3>
        <div>
          <xsl:apply-templates select="method">
            <xsl:sort select="name" />
          </xsl:apply-templates>
        </div>
      </xsl:if>
    </div>

  </xsl:template>

</xsl:stylesheet>
