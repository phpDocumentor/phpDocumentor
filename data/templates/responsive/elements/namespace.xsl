<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <xsl:template match="namespace" mode="sidebar">
        <xsl:param name="parent_name" />
        <xsl:variable name="fqnn" select="concat($parent_name, @name)" />

        <li>
            <xsl:if test="count(/project/file/constant[@namespace=$fqnn]
                |/project/file/function[@namespace=$fqnn]
                |/project/file/class[@namespace=$fqnn]
                |/project/file/interface[@namespace=$fqnn]
                |/project/file/trait[@namespace=$fqnn]) > 0"
            >
                <xsl:variable name="link">
                    <xsl:call-template name="createLink">
                        <xsl:with-param name="value" select="@full_name"/>
                    </xsl:call-template>
                </xsl:variable>

                <a href="{$root}namespaces/{$link}.html" title="{@name}"><i class="icon-th"></i> <xsl:value-of select="@name" /></a>
            </xsl:if>

            <xsl:if test="count(/project/file/constant[@namespace=$fqnn]
                |/project/file/function[@namespace=$fqnn]
                |/project/file/class[@namespace=$fqnn]
                |/project/file/interface[@namespace=$fqnn]
                |/project/file/trait[@namespace=$fqnn]) = 0"
            >
                <span class="empty-namespace"><i class="icon-stop"></i> <xsl:value-of select="@name" /></span>
            </xsl:if>

            <ul class="nav nav-list nav-namespaces">
                <xsl:apply-templates select="namespace" mode="sidebar">
                    <xsl:sort select="@name" />
                    <xsl:with-param name="parent_name" select="concat($fqnn, '\')" />
                </xsl:apply-templates>
            </ul>
        </li>
    </xsl:template>

    <xsl:template match="namespace" mode="contents">
        <xsl:param name="parent_name" />
        <xsl:variable name="fqnn" select="concat($parent_name, @name)" />

        <xsl:if test="count(/project/file/constant[@namespace=$fqnn]
            |/project/file/function[@namespace=$fqnn]
            |/project/file/class[@namespace=$fqnn]
            |/project/file/interface[@namespace=$fqnn]
            |/project/file/trait[@namespace=$fqnn]
            |namespace) > 0"
        >
            <ul class="breadcrumb">
                <li>
                    <a href="{$root}index.html"><i class="icon-th"></i></a>
                    <span class="divider">\</span>
                </li>
                <xsl:apply-templates select="." mode="breadcrumb"/>
            </ul>

            <div class="namespace-indent">
                <xsl:if test="count(/project/file/constant[@namespace=$fqnn]
                    |/project/file/function[@namespace=$fqnn]
                    |/project/file/class[@namespace=$fqnn]
                    |/project/file/interface[@namespace=$fqnn]
                    |/project/file/trait[@namespace=$fqnn]) = 0"
                >
                    <div class="alert alert-info">This namespace does not contain any documentable elements</div>
                </xsl:if>

                <xsl:variable name="functions" select="/project/file/function[@namespace=$fqnn]"/>
                <xsl:if test="count($functions) > 0">
                    <h3><i title="Functions" class="icon-custom icon-function"></i> Functions</h3>
                    <xsl:apply-templates select="$functions" mode="contents">
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="classes" select="/project/file/class[@namespace=$fqnn]"/>
                <xsl:variable name="interfaces" select="/project/file/interface[@namespace=$fqnn]"/>
                <xsl:variable name="traits" select="/project/file/trait[@namespace=$fqnn]"/>
                <xsl:if test="count($classes)+count($interfaces)+count($traits) > 0">
                    <h3><i title="Class" class="icon-custom icon-class"></i> Classes, interfaces, and traits</h3>
                    <xsl:apply-templates select="$classes|$interfaces|$traits" mode="compact">
                        <xsl:sort select="local-name()" order="descending" />
                        <xsl:sort select="full_name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="constants" select="/project/file/constant[@namespace=$fqnn]"/>
                <xsl:if test="count($constants) > 0">
                    <h3><i title="Constants" class="icon-custom icon-constant"></i> Constants</h3>
                    <xsl:apply-templates select="$constants" mode="contents">
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="namespaces" select="namespace"/>
                <xsl:if test="$namespaces">
                    <xsl:apply-templates select="$namespaces" mode="contents">
                        <xsl:sort select="@name" />
                        <xsl:with-param name="parent_name" select="concat($fqnn, '\')" />
                    </xsl:apply-templates>
                </xsl:if>
            </div>
        </xsl:if>

    </xsl:template>

    <xsl:template match="namespace" mode="sidebar-nav">
        <xsl:param name="parent_name" select="@full_name"/>
        <xsl:variable name="fqnn" select="concat($parent_name, @name)" />

        <div class="btn-group view pull-right" data-toggle="buttons-radio">
          <button class="btn details" title="Show descriptions and method names"><i class="icon-list"></i></button>
          <button class="btn simple" title="Show only method names"><i class="icon-align-justify"></i></button>
        </div>

        <ul class="side-nav nav nav-list">
            <li class="nav-header"><i class="icon-map-marker"></i> Namespaces</li>

            <xsl:apply-templates select="." mode="sidebar">
                <xsl:with-param name="parent_name" select="$parent_name"/>
            </xsl:apply-templates>

            <xsl:variable name="functions" select="/project/file/function[@namespace=$fqnn]"/>
            <xsl:if test="count($functions) > 0">
                <li class="nav-header"><i title="Functions" class="icon-custom icon-function"></i> Functions</li>
                <xsl:apply-templates select="$functions" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:variable name="traits" select="/project/file/trait[@namespace=$fqnn]"/>
            <xsl:if test="count($traits) > 0">
                <li class="nav-header"><i title="Traits" class="icon-custom icon-trait"></i> Traits</li>
                <xsl:for-each select="$traits">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="interfaces" select="/project/file/interface[@namespace=$fqnn]"/>
            <xsl:if test="count($interfaces) > 0">
                <li class="nav-header"><i title="Interfaces" class="icon-custom icon-interface"></i> Interfaces</li>
                <xsl:for-each select="$interfaces">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="classes" select="/project/file/class[@namespace=$fqnn]"/>
            <xsl:if test="count($classes) > 0">
                <li class="nav-header"><i title="Classes" class="icon-custom icon-class"></i> Classes</li>
                <xsl:for-each select="$classes">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="constants" select="/project/file/constant[@namespace=$fqnn]"/>
            <xsl:if test="count($constants) > 0">
                <li class="nav-header"><i title="Constants" class="icon-custom icon-constant"></i> Constants</li>
                <xsl:apply-templates select="$constants" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>
        </ul>
    </xsl:template>

</xsl:stylesheet>
