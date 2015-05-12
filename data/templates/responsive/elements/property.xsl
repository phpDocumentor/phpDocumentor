<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <!-- Property display name -->
    <xsl:template match="property/name">
        <xsl:choose>
            <xsl:when test="../docblock/description[.!='']">
                <p><xsl:value-of select="../docblock/description" /></p>
            </xsl:when>
            <xsl:when test="../docblock/tag[@name='var']/@description[.!='']">
                <xsl:value-of select="../docblock/tag[@name='var']/@description" disable-output-escaping="yes"/>
            </xsl:when>
            <xsl:otherwise>
                <p><xsl:value-of select="." /></p>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="property/name" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:variable name="name" select="."/>
        <pre>
            <xsl:value-of select="$name"/>
            <xsl:text>&#160;</xsl:text>
            <xsl:apply-templates select="../docblock/tag[@name='var']" mode="signature">
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:apply-templates>
        </pre>
        
        <div class="row collapse">
            <div class="detail-description">
                <h3>Default</h3>
                <div class="subelement argument">
                    <xsl:if test="../default[.!='']">
                        <code>
                            <xsl:value-of select="../default" disable-output-escaping="yes"/>
                        </code>
                    </xsl:if>
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>
