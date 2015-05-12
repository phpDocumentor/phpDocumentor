<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <xsl:template match="package" mode="sidebar">
        <xsl:param name="parent_name" />
        <xsl:variable name="fqnn" select="concat($parent_name, @name)" />
        <xsl:variable name="element_count"
            select="count(/project/file/constant[@package=$fqnn]
                |/project/file/function[@package=$fqnn]
                |/project/file/class[@package=$fqnn]
                |/project/file/interface[@package=$fqnn]
                |/project/file/trait[@package=$fqnn])"
        />

        <li>
            <xsl:if test="$element_count > 0">
                <xsl:variable name="link">
                    <xsl:call-template name="createLink">
                        <xsl:with-param name="value" select="@full_name"/>
                    </xsl:call-template>
                </xsl:variable>
                <a href="{$root}packages/{$link}.html" title="{@name}"><i class="icon-folder-open"></i> <xsl:value-of select="@name" /></a>
            </xsl:if>

            <xsl:if test="$element_count = 0">
                <span class="empty-package"><i class="icon-folder-close"></i> <xsl:value-of select="@name" /></span>
            </xsl:if>

            <ul class="nav nav-list nav-packages">
                <xsl:apply-templates select="package" mode="sidebar">
                    <xsl:sort select="@name" />
                    <xsl:with-param name="parent_name" select="concat($fqnn, '\')" />
                </xsl:apply-templates>
            </ul>
        </li>
    </xsl:template>

    <xsl:template match="package" mode="contents">
        <xsl:param name="parent_name" />
        <xsl:variable name="fqnn" select="concat($parent_name, @name)" />

        <xsl:if test="count(/project/file/constant[@package=$fqnn]
            |/project/file/function[@package=$fqnn]
            |/project/file/class[@package=$fqnn]
            |/project/file/interface[@package=$fqnn]
            |/project/file/trait[@package=$fqnn]
            |package) > 0"
        >
            <ul class="breadcrumb">
                <li>
                    <a href="{$root}index.html"><i class="icon-folder-open"></i></a>
                    <span class="divider">\</span>
                    <xsl:apply-templates select="." mode="breadcrumb"/>
                </li>
            </ul>

            <div class="package-indent">
                <xsl:if test="count(/project/file/constant[@package=$fqnn]
                    |/project/file/function[@package=$fqnn]
                    |/project/file/class[@package=$fqnn]
                    |/project/file/interface[@package=$fqnn]
                    |/project/file/trait[@package=$fqnn]
                    |package) = 0"
                >
                    <div class="alert alert-info">This package does not contain any documentable elements</div>
                </xsl:if>

                <xsl:variable name="functions" select="/project/file/function[@package=$fqnn]"/>
                <xsl:if test="count($functions) > 0">
                    <h3><i title="Functions" class="icon-custom icon-function"></i> Functions</h3>
                    <xsl:apply-templates select="$functions" mode="contents">
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="classes" select="/project/file/class[@package=$fqnn]|/project/file/interface[@package=$fqnn]|/project/file/trait[@package=$fqnn]"/>
                <xsl:if test="count($classes) > 0">
                    <h3><i title="Classes" class="icon-custom icon-class"></i> Classes, interfaces, and traits</h3>
                    <xsl:apply-templates select="$classes" mode="compact">
                        <xsl:sort select="local-name()" order="descending" />
                        <xsl:sort select="full_name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="constants" select="/project/file/constant[@package=$fqnn]"/>
                <xsl:if test="count($constants) > 0">
                    <h3><i title="Constants" class="icon-custom icon-constant"></i> Constants</h3>
                    <xsl:apply-templates select="$constants" mode="contents">
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:variable name="packages" select="package"/>
                <xsl:if test="count($packages) > 0">
                    <xsl:apply-templates select="$packages" mode="contents">
                        <xsl:sort select="@name" />
                        <xsl:with-param name="parent_name" select="concat($fqnn, '\')" />
                    </xsl:apply-templates>
                </xsl:if>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template match="package" mode="sidebar-nav">
        <xsl:param name="parent_name"/>
        <xsl:variable name="name" select="@full_name"/>

        <div class="btn-group view pull-right" data-toggle="buttons-radio">
          <button class="btn details" title="Show descriptions and method names"><i class="icon-list"></i></button>
          <button class="btn simple" title="Show only method names"><i class="icon-align-justify"></i></button>
        </div>

        <ul class="side-nav nav nav-list">
            <li class="nav-header"><i class="icon-map-marker"></i> Packages</li>

            <xsl:apply-templates select="." mode="sidebar">
                <xsl:with-param name="parent_name" select="$parent_name" />
            </xsl:apply-templates>

            <xsl:variable name="functions" select="/project/file/function[@package=$name]"/>
            <xsl:if test="count($functions) > 0">
                <li class="nav-header"><i title="Functions" class="icon-custom icon-function"></i> Functions</li>
                <xsl:apply-templates select="$functions" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:variable name="traits" select="/project/file/trait[@package=$name]"/>
            <xsl:if test="count($traits) > 0">
                <li class="nav-header"><i title="Traits" class="icon-custom icon-trait"></i> Traits</li>
                <xsl:for-each select="$traits">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="interfaces" select="/project/file/interface[@package=$name]"/>
            <xsl:if test="count($interfaces) > 0">
                <li class="nav-header"><i title="Interfaces" class="icon-custom icon-interface"></i> Interfaces</li>
                <xsl:for-each select="$interfaces">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="classes" select="/project/file/class[@package=$name]"/>
            <xsl:if test="count($classes) > 0">
                <li class="nav-header"><i title="Classes" class="icon-custom icon-class"></i> Classes</li>
                <xsl:for-each select="$classes">
                    <li><a href="#{name}" title="{docblock/description}"><xsl:value-of select="name" /></a></li>
                </xsl:for-each>
            </xsl:if>

            <xsl:variable name="constants" select="/project/file/constant[@package=$name]"/>
            <xsl:if test="count($constants) > 0">
                <li class="nav-header"><i title="Constants" class="icon-custom icon-constant"></i> Constants</li>
                <xsl:apply-templates select="$constants" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>
        </ul>
    </xsl:template>

</xsl:stylesheet>
