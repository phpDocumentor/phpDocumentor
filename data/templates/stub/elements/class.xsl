<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <!-- Sidebar index for classes and interfaces -->
    <xsl:template match="/project/file/class|/project/file/interface" mode="sidebar">
        <!-- Visibility toggles -->
        <span class="btn-group visibility" data-toggle="buttons-checkbox">
          <button class="btn public active" title="Show public elements">Public</button>
          <button class="btn protected" title="Show protected elements">Protected</button>
          <button class="btn private" title="Show private elements">Private</button>
          <button class="btn inherited active" title="Show inherited elements">Inherited</button>
        </span>

        <div class="btn-group view pull-right" data-toggle="buttons-radio">
          <button class="btn details" title="Show descriptions and method names"><i class="icon-list"></i></button>
          <button class="btn simple" title="Show only method names"><i class="icon-align-justify"></i></button>
        </div>

        <ul class="side-nav nav nav-list">
            <xsl:if test="method">
                <li class="nav-header"><i class="icon-custom icon-method"></i>&#160;Methods</li>
                <xsl:apply-templates select="method[@visibility != 'protected' and @visibility != 'private']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="method[@visibility = 'protected']">
                <li class="nav-header protected">&#187; Protected</li>
                <xsl:apply-templates select="method[@visibility = 'protected']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="method[@visibility = 'private']">
                <li class="nav-header private">&#187; Private</li>
                <xsl:apply-templates select="method[@visibility = 'private']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="property">
                <li class="nav-header"><i class="icon-custom icon-property"></i>&#160;Properties</li>
                <xsl:apply-templates select="property[@visibility != 'protected' and @visibility != 'private']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="property[@visibility = 'protected']">
                <li class="nav-header protected">&#187; Protected</li>
                <xsl:apply-templates select="property[@visibility = 'protected']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="property[@visibility = 'private']">
                <li class="nav-header private">&#187; Private</li>
                <xsl:apply-templates select="property[@visibility = 'private']" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>

            <xsl:if test="constant">
                <li class="nav-header"><i class="icon-custom icon-constant"></i>&#160;Constants</li>
                <xsl:apply-templates select="constant" mode="sidebar">
                    <xsl:sort select="name" />
                </xsl:apply-templates>
            </xsl:if>
        </ul>
    </xsl:template>

    <xsl:template match="extends|implements">
        <xsl:param name="element" select="/project/file/class[full_name=.]|/project/file/interface[full_name=.]"/>

        <xsl:if test="$element">
            <a href="{$root}classes/db_{.}.html">
                <xsl:value-of select="." />
            </a>
        </xsl:if>
        <xsl:if test="not($element)">
            <xsl:value-of select="." />
        </xsl:if>
    </xsl:template>

    <!-- Breadcrumb to display inheritance tree -->
    <xsl:template match="extends" mode="inheritance_breadcrumb">
        <xsl:variable name="name" select="."/>
        <xsl:variable
            name="extended_element"
            select="/project/file/class[full_name=$name]|/project/file/interface[full_name=$name]"
        />

        <xsl:if test="$name != ''">
            <li>
                <xsl:apply-templates select="$extended_element/extends" mode="inheritance_breadcrumb" />
            </li>

            <xsl:apply-templates select=".">
                <xsl:with-param name="element" select="$extended_element" />
            </xsl:apply-templates>
        </xsl:if>
    </xsl:template>

    <xsl:template match="class|interface" mode="compact">
        <xsl:variable name="filename">db_<xsl:value-of select="full_name"/></xsl:variable>

        <a name="{name}" id="{name}" />
        <div class="element ajax clickable {local-name()}" href="{$root}classes/{$filename}.html">
            <h1><xsl:value-of select="name"/><a href="{$root}classes/{$filename}.html">¶</a></h1>
            <p class="short_description"><xsl:value-of select="docblock/description" disable-output-escaping="yes"/></p>
            <div class="details collapse"></div>
            <a href="{$root}classes/{$filename}.html" class="more">&#171; More &#187;</a>
        </div>
    </xsl:template>

    <xsl:template match="class|interface" mode="contents">
        <xsl:variable name="namespace" select="@namespace"/>

        <a name="{full_name}" id="{full_name}"></a>

        <xsl:if test="$namespace != 'default'">
            <ul class="breadcrumb">
                <li>
                    <a href="{$root}index.html"><i class="icon-custom icon-class"></i></a>
                    <span class="divider">\</span>
                </li>
                <xsl:apply-templates select="//namespace[@full_name=$namespace]" mode="breadcrumb">
                    <xsl:with-param name="active" select="false()"/>
                </xsl:apply-templates>
                <li class="active">
                    <span class="divider">\</span>
                    <a href="{$root}classes/db_{full_name}.html"><xsl:value-of select="name" /></a>
                </li>
            </ul>
        </xsl:if>

        <div href="{$root}classes/db_{full_name}.html" class="element {local-name()}">
            <xsl:if test="docblock/description">
                <p class="short_description"><xsl:value-of select="docblock/description" disable-output-escaping="yes"/></p>
            </xsl:if>

            <div class="details">
                <xsl:if test="docblock/long-description">
                <p class="long_description"><xsl:value-of select="docblock/long-description" disable-output-escaping="yes"/></p>
                </xsl:if>

                <xsl:if test="count(docblock/tag) > 0">
                    <table class="table table-bordered">
                        <xsl:apply-templates select="docblock/tag" mode="tabular" />
                    </table>
                </xsl:if>

                <xsl:if test="method">
                    <h3><i class="icon-custom icon-method"></i>&#160;Methods</h3>
                    <xsl:apply-templates select="method" mode="contents">
                        <xsl:sort select="@visibility" order="descending"/>
                        <xsl:sort select="name"/>
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:if test="property">
                    <h3><i class="icon-custom icon-property"></i>&#160;Properties</h3>
                    <xsl:apply-templates select="property" mode="contents">
                        <xsl:sort select="@visibility" order="descending" />
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>

                <xsl:if test="constant">
                    <h3><i class="icon-custom icon-constant"></i>&#160;Constants</h3>
                    <xsl:apply-templates select="constant" mode="contents">
                        <xsl:sort select="name" />
                    </xsl:apply-templates>
                </xsl:if>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>