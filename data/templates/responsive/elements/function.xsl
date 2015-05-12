<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
>
    <xsl:output indent="yes" method="html" />

    <!-- Method/Function display name -->
    <xsl:template match="function/name|method/name">
        <xsl:value-of select="../docblock/description" />
        <xsl:if test="not(../docblock/description) or ../docblock/description = ''">
            <xsl:value-of select="." />()
        </xsl:if>
    </xsl:template>

    <xsl:template match="argument/type" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:variable name="name" select="../name" />
        <xsl:if test=". = '' and ../../docblock/tag[@name='param' and @variable=$name]/type">
            <xsl:call-template name="implodeTypes">
                <xsl:with-param name="items" select="../../docblock/tag[@name='param' and @variable=$name]/type"/>
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:call-template>
            <xsl:text>&#160;</xsl:text>
        </xsl:if>
        <xsl:if test=". != ''">
            <xsl:apply-templates select="."/>
            <xsl:text>&#160;</xsl:text>
        </xsl:if>
    </xsl:template>

    <xsl:template match="function/name|method/name" mode="signature">
        <xsl:param name="exclude-link" />

        <pre>
            <xsl:value-of select="."/>
            <xsl:text>(</xsl:text>
            <xsl:call-template name="implodeTypesSignature">
                <xsl:with-param name="items" select="../argument"/>
                <xsl:with-param name="separator" select="', '"/>
                <xsl:with-param name="exclude-link" select="$exclude-link" />
            </xsl:call-template>
            <xsl:text>)&#160;</xsl:text>
            <xsl:apply-templates select="../docblock/tag[@name='return']" mode="signature">
                <xsl:with-param name="exclude-link" select="$exclude-link" />
            </xsl:apply-templates>
        </pre>
    </xsl:template>

    <xsl:template match="argument">
        <div class="subelement argument">
            <xsl:variable name="name" select="name"/>
            <xsl:variable name="tag" select="../docblock/tag[@name='param' and @variable=$name]" />
            <h4><xsl:value-of select="name"/></h4>
            <xsl:apply-templates select="$tag/type" mode="contents" />
            <xsl:value-of select="$tag/@description" disable-output-escaping="yes" />
        </div>
    </xsl:template>

    <xsl:template match="function|method" mode="contents">
        <a id="{local-name(.)}_{name}"></a>
        <xsl:variable name="inherited">
            <xsl:if test="inherited_from"><xsl:value-of select="'inherited'" /></xsl:if>
        </xsl:variable>

        <div class="element clickable {local-name(.)} {@visibility} {local-name(.)}_{name} {$inherited}" data-toggle="collapse" data-target=".{local-name(.)}_{name} .collapse" title="{@visibility}">
            <h2><xsl:apply-templates select="name" /></h2>
            <xsl:apply-templates select="name" mode="signature" />
            <div class="labels">
                <xsl:if test="docblock/tag[@name='api']">
                    <span class="label label-info">API</span>
                </xsl:if>
                <xsl:if test="inherited_from">
                    <span class="label">Inherited</span>
                </xsl:if>
                <xsl:if test="@static='true' or docblock/tag[@name='static']">
                    <span class="label">Static</span>
                </xsl:if>
            </div>

            <div class="row collapse">
                <div>
                    <xsl:attribute name="class">
                        <xsl:if test="docblock/tag[@name='example']">span4</xsl:if>
                        <xsl:if test="not(docblock/tag[@name='example'])">detail-description</xsl:if>
                    </xsl:attribute>

                    <div class="long_description">
                        <xsl:value-of select="php:function('phpDocumentor\Plugin\Core\Xslt\Extension::markdown', string(docblock/long-description))" disable-output-escaping="yes" />
                    </div>

                    <xsl:if test="count(docblock/tag[@name != 'return' and @name != 'param' and @name != 'throws' and @name != 'throw']) > 0">
                        <table class="table table-bordered">
                            <xsl:apply-templates select="docblock/tag[@name != 'return' and @name != 'param' and @name != 'throws' and @name != 'throw']" mode="tabular" />
                        </table>
                    </xsl:if>

                    <xsl:if test="count(argument) > 0">
                    <h3>Parameters</h3>
                        <xsl:apply-templates select="argument" />
                    </xsl:if>

                    <xsl:if test="count(docblock/tag[@name = 'throws' or @name = 'throw']) > 0">
                        <h3>Exceptions</h3>
                        <table class="table table-bordered">
                            <xsl:apply-templates select="docblock/tag[@name = 'throws' and @name != 'throw']" mode="tabular" />
                        </table>
                    </xsl:if>

                    <xsl:if test="docblock/tag[@name='return' and @type != 'void']">
                        <h3>Returns</h3>
                        <xsl:apply-templates select="docblock/tag[@name='return' and @type != 'void']" mode="contents"/>
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
