<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="sidebar-section-namespaces">
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="/project/namespace">
                <xsl:sort select="@name"/>
                <xsl:with-param name="parent_name" select="''"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

  <xsl:template match="namespace">
    <xsl:param name="parent_name" />
    <xsl:variable name="full_name" select="concat($parent_name, @name)" />

    <xsl:if test="(count(namespace) > 0) or (count(/project/file/function[@namespace=$full_name]) > 0) or (count(/project/file/class[@namespace=$full_name]) > 0) or (count(/project/file/interface[@namespace=$full_name]) > 0)">
      <li class="closed">
        <span class="namespace folder">
          <xsl:if test="@name=''">\</xsl:if>
          <xsl:if test="not(@name='')">
            <xsl:value-of select="@name" />
          </xsl:if>
        </span>
        <ul>
          <!-- process child namespaces -->
          <xsl:apply-templates select="namespace">
            <xsl:sort select="@name" />
            <xsl:with-param name="parent_name" select="concat($full_name, '\')" />
          </xsl:apply-templates>

          <xsl:for-each select="/project/file/function[@namespace=$full_name]">
            <xsl:sort select="name" />
            <li>
              <span class="{name()}">
                <a href="{$root}files/{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
            </li>
          </xsl:for-each>

          <xsl:for-each select="/project/file/class[@namespace=$full_name]|/project/file/interface[@namespace=$full_name]">
            <xsl:sort select="name" />
            <li>
              <span class="{name()}">
                <a href="{$root}files/{../@generated-path}#{full_name}" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
            </li>
          </xsl:for-each>
        </ul>
      </li>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>