<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />
    <xsl:include href="layout.xsl" />

    <xsl:template match="/project" mode="contents">

        <div class="hero-unit">
            <h1>
                <xsl:value-of select="$title" disable-output-escaping="yes"/>
                <xsl:if test="$title = ''">phpDocumentor</xsl:if>
            </h1>
            <h2>Documentation</h2>
        </div>

        <div class="row">
            <div class="span7">
                <xsl:if test="count(/project/namespace[@name != 'default' and @name != 'global' and @name != '']) > 0">
                <div class="well">
                    <ul class="nav nav-list">
                        <li class="nav-header">Namespaces</li>
                        <xsl:apply-templates select="/project/namespace" mode="menu">
                            <xsl:sort select="@full_name" />
                        </xsl:apply-templates>
                    </ul>
                </div>
                </xsl:if>

                <xsl:if test="count(/project/package[@name != '' and @name != 'default']) > 0 or count(/project/namespace[@name != 'default' and @name != 'global' and @name != '']) = 0">
                <div class="well">
                    <ul class="nav nav-list">
                        <li class="nav-header">Packages</li>
                        <xsl:apply-templates select="/project/package" mode="menu">
                            <xsl:sort select="@name"/>
                        </xsl:apply-templates>
                    </ul>
                </div>
                </xsl:if>

                <xsl:if test="count(/project/file/class[docblock/tag[@name='api']]) + count(/project/file/class[docblock/tag[@name='api']]/method[@visibility='public' and substring(name,1,2)!='__']) + count(/project/file/class/method[@visibility='public' and docblock/tag[@name='api']]) > 0">
                <div class="well">
                    <ul class="nav nav-list">
                        <li class="nav-header">Api</li>

    		            <xsl:variable name="classes" select="/project/file/class[docblock/tag[@name='api']]"/>
                            <xsl:if test="count($classes) > 0">
                                <li class="nav-header"><i class="icon-custom icon-class"></i> Public API Classes</li>
                                <xsl:for-each select="$classes">
                                    <li><a href="classes/{name}.html" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                                </xsl:for-each>
                            </xsl:if>

                            <xsl:variable name="methods" select="/project/file/class[docblock/tag[@name='api']]/method[@visibility='public' and substring(name,1,2)!='__']|/project/file/class/method[@visibility='public' and docblock/tag[@name='api']]"/>
                            <xsl:if test="count($methods) > 0">
                                <li class="nav-header"><i class="icon-custom icon-method"></i> Public API Methods</li>
                                <xsl:for-each select="$methods">
                                    <li><a href="classes/{../name}.html#{name}" title="{docblock/description}"><xsl:value-of select="../name" />.<xsl:value-of select="name" /></a></li>
                                </xsl:for-each>
                            </xsl:if>
                    </ul>
                </div>
                </xsl:if>

            </div>
            <div class="span5">
                <div class="well">
                    <ul class="nav nav-list">
                        <li class="nav-header">Charts</li>
                        <li>
                            <a href="{$root}graph_class.html">
                                <i class="icon-list-alt"></i> Class inheritance diagram
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="well">
                    <ul class="nav nav-list">
                        <li class="nav-header">Reports</li>
                        <xsl:apply-templates select="/" mode="report-overview" />
                    </ul>
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>