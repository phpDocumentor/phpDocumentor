<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="sidebar-section-files">
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="/project/file">
                <xsl:sort select="@path"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

    <xsl:template match="/project/file">
      <li class="closed">
        <span class="file">
            <a href="{$root}files/{@generated-path}" target="content">
                <xsl:value-of select="@path" />
            </a>
        </span>
        <xsl:if test="$section.files.show-elements != false">
            <xsl:variable name="file" select="@hash" />
            <ul id="files_{$file}" class="filetree">
              <xsl:for-each select="constant">
                <li>
                  <span class="constant">
                    <a href="{$root}files/{../@generated-path}#::{name}" target="content">
                      <xsl:value-of select="name" /><br/>
                    </a>
                  </span>
                </li>
              </xsl:for-each>
              <xsl:for-each select="function">
                <li>
                  <span class="function">
                    <a href="{$root}files/{../@generated-path}#::{name}()" target="content">
                      <xsl:value-of select="name" /><br/>
                    </a>
                  </span>
                </li>
              </xsl:for-each>
              <xsl:for-each select="class|interface">
                <li>
                  <span class="{name()}">
                    <a href="{$root}files/{../@generated-path}#{full_name}" target="content">
                      <xsl:value-of select="name" /><br/>
                    </a>
                  </span>
                </li>
              </xsl:for-each>
            </ul>
        </xsl:if>
      </li>
    </xsl:template>

</xsl:stylesheet>