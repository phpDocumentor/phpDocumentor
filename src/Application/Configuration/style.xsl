<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" indent="yes" />
    <xsl:template match="/">
        <phpdocumentor
                version="3"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.phpdoc.org"
                xsi:noNamespaceSchemaLocation="phpdoc.xsd"
        >
            <paths>
                <xsl:choose>
                    <xsl:when test="phpdocumentor/parser/target != ''">
                        <output><xsl:value-of select="phpdocumentor/parser/target"/></output>
                    </xsl:when>
                    <xsl:otherwise>
                        <output>file://build/docs</output>
                    </xsl:otherwise>
                </xsl:choose>
                <cache>/tmp/phpdoc-doc-cache</cache>
            </paths>
            <version number="1.0.0">
                <folder></folder>
                <api format="php">
                    <source dsn="file://.">
                        <path>src</path>
                    </source>

                    <xsl:variable name="ignoreHidden">
                        <xsl:choose>
                            <xsl:when test="phpdocumentor/parser/files/ignore-hidden != ''">
                                <xsl:value-of select="phpdocumentor/parser/files/ignore-hidden"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="'true'"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:variable>

                    <xsl:variable name="ignoreSymlinks">
                        <xsl:choose>
                            <xsl:when test="phpdocumentor/parser/files/ignore-symlinks != ''">
                                <xsl:value-of select="phpdocumentor/parser/files/ignore-symlinks"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:value-of select="'true'"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:variable>

                    <ignore hidden="{$ignoreHidden}" symlinks="{$ignoreSymlinks}">
                        <xsl:for-each select="phpdocumentor/parser/files/directory">
                            <path><xsl:value-of select="text()"/></path>
                        </xsl:for-each>
                    </ignore>

                    <extensions>
                        <xsl:for-each select="phpdocumentor/parser/extensions/extension">
                            <extension><xsl:value-of select="text()"/></extension>
                        </xsl:for-each>
                    </extensions>

                    <xsl:choose>
                        <xsl:when test="phpdocumentor/parser/visibility != ''">
                            <visibility><xsl:value-of select="text()"/></visibility>
                        </xsl:when>
                        <xsl:otherwise>
                            <visibility>public</visibility>
                        </xsl:otherwise>
                    </xsl:choose>

                    <xsl:choose>
                        <xsl:when test="phpdocumentor/parser/default-package-name != ''">
                            <default-package-name><xsl:value-of select="phpdocumentor/parser/default-package-name"/></default-package-name>
                        </xsl:when>
                        <xsl:otherwise>
                            <default-package-name>Default</default-package-name>
                        </xsl:otherwise>
                    </xsl:choose>

                    <markers>
                        <xsl:for-each select="phpdocumentor/parser/markers/item">
                            <marker><xsl:value-of select="text()"/></marker>
                        </xsl:for-each>
                    </markers>
                </api>

                <guide format="rst">
                    <source dsn="file://../phpDocumentor/phpDocumentor2">
                        <path>docs</path>
                    </source>
                </guide>
            </version>

            <xsl:for-each select="phpdocumentor/transformations/template/@name">
                <template><xsl:copy-of select="current()"/></template>
            </xsl:for-each>

        </phpdocumentor>
    </xsl:template>
</xsl:stylesheet>
