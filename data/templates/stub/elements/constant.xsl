<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <!-- Constant display name -->
    <xsl:template match="constant/name">
        <xsl:value-of select="../docblock/description" />
        <xsl:if test="not(../docblock/description) or ../docblock/description = ''">
            <xsl:value-of select="../docblock/tag[@name='var']/@description" />
            <xsl:if test="not(../docblock/tag[@name='var']/@description) or ../docblock/tag[@name='var']/@description = ''">
                <xsl:value-of select="." />
            </xsl:if>
        </xsl:if>
    </xsl:template>

    <xsl:template match="constant/name" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:variable name="name" select="."/>
        <pre>
            <xsl:value-of select="$name"/>
            <xsl:text>&#160;</xsl:text>
            <xsl:apply-templates select="../docblock/tag[@name='var']" mode="signature">
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:apply-templates>
        </pre>
    </xsl:template>

    <xsl:template match="property|constant" mode="contents">
        <a name="{name}" id="{name}">&#160;</a>
        <div class="element clickable {local-name(.)} {@visibility} {name}" data-toggle="collapse" data-target=".{name} .collapse">
            <h2><xsl:apply-templates select="name" /></h2>
            <xsl:apply-templates select="name" mode="signature" />
            <div class="labels">
                <xsl:if test="docblock/tag[@name='api']">
                    <span class="label label-info">API</span>
                </xsl:if>
                <xsl:if test="inherited_from">
                    <span class="label">Inherited</span>
                </xsl:if>
            </div>

            <div class="row collapse">
                <div>
                    <xsl:attribute name="class">
                        <xsl:if test="docblock/tag[@name='example']">span4</xsl:if>
                        <xsl:if test="not(docblock/tag[@name='example'])">span8</xsl:if>
                    </xsl:attribute>
                    <p class="long_description">
                        <xsl:value-of select="docblock/long-description" disable-output-escaping="yes" />
                    </p>

                    <xsl:if test="count(docblock/tag[@name != 'var']) > 0">
                        <table class="table table-bordered">
                            <xsl:apply-templates select="docblock/tag[@name != 'var']" mode="tabular" />
                        </table>
                    </xsl:if>
                </div>
                <xsl:if test="docblock/tag[@name='example']">
                    <div class="span4">
                        <h3>Examples</h3>
                        <xsl:for-each select="docblock/tag[@name='example']">
                            <pre class="prettyprint linenums">
                                <xsl:value-of select="."/>
                            </pre>
                        </xsl:for-each>
                    </div>
                </xsl:if>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>