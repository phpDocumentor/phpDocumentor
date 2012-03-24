<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />
    <xsl:include href="../layout.xsl" />

    <xsl:template match="/project" mode="contents">

        <div class="row">
            <div class="span4">
                <ul class="side-nav nav nav-list">
                    <li class="nav-header">Navigation</li>
                    <xsl:for-each select="/project/file">
                        <xsl:if test="markers|.//docblock/tag[@name='todo']|.//docblock/tag[@name='fixme']">
                        <li>
                            <a href="#{@path}">
                                <i class="icon-file"></i> <xsl:value-of select="@path"/>
                            </a>
                        </li>
                        </xsl:if>
                    </xsl:for-each>
                </ul>
            </div>

            <div class="span8">

                <ul class="breadcrumb">
                    <li><a href="{$root}"><i class="icon-map-marker"></i></a><span class="divider">\</span></li>
                    <li>Markers</li>
                </ul>

                <xsl:if test="count(/project/file/markers|//docblock/tag[@name='todo']|//docblock/tag[@name='fixme']) &lt; 1">
                    <div class="alert alert-info">No markers have been found in this project.</div>
                </xsl:if>

                <xsl:if test="count(/project/marker) > 0">
                    <div class="alert alert-info">
                        The following markers were found:
                        <ul>
                            <xsl:apply-templates select="/project/marker" mode="report-overview" />
                        </ul>
                    </div>
                </xsl:if>

                <div id="marker-accordion">
                    <xsl:for-each select="/project/file">
                        <xsl:if test="markers|.//docblock/tag[@name='todo']|.//docblock/tag[@name='fixme']">
                            <div class="package-contents">
                                <a name="{@path}" id="{@path}"></a>
                                <h3>
                                <i class="icon-file"></i>
                                    <xsl:value-of select="@path" />
                                    <small style="float: right;padding-right: 10px;">
                                        <xsl:value-of select="count(markers/*|.//docblock/tag[@name='todo']|.//docblock/tag[@name='fixme'])" />
                                    </small>
                                </h3>
                                <div>
                                    <table class="table markers table-bordered">
                                        <tr>
                                            <th>Type</th>
                                            <th>Line</th>
                                            <th>Description</th>
                                        </tr>
                                        <xsl:for-each select="markers/*|.//docblock/tag[@name='todo']|.//docblock/tag[@name='fixme']">
                                            <xsl:sort select="line" />
                                            <tr>
                                                <xsl:if test="name() = 'tag'">
                                                    <td>
                                                        <xsl:value-of select="@name" />
                                                    </td>
                                                </xsl:if>
                                                <xsl:if test="name() != 'tag'">
                                                    <td>
                                                        <xsl:value-of select="name()" />
                                                    </td>
                                                </xsl:if>
                                                <td>
                                                    <xsl:value-of select="@line" />
                                                </td>
                                                <xsl:if test="name() = 'tag'">
                                                    <td>
                                                        <xsl:value-of select="@description" disable-output-escaping="yes" />
                                                    </td>
                                                </xsl:if>
                                                <xsl:if test="name() != 'tag'">
                                                    <td>
                                                        <xsl:value-of select="." disable-output-escaping="yes" />
                                                    </td>
                                                </xsl:if>
                                            </tr>
                                        </xsl:for-each>
                                    </table>
                                </div>
                            </div>
                        </xsl:if>
                    </xsl:for-each>
                </div>
            </div>

        </div>
    </xsl:template>

</xsl:stylesheet>