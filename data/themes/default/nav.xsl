<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <xsl:template match="/project">
        <script type="text/javascript" src="{$root}js/sidebar.js"></script>

        <div id="sidebar">
            <div id="sidebar-header">
                <xsl:call-template name="sidebar-header"/>
            </div>

            <div id="sidebar-nav">
                <xsl:call-template name="sidebar-sections"/>
            </div>

            <div id="sidebar-footer"><xsl:call-template name="sidebar-footer"/></div>
        </div>

  </xsl:template>

    <xsl:template name="sidebar-sections">
        <h3 id="sidebar-dashboard">
            <img class="icon" src="{$root}css/docblox/images/icons/dashboard.png" alt="Dashboard"/>
            <a href="{$root}content.html" class="link" target="content">Dashboard</a>
        </h3>
        <div style="display: none"></div>

        <xsl:if test="count(/project/file/*/docblock/tag[@name='api']|/project/file/class/*/docblock/tag[@name='api']|/project/file/interface/*/docblock/tag[@name='api']) > 0">
        <h3 id="sidebar-api">
            <img src="{$root}css/docblox/images/icons/book.png" alt="API" />
            <a href="#api">API</a>
        </h3>
        <div class="sidebar-section">
            <xsl:call-template name="sidebar-api"/>
        </div>
        </xsl:if>

        <h3 id="sidebar-files">
            <img src="{$root}css/docblox/images/icons/files.png" alt="Files"/>
            <a href="#files">Files</a>
        </h3>
        <div class="sidebar-section">
            <xsl:call-template name="sidebar-section-files"/>
        </div>

        <xsl:if test="count(/project/package) > 0">
        <h3 id="sidebar-packages">
            <img src="{$root}css/docblox/images/icons/packages.png" alt="Packages"/>
            <a href="#packages">Packages</a>
        </h3>
        <div class="sidebar-section sidebar-tree">
            <xsl:call-template name="sidebar-section-packages"/>
        </div>
        </xsl:if>

        <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
        <h3 id="sidebar-namespaces">
            <img src="{$root}css/docblox/images/icons/namespaces.png" alt="Namespaces"/>
            <a href="#namespaces">Namespaces</a>
        </h3>
        <div class="sidebar-section sidebar-tree">
            <xsl:call-template name="sidebar-section-namespaces"/>
        </div>
        </xsl:if>

        <h3 id="sidebar-charts">
            <img src="{$root}css/docblox/images/icons/chart.png" alt="Charts"/>
            <a href="#charts">Charts</a>
        </h3>
        <div class="sidebar-section">
            <ul style="list-style-image: url('css/docblox/images/icons/chart15x12.png');">
                <xsl:call-template name="sidebar-section-charts"/>
            </ul>
        </div>

        <h3 id="sidebar-reports">
            <img src="{$root}css/docblox/images/icons/reports.png" alt="Reports"/>
            <a href="#reports">Reports</a>
        </h3>
        <div class="sidebar-section">
            <ul style="list-style-image: url('css/docblox/images/icons/reports9x12.png');">
                <xsl:call-template name="sidebar-section-reports"/>
            </ul>
        </div>
    </xsl:template>

    <xsl:template name="sidebar-header">
        <xsl:if test="not($title)">
            <img src="images/icon48x48.png" id="sidebar-logo" alt="Logo" />
        </xsl:if>
        <h1>
            <xsl:if test="not($title)">DocBlox</xsl:if>
            <xsl:if test="$title"><xsl:value-of select="$title" disable-output-escaping="yes" /></xsl:if>
        </h1>
        <div style="clear: both"></div>
    </xsl:template>

    <xsl:template name="sidebar-section-tree-search">
        <div class="search-bar">
            <a href="#" onclick="$(this).parent().next().find('.collapsable-hitarea').click(); return false;">
                <img src="images/collapse_all.png" title="Collapse all" alt="Collapse all" />
            </a>
            <a href="#" onclick="$(this).parent().next().find('.expandable-hitarea').click(); return false;">
                <img src="images/expand_all.png" title="Expand all" alt="Expand all" />
            </a>
            <div>
                <input type="text" onkeyup="tree_search(this);" />
            </div>
        </div>
    </xsl:template>

    <xsl:template name="sidebar-section-files">
        <xsl:call-template name="sidebar-section-tree-search"/>
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="file">
                <xsl:sort select="@path"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

    <xsl:template name="sidebar-section-namespaces">
        <xsl:call-template name="sidebar-section-tree-search"/>
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="/project/namespace">
                <xsl:sort select="@name"/>
                <xsl:with-param name="parent_name" select="''"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

    <xsl:template name="sidebar-section-packages">
        <xsl:call-template name="sidebar-section-tree-search"/>
        <ul class="sidebar-nav-tree">
            <xsl:apply-templates select="/project/package">
                <xsl:sort select="@name"/>
                <xsl:with-param name="parent_name" select="''"/>
            </xsl:apply-templates>
        </ul>
    </xsl:template>

    <xsl:template name="sidebar-section-charts">
        <li><a href="{$root}graph.html" target="content">Class Inheritance Diagram</a></li>
    </xsl:template>

    <xsl:template name="sidebar-section-reports">
        <li><a href="{$root}report_markers.html" target="content">Markers (TODO/FIXME)</a></li>
        <li><a href="{$root}report_parse_markers.html" target="content">Parsing errors</a></li>
        <li><a href="{$root}report_deprecated.html" target="content">Deprecated elements</a></li>
    </xsl:template>

    <xsl:template name="sidebar-footer">
        Documentation is generated by <a href="http://docblox-project.org" target="_top">
        <img src="{$root}images/icon48x48.png" height="12" align="top" alt="Logo"/> DocBlox <xsl:value-of select="$version" />
        </a>, images courtesy of <a href="http://glyphish.com/" target="_top">Glyphish</a>
    </xsl:template>

    <xsl:template name="sidebar-api">
        <div style="padding: 0px;">
            <ul id="api-" class="filetree">
            <xsl:for-each select="/project/file/*">
                <xsl:sort select="./name" />

                <xsl:if test="count(./*/docblock/tag[@name='api']) > 0">
                    <xsl:comment>Class|Interface level</xsl:comment>
                    <li class="closed">
                        <span class="{name()}">
                            <a href="{$root}{../@generated-path}#{./full_name}" target="content">
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
                                    <a class="{../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}()" target="content">
                                    <xsl:value-of select="../../name" />
                                    </a>
                                </xsl:when>
                                <xsl:when test="name(../..) = 'constant'">
                                    <a class="{../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
                                    <xsl:value-of select="../../name" />
                                    </a>
                                </xsl:when>
                                <xsl:when test="name(../..) = 'property'">
                                    <a class="{../../@visibility}" href="{$root}{../../../../@generated-path}#{../../../full_name}::{../../name}" target="content">
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
                                <a href="{$root}{../@generated-path}" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'function'">
                                <a href="{$root}{../@generated-path}#{./full_name}::{./name}()" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'class'">
                                <a href="{$root}{../@generated-path}#{./full_name}" target="content">
                                    <xsl:value-of select="./full_name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'constant'">
                                <a href="{$root}{../@generated-path}#{./full_name}::{./name}" target="content">
                                    <xsl:value-of select="./name" />
                                </a>
                            </xsl:when>
                            <xsl:when test="name() = 'property'">
                                <a href="{$root}{../@generated-path}#{./full_name}::{./name}" target="content">
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

  <xsl:template match="file">
      <li class="closed">
        <span class="file">
            <a href="{$root}{@generated-path}" target="content">
                <xsl:value-of select="@path" />
            </a>
        </span>
        <xsl:variable name="file" select="@hash" />
        <ul id="files_{$file}" class="filetree">
          <xsl:for-each select="constant">
            <li>
              <span class="constant">
                <a href="{$root}{../@generated-path}#::{name}" target="content">
                  <xsl:value-of select="name" /><br/>
                </a>
              </span>
            </li>
          </xsl:for-each>
          <xsl:for-each select="function">
            <li>
              <span class="function">
                <a href="{$root}{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" /><br/>
                </a>
              </span>
            </li>
          </xsl:for-each>
          <xsl:for-each select="class|interface">
            <li>
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#{full_name}" target="content">
                  <xsl:value-of select="name" /><br/>
                </a>
              </span>
            </li>
          </xsl:for-each>
        </ul>
      </li>
  </xsl:template>

    <xsl:template match="subpackage">
        <xsl:variable name="package" select="../@name" />
        <li class="closed">
          <xsl:variable name="subpackage" select="." />
          <span class="folder">
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
                  <a href="{$root}{../@generated-path}#::{name}()" target="content">
                    <xsl:value-of select="name" />
                  </a>
                </span>
              </li>
            </xsl:for-each>

            <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]">
              <xsl:sort select="name" />
              <li>
                <span class="{name()}">
                  <a href="{$root}{../@generated-path}#{full_name}" target="content">
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
                  <a href="{$root}{../@generated-path}#::{name}()"
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
                  <a href="{$root}{../@generated-path}#{full_name}"
                     target="content">
                      <xsl:value-of select="name"/>
                  </a>
              </span>
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
      <li class="closed">
        <span class="folder">
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
                <a href="{$root}{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
            </li>
          </xsl:for-each>

          <xsl:for-each select="/project/file/class[@package=$full_name]|/project/file/interface[@package=$full_name]">
            <xsl:sort select="name" />
            <li>
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#{full_name}" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
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
      <li class="closed">
        <span class="folder">
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
                <a href="{$root}{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" />
                </a>
              </span>
            </li>
          </xsl:for-each>

          <xsl:for-each select="/project/file/class[@namespace=$full_name]|/project/file/interface[@namespace=$full_name]">
            <xsl:sort select="name" />
            <li>
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#{full_name}" target="content">
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
