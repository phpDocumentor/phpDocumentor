<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <xsl:template name="nav">
        <div id="tree">
            <ul>
                <xsl:call-template name="api"/>

                <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
                    <li>
                        <a href="#namespaces-" class="main_title">Namespaces</a>
                        <ul id="namespaces-">
                            <xsl:apply-templates select="/project/namespace">
                                <xsl:sort select="@name" />
                                <xsl:with-param name="parent_name" select="''" />
                            </xsl:apply-templates>
                        </ul>
                    </li>
                </xsl:if>

                <xsl:if test="count(/project/package) > 0">
                    <li>
                        <a href="#packages-" class="main_title">Packages</a>
                        <ul id="packages-">
                            <xsl:apply-templates select="/project/package">
                                <xsl:sort select="@name" />
                                <xsl:with-param name="parent_name" select="''"/>
                            </xsl:apply-templates>
                        </ul>
                    </li>
                </xsl:if>

                <li>
                    <a href="#files-" class="main_title">Files</a>
                    <ul id="files-">
                        <xsl:apply-templates select="/project/file">
                            <xsl:sort select="@path" />
                        </xsl:apply-templates>
                    </ul>
                </li>
            </ul>
        </div>

    </xsl:template>

    <xsl:template name="api">
        <xsl:if test="count(/project/file/*/docblock/tag[@name='api']|/project/file/class/*/docblock/tag[@name='api']|/project/file/interface/*/docblock/tag[@name='api']) > 0">
            <li>
                
                <a href="#api-" class="main_title">API</a>
                <ul id="api-" >
                    <xsl:for-each select="/project/file/*">
                        <xsl:sort select="./name" />

                        <xsl:if test="count(./*/docblock/tag[@name='api']) > 0">
                            <xsl:comment>Class|Interface level</xsl:comment>
                            <li>
                                <a class="{name()}" href="{$root}{../@generated-path}#{./full_name}" target="content">
                                    <xsl:value-of select="./full_name" />
                                </a>

                                <ul>
                                    <xsl:for-each select="./*/docblock/tag[@name='api']">
                                        <xsl:sort select="../../name" />
                                        <xsl:variable name="className" select="name(../..)" />
                                        <li>
                                            <xsl:choose>
                                                <xsl:when test="name(../..) = 'method'">
                                                    <a class="{$className} {../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}()" target="content">
                                                        <xsl:value-of select="../../name" />
                                                    </a>
                                                </xsl:when>
                                                <xsl:when test="name(../..) = 'constant'">
                                                    <a class="{$className} {../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
                                                        <xsl:value-of select="../../name" />
                                                    </a>
                                                </xsl:when>
                                                <xsl:when test="name(../..) = 'property'">
                                                    <a class="{$className} {../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
                                                        <xsl:value-of select="../../name" />
                                                    </a>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <a class="{$className}">
                                                        <xsl:value-of select="../../name" />
                                                    </a>
                                                </xsl:otherwise>
                                            </xsl:choose>
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
                                <li>
                                    <xsl:choose>
                                        <xsl:when test="name() = 'file'">
                                            <a class="{name()}" href="{$root}{../@generated-path}" target="content">
                                                <xsl:value-of select="./name" />
                                            </a>
                                        </xsl:when>
                                        <xsl:when test="name() = 'function'">
                                            <a class="{name()}" href="{$root}{../@generated-path}#{./full_name}::{./name}()" target="content">
                                                <xsl:value-of select="./name" />
                                            </a>
                                        </xsl:when>
                                        <xsl:when test="name() = 'class'">
                                            <a class="{name()}" href="{$root}{../@generated-path}#{./full_name}" target="content">
                                                <xsl:value-of select="./full_name" />
                                            </a>
                                        </xsl:when>
                                        <xsl:when test="name() = 'constant'">
                                            <a class="{name()}" href="{$root}{../@generated-path}#{./full_name}::{./name}" target="content">
                                                <xsl:value-of select="./name" />
                                            </a>
                                        </xsl:when>
                                        <xsl:when test="name() = 'property'">
                                            <a class="{name()}" href="{$root}{../@generated-path}#{./full_name}::{./name}" target="content">
                                                <xsl:value-of select="./name" />
                                            </a>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <a class="{$className}">
                                                <xsl:value-of select="./name" />
                                            </a>
                                    
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </li>
                            </xsl:if>
                        </xsl:if>
                    </xsl:for-each>
                </ul>
            </li>
        </xsl:if>
    </xsl:template>

    <xsl:template match="file">
        <li>
            <a href="{$root}{@generated-path}" target="content" class="file">
                <xsl:value-of select="@path" />
            </a>
            <xsl:variable name="file" select="@hash" />
            <ul id="files_{$file}" class="filetree">
                <xsl:for-each select="constant">
                    <li>
                        <a class="constant" href="{$root}{../@generated-path}#::{name}" target="content">
                            <xsl:value-of select="name" />
                            <br/>
                        </a>
                    </li>
                </xsl:for-each>
                <xsl:for-each select="function">
                    <li>
                        <a class="function" href="{$root}{../@generated-path}#::{name}()" target="content">
                            <xsl:value-of select="name" />
                            <br/>
                        </a>
                    </li>
                </xsl:for-each>
                <xsl:for-each select="class|interface">
                    <li>
                        <a class="{name()}" href="{$root}{../@generated-path}#{full_name}" target="content">
                            <xsl:value-of select="name" />
                            <br/>
                        </a>
                    </li>
                </xsl:for-each>
            </ul>
        </li>
    </xsl:template>

    <xsl:template match="subpackage">
        <xsl:variable name="package" select="../@name" />
        <li>
            <xsl:variable name="subpackage" select="." />
            <a href="">
                <xsl:if test="$subpackage=''">Default</xsl:if>
                <xsl:if test="not($subpackage='')">
                    <xsl:value-of select="$subpackage" />
                </xsl:if>
            </a>
            <ul id="packages_{$package}_{$subpackage}">

            <!-- List all functions whose file has a package which matches @name but no subpackage OR which have no package and $package is empty -->
                <xsl:for-each select="/project/file[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]/function">
                    <xsl:sort select="name" />
                    <li>
                        <a class="{name()}" href="{$root}{../@generated-path}#::{name}()" target="content">
                            <xsl:value-of select="name" />
                        </a>
                    </li>
                </xsl:for-each>

                <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]">
                    <xsl:sort select="name" />
                    <li>
                        <a class="{name()}" href="{$root}{../@generated-path}#{full_name}" target="content">
                            <xsl:value-of select="name" />
                        </a>
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
            <li>
                <a class="{name()}" href="{$root}{../@generated-path}#::{name}()"
                     target="content">
                    <xsl:value-of select="name"/>
                    <br/>
                    <small>
                        <xsl:value-of select="docblock/description"/>
                    </small>
                </a>
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
                <a class="{name()}" href="{$root}{../@generated-path}#{full_name}"
                     target="content">
                    <xsl:value-of select="name"/>
                </a>
            </li>
        </xsl:for-each>
    </xsl:template>

<!--
  <xsl:template match="package">
    <xsl:variable name="package" select="@name" />
    <xsl:if test="(count(/project/file/class[docblock/tag[@name='package'][@description=$package]]) > 0)
    or (count(/project/file/interface[docblock/tag[@name='package'][@description=$package]]) > 0)
    or (count(/project/file[docblock/tag[@name='package'][@description=$package]]/function) > 0)
    or ($package = '' and (
        (count(/project/file[not(docblock/tag[@name='package'])]/function) > 0)
        or (count(/project/file/class[not(docblock/tag[@name='package'])]) > 0)
        or (count(/project/file/interface[not(docblock/tag[@name='package'])]) > 0)
        ))
        ">
      <li class="closed">
          <xsl:if test="count(/project/package[@name != '']) > 1">
              <span class="folder">
                  <xsl:if test="@name=''">Default</xsl:if>
                  <xsl:if test="not(@name='')">
                    <xsl:value-of select="@name" />
                  </xsl:if>
              </span>
          </xsl:if>
        <ul id="packages_{$package}" class="filetree">
            <xsl:call-template name="package-content">
                <xsl:with-param name="package" select="$package" />
            </xsl:call-template>
        </ul>
      </li>

    </xsl:if>
  </xsl:template>
-->

    <xsl:template match="package">
        <xsl:param name="parent_name" />
        <xsl:variable name="full_name" select="concat($parent_name, @name)" />

        <xsl:if test="((count(/project/file/class[contains(@package, $full_name)]) > 0)
        or (count(/project/file/interface[contains(@package, $full_name)]) > 0)
        or (count(/project/file/function[contains(@package, $full_name)]) > 0))
      ">
            <li>
                <a href="#">
                    <xsl:if test="@name=''">\</xsl:if>
                    <xsl:if test="not(@name='')">
                        <xsl:value-of select="@name" />
                    </xsl:if>
                </a>
                <ul>
          <!-- process child packages -->
                    <xsl:apply-templates select="package">
                        <xsl:sort select="@name" />
                        <xsl:with-param name="parent_name" select="concat($full_name, '\')" />
                    </xsl:apply-templates>

                    <xsl:for-each select="/project/file/function[@package=$full_name]">
                        <xsl:sort select="name" />
                        <li>
                            <a class="{name()}" href="{$root}{../@generated-path}#::{name}()" target="content">
                                <xsl:value-of select="name" />
                            </a>
                        </li>
                    </xsl:for-each>

                    <xsl:for-each select="/project/file/class[@package=$full_name]|/project/file/interface[@package=$full_name]">
                        <xsl:sort select="name" />
                        <li>
                            <a class="{name()}" href="{$root}{../@generated-path}#{full_name}" target="content">
                                <xsl:value-of select="name" />
                            </a>
                        </li>
                    </xsl:for-each>
                </ul>
            </li>
        </xsl:if>
    </xsl:template>

    <xsl:template match="namespace">
        <xsl:param name="parent_name" />
        <xsl:variable name="full_name" select="concat($parent_name, @name)" />

        <xsl:if test="(count(namespace) > 0) or (count(/project/file/function[@namespace=$full_name]) > 0) or (count(/project/file/class[@namespace=$full_name]) > 0) or (count(/project/file/interface[@namespace=$full_name]) > 0)">
            <li>
                <a href="#">
                    <xsl:if test="@name=''">\</xsl:if>
                    <xsl:if test="not(@name='')">
                        <xsl:value-of select="@name" />
                    </xsl:if>
                </a>
                <ul>
          <!-- process child namespaces -->
                    <xsl:apply-templates select="namespace">
                        <xsl:sort select="@name" />
                        <xsl:with-param name="parent_name" select="concat($full_name, '\')" />
                    </xsl:apply-templates>

                    <xsl:for-each select="/project/file/function[@namespace=$full_name]">
                        <xsl:sort select="name" />
                        <li>
                            <a class="{name()}" href="{$root}{../@generated-path}#::{name}()" target="content">
                                <xsl:value-of select="name" />
                            </a>
                        </li>
                    </xsl:for-each>

                    <xsl:for-each select="/project/file/class[@namespace=$full_name]|/project/file/interface[@namespace=$full_name]">
                        <xsl:sort select="name" />
                        <li>
                            <a class="{name()}" href="{$root}{../@generated-path}#{full_name}" target="content">
                                <xsl:value-of select="name" />
                            </a>
                        </li>
                    </xsl:for-each>
                </ul>
            </li>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>
