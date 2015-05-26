<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml"
    xmlns:dbx="http://phpdoc.org/xsl/functions"
    exclude-result-prefixes="dbx">
    <xsl:output indent="yes" method="html"/>


    <xsl:template match="class/extends|interface/extends">
        <xsl:if test="not(.)">n/a</xsl:if>
        <xsl:if test=".">
            <xsl:if test="@link">
                <a href="{$root}files/{@link}"><xsl:value-of select="."/></a>
            </xsl:if>
            <xsl:if test="not(@link)">
                <xsl:if test=". = ''">?</xsl:if>
                <xsl:if test=". != ''">
                    <xsl:value-of select="."/>
                </xsl:if>
            </xsl:if>
        </xsl:if>
    </xsl:template>

    <xsl:template name="class_inherit">
        <xsl:param name="class"/>
        <xsl:param name="depth"/>

        <a href="{concat($root, 'files/', $class/../@generated-path, '#', $class/full_name)}">
            <xsl:if test="$depth != 0">
                <xsl:attribute name="style">color: gray; font-size: 0.8em
                </xsl:attribute>
            </xsl:if>
            <xsl:value-of select="$class/full_name"/>
        </a>

        <xsl:variable name="parent" select="$class/extends"/>
        <xsl:if test="/project/file/*[full_name=$parent]">
            &lt;
            <xsl:call-template name="class_inherit">
                <xsl:with-param name="class"
                                select="/project/file/*[full_name=$parent]"/>
                <xsl:with-param name="depth" select="$depth+1"/>
            </xsl:call-template>
        </xsl:if>

        <xsl:if test="$parent != '' and not(/project/file/*[full_name=$parent])">
            &lt;
            <xsl:apply-templates select="$parent"/>
        </xsl:if>

    </xsl:template>

    <xsl:template match="class|interface">
        <a id="{full_name}" class="anchor"/>
        <h2 class="{name()}">
            <xsl:value-of select="full_name"/>
            <div class="to-top">
                <a href="#top">jump to top</a>
            </div>
        </h2>

        <div class="class">
            <div class="description">
                <xsl:if test="@final='true'">
                    <span class="attribute">final</span>
                </xsl:if>

                <xsl:if test="@abstract='true'">
                    <span class="attribute">abstract</span>
                </xsl:if>
            </div>
            <small class="package"><b>Package: </b><xsl:value-of select="@package"/></small>
            <xsl:if test="docblock/description|docblock/long-description">
                <xsl:apply-templates select="docblock/description"/>
                <xsl:apply-templates select="docblock/long-description"/>
            </xsl:if>

            <dl class="class-info">

                <xsl:if test="implements">
                    <dt>Implements</dt>
                    <xsl:for-each select="implements">
                        <dd>
                            <xsl:if test="@link = ''">
                                <xsl:value-of select="."/>
                            </xsl:if>
                            <xsl:if test="@link != ''">
                                <a href="{$root}files/{@link}">
                                    <xsl:value-of select="."/>
                                </a>
                            </xsl:if>
                        </dd>
                    </xsl:for-each>
                </xsl:if>

                <xsl:if test="extends != ''">
                    <dt>Parent(s)</dt>
                    <dd>
                        <xsl:variable name="parent" select="extends"/>
                        <xsl:if test="/project/file/*[full_name=$parent]">
                            <xsl:call-template name="class_inherit">
                                <xsl:with-param name="class"
                                                select="/project/file/*[full_name=$parent]"/>
                                <xsl:with-param name="depth" select="0"/>
                            </xsl:call-template>
                        </xsl:if>
                        <xsl:if test="not(/project/file/*[full_name=$parent])">
                            <xsl:apply-templates select="$parent"/>
                        </xsl:if>
                    </dd>
                </xsl:if>

                <xsl:variable name="full_name" select="full_name"/>

                <xsl:if test="/project/file/*[extends=$full_name]">
                    <dt>Children</dt>
                    <xsl:for-each select="/project/file/*[extends=$full_name]">
                        <dd>
                            <a href="{concat($root, 'files/', ../@generated-path, '#', full_name)}">
                                <xsl:value-of select="full_name"/>
                            </a>
                        </dd>
                    </xsl:for-each>
                </xsl:if>

                <xsl:apply-templates select="docblock/tag[@name='see']"/>
                <xsl:apply-templates select="docblock/tag[@name != 'see' and @name != 'package' and @name != 'subpackage']">
                    <xsl:sort select="dbx:ucfirst(@name)"/>
                </xsl:apply-templates>
            </dl>

            <xsl:call-template name="doctrine"/>

            <xsl:if test="count(constant) > 0">
                <h3>Constants</h3>
                <div>
                    <xsl:apply-templates select="constant"/>
                </div>
            </xsl:if>

            <xsl:if test="count(property) > 0">
                <h3>Properties</h3>
                <div>
                    <xsl:apply-templates select="property">
                        <xsl:sort select="name"/>
                    </xsl:apply-templates>
                </div>
            </xsl:if>

            <xsl:if test="count(method) > 0">
                <h3>Methods</h3>
                <div>
                    <xsl:apply-templates select="method">
                        <xsl:sort select="name"/>
                    </xsl:apply-templates>
                </div>
            </xsl:if>
        </div>

    </xsl:template>

</xsl:stylesheet>
