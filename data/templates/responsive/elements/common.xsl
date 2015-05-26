<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <!-- Concatenate items with links with a given separator, based on: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
    <xsl:template name="implodeTypes">
        <xsl:param name="items" />
        <xsl:param name="separator" select="' | '" />
        <xsl:param name="exclude-link" />

        <xsl:for-each select="$items">
            <xsl:if test="position() &gt; 1">
                <xsl:value-of select="$separator" />
            </xsl:if>

            <xsl:apply-templates select=".">
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:apply-templates>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="implodeTypesSignature">
        <xsl:param name="items" />
        <xsl:param name="separator" select="' | '" />
        <xsl:param name="exclude-link" />

        <xsl:for-each select="$items">
            <xsl:if test="position() &gt; 1">
                <xsl:value-of select="$separator" />
            </xsl:if>

            <xsl:apply-templates select="." mode="signature">
                <xsl:with-param name="exclude-link" select="$exclude-link"/>
            </xsl:apply-templates>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="type">
        <xsl:param name="exclude-link" />

        <xsl:if test="@link and not($exclude-link)">
            <xsl:variable name="name" select="."/>
            <xsl:variable name="element" select="/project/file/class[full_name=$name]|/project/file/interface[full_name=$name]"/>

            <xsl:variable name="link">
                <xsl:call-template name="createLink">
                    <xsl:with-param name="value" select="$element/full_name"/>
                </xsl:call-template>
            </xsl:variable>

            <xsl:if test="$element">
                <a href="{$root}classes/{substring($link, 2)}.html"><xsl:value-of select="." /></a>
            </xsl:if>

            <xsl:if test="not($element)">
                <a href="{@link}"><xsl:value-of select="." /></a>
            </xsl:if>

        </xsl:if>
        <xsl:if test="not(@link) or $exclude-link"><xsl:value-of select="."/></xsl:if>
    </xsl:template>

    <xsl:template match="type" mode="contents">
        <code><xsl:apply-templates select="."/></code>
    </xsl:template>

    <xsl:template match="tag[@name='return' or @name='param' or @name='var']" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:text>:&#160;</xsl:text>

        <xsl:call-template name="implodeTypes">
            <xsl:with-param name="items" select="type"/>
            <xsl:with-param name="exclude-link" select="$exclude-link"/>
        </xsl:call-template>
    </xsl:template>

    <xsl:template match="argument" mode="signature">
        <xsl:param name="exclude-link" />

        <xsl:apply-templates select="type" mode="signature">
            <xsl:with-param name="exclude-link" select="$exclude-link"/>
        </xsl:apply-templates>
        <xsl:value-of select="name"/>
        <xsl:if test="default[.!='']">
            <code>
                <xsl:text>&#160;=&#160;</xsl:text>
                <xsl:value-of select="default"/>
            </code>
        </xsl:if>
    </xsl:template>

    <!-- Property/Constant list item in the sidebar -->
    <xsl:template match="method" mode="sidebar">
        <li>
            <xsl:attribute name="class">
                <xsl:value-of select="local-name()"/>
                <xsl:text> </xsl:text>
                <xsl:value-of select="@visibility" />
                <xsl:text> </xsl:text>
                <xsl:if test="inherited_from">inherited</xsl:if>
            </xsl:attribute>

			<xsl:variable name="tooltip">
			    <xsl:value-of select="name"/>
				<xsl:text>()</xsl:text>
				<xsl:if test="docblock/description">
					<xsl:text> :: </xsl:text>
					<xsl:value-of select="docblock/description"/>
				</xsl:if>	
			</xsl:variable>

            <a href="#{local-name(.)}_{translate(name, '$', '')}" title="{$tooltip}">
                <xsl:variable name="desc">
                    <xsl:apply-templates select="name" />
                </xsl:variable>
                <span class="description">
                    <xsl:choose>
                        <xsl:when test="name()='property'">
                            <xsl:value-of select="substring-after(substring-before(string($desc), '&lt;/p&gt;'), '&lt;p&gt;')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="$desc" />
                        </xsl:otherwise>
                    </xsl:choose>
                </span>
                <pre><xsl:value-of select="name" /><xsl:if test="local-name() = 'method'">()</xsl:if></pre>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="function|property|constant|class|interface|trait" mode="sidebar">
        <li>
            <xsl:attribute name="class">
                <xsl:value-of select="local-name()"/>
                <xsl:text> </xsl:text>
                <xsl:value-of select="@visibility" />
                <xsl:text> </xsl:text>
                <xsl:if test="inherited_from">inherited</xsl:if>
            </xsl:attribute>

            <a href="#{local-name(.)}_{translate(name, '$', '')}" title="{name}() :: {docblock/description}">
                <xsl:variable name="desc">
                    <xsl:apply-templates select="name" />
                </xsl:variable>
                <span class="description">
                    <xsl:choose>
                        <xsl:when test="name()='property'">
                            <xsl:value-of select="substring-after(substring-before(string($desc), '&lt;/p&gt;'), '&lt;p&gt;')"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="$desc" />
                        </xsl:otherwise>
                    </xsl:choose>
                </span>
                <pre><xsl:value-of select="name" /><xsl:if test="local-name() = 'method'">()</xsl:if></pre>
            </a>
        </li>
    </xsl:template>

    <xsl:template match="tag" mode="tabular">
        <tr>
            <th><xsl:value-of select="@name"/></th>
            <td><xsl:value-of select="@description" disable-output-escaping="yes"/></td>
        </tr>
    </xsl:template>

    <xsl:template match="tag[@name = 'license' or @name = 'link' or @name = 'see' or @name = 'uses' or @name = 'author']" mode="tabular">
        <tr>
            <th><xsl:value-of select="@name"/></th>
            <td>
                <a href="{@link}">
                    <xsl:value-of select="@description" disable-output-escaping="yes" />
                </a>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="tag[@name = 'package']" mode="tabular">
        <xsl:variable name="link">
            <xsl:call-template name="createLink">
                <xsl:with-param name="value" select="../../@package"/>
            </xsl:call-template>
        </xsl:variable>

        <tr>
            <th><xsl:value-of select="@name"/></th>
            <td>
                <a href="{$root}packages/{$link}.html">
                    <xsl:value-of select="@description" />
                </a>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="tag[@name = 'throws' or @name = 'throw']" mode="tabular">
        <tr>
            <th><xsl:apply-templates select="type" mode="contents" /></th>
            <td><xsl:value-of select="@description" disable-output-escaping="yes"/></td>
        </tr>
    </xsl:template>

    <xsl:template match="tag[@name='return']" mode="contents">
        <div class="subelement response">
            <xsl:apply-templates select="type" mode="contents" />
            <xsl:value-of select="@description" disable-output-escaping="yes" />
        </div>
    </xsl:template>

    <xsl:template match="namespace|package" mode="breadcrumb">
        <xsl:param name="active" select="'true'"/>

        <xsl:variable name="link">
            <xsl:call-template name="createLink">
                <xsl:with-param name="value" select="@full_name" />
            </xsl:call-template>
        </xsl:variable>

        <xsl:if test="local-name(..) = local-name()">
            <xsl:apply-templates select=".." mode="breadcrumb">
                <xsl:with-param name="active" select="'false'" />
            </xsl:apply-templates>
        </xsl:if>

        <li>
            <xsl:if test="$active = 'true'"><xsl:attribute name="class">active</xsl:attribute></xsl:if>
            <xsl:if test="local-name(..) = local-name()">
                <span class="divider">\</span>
            </xsl:if>
            <a href="{$root}{local-name()}s/{$link}.html"><xsl:value-of select="@name" /></a>
        </li>
    </xsl:template>

</xsl:stylesheet>
