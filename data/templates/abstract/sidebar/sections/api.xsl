<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="sidebar-section-api">
        <div style="padding: 0px;">
            <ul id="api-" class="filetree sidebar-nav-tree">
            <xsl:for-each select="/project/file/*">
                <xsl:sort select="./name" />

                <xsl:if test="count(./*/docblock/tag[@name='api']) > 0">
                    <xsl:comment>Class|Interface level</xsl:comment>
                    <li class="closed">
                        <span class="{name()}">
                            <a href="{$root}files/{../@generated-path}#{./full_name}" target="content">
                                <xsl:value-of select="./full_name" />
                            </a>
                        </span>

                        <ul class="filetree">
                        <xsl:for-each select="./*/docblock/tag[@name='api']">
                            <xsl:sort select="../../name" />
                            <xsl:variable name="className" select="name(../..)" />
                            <li>
                            <span class="{$className}">
                            <xsl:choose>
                                <xsl:when test="name(../..) = 'method'">
                                    <a class="{../../@visibility}" href="{$root}files/{../../../../@generated-path}#{../../../full_name}::{../../name}()" target="content">
                                    <xsl:value-of select="../../name" />
                                    </a>
                                </xsl:when>
                                <xsl:when test="name(../..) = 'constant'">
                                    <a class="{../../@visibility}" href="{$root}files/{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
                                    <xsl:value-of select="../../name" />
                                    </a>
                                </xsl:when>
                                <xsl:when test="name(../..) = 'property'">
                                    <a class="{../../@visibility}" href="{$root}files/{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
                                    <xsl:value-of select="../../name" />
                                    </a>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="../../name" />
                                </xsl:otherwise>
                            </xsl:choose>
                            </span>
                            </li>
                        </xsl:for-each>
                        </ul>
                    </li>
                </xsl:if>

                <xsl:if test="count(./docblock/tag[@name='api']) > 0">
                    <xsl:comment>File level</xsl:comment>
                    <xsl:if test="
                    not(((name() = 'class') and count(./*/docblock/tag[@name='api']) > 0))
                    and
                    not(((name() = 'interface') and count(./*/docblock/tag[@name='api']) > 0))">
                    <li class="closed">
                        <span class="{name()}">
                        <xsl:choose>
                            <xsl:when test="name() = 'file'">
                                <a href="{$root}files/{../@generated-path}" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'function'">
                                <a href="{$root}files/{../@generated-path}#{./full_name}::{./name}()" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'class'">
                                <a href="{$root}files/{../@generated-path}#{./full_name}" target="content">
                                    <xsl:value-of select="./full_name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'constant'">
                                <a href="{$root}files/{../@generated-path}#{./full_name}::{./name}" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'property'">
                                <a href="{$root}files/{../@generated-path}#{./full_name}::{./name}" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="./name" />
                            </xsl:otherwise>
                        </xsl:choose>
                        </span>
                    </li>
                    </xsl:if>
                </xsl:if>
            </xsl:for-each>
            </ul>
        </div>
    </xsl:template>

</xsl:stylesheet>
