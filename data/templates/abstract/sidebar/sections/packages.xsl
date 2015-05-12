<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html"/>

    <xsl:template name="sidebar-section-packages">
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="/project/package">
                <xsl:sort select="@name"/>
                <xsl:with-param name="parent_name" select="''"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

    <xsl:template match="subpackage">
        <xsl:variable name="package" select="../@name" />
        <li class="closed">
          <xsl:variable name="subpackage" select="." />
          <span class="package folder">
            <xsl:if test="$subpackage=''">Default</xsl:if>
            <xsl:if test="not($subpackage='')">
              <xsl:value-of select="$subpackage" />
            </xsl:if>
          </span>
          <ul id="packages_{$package}_{$subpackage}" class="filetree">

            <!-- List all functions whose file has a package which matches @name but no subpackage OR which have no package and $package is empty -->
            <xsl:for-each select="/project/file[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]/function">
              <xsl:sort select="name" />
              <li class="closed">
                <span class="{name()}">
                  <a href="{$root}files/{../@generated-path}#::{name}()" target="content">
                    <xsl:value-of select="name" />
                  </a>
                </span>
              </li>
            </xsl:for-each>

            <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]">
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
    </xsl:template>

  <xsl:template name="package-content">
      <xsl:param name="package"/>

      <!-- List all subpackages and their classes -->
      <xsl:for-each select="subpackage">
          <xsl:sort select="."/>
          <xsl:apply-templates select="."/>
      </xsl:for-each>

      <!-- List all functions whose file has a package which matches @name but no subpackage OR which have no package and $package is empty -->
      <xsl:for-each select="
          /project/file[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]/function
          |/project/file[not(docblock/tag[@name='package']) and $package='']/function">
          <xsl:sort select="name"/>
          <li class="closed">
              <span class="{name()}">
                  <a href="{$root}files/{../@generated-path}#::{name}()"
                     target="content">
                      <xsl:value-of select="name"/>
                      <br/>
                      <small>
                          <xsl:value-of select="docblock/description"/>
                      </small>
                  </a>
              </span>
          </li>
      </xsl:for-each>

      <!-- List all classes that have a package which matches @name but no subpackage OR which have no package and $package is empty -->
      <xsl:for-each select="
            /project/file/class[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]
            |/project/file/interface[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]
            |/project/file/class[not(docblock/tag[@name='package']) and $package='']
            |/project/file/interface[not(docblock/tag[@name='package']) and $package='']">
          <xsl:sort select="name"/>
          <li class="closed">
              <span class="{name()}">
                  <a href="{$root}files/{../@generated-path}#{full_name}"
                     target="content">
                      <xsl:value-of select="name"/>
                  </a>
              </span>
          </li>
      </xsl:for-each>
  </xsl:template>

  <xsl:template match="package">
    <xsl:param name="parent_name" />
    <xsl:variable name="full_name" select="concat($parent_name, @name)" />

      <xsl:if test="((count(/project/file/class[contains(@package, $full_name)]) > 0)
        or (count(/project/file/interface[contains(@package, $full_name)]) > 0)
        or (count(/project/file/function[contains(@package, $full_name)]) > 0))
      ">
      <li class="closed">
        <span class="package folder">
          <xsl:if test="@name=''">\</xsl:if>
          <xsl:if test="not(@name='')">
            <xsl:value-of select="@name" />
          </xsl:if>
        </span>
        <ul>
          <!-- process child packages -->
          <xsl:apply-templates select="package">
            <xsl:sort select="@name" />
            <xsl:with-param name="parent_name" select="concat($full_name, '\')" />
          </xsl:apply-templates>

          <xsl:for-each select="/project/file/function[@package=$full_name]">
            <xsl:sort select="name" />
            <li>
              <span class="{name()}">
                <a href="{$root}files/{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
            </li>
          </xsl:for-each>

          <xsl:for-each select="/project/file/class[@package=$full_name]|/project/file/interface[@package=$full_name]">
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