<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/project">
    <div id="accordion">
        <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
            <h1><a href="#">Namespaces</a></h1>
            <div style="padding: 0px;">
                <!--<input type="checkbox" onclick="$('.treeview-docblox small').toggle(!this.checked);" checked="">Compact view<br />-->
                <ul id="namespaces-" class="filetree">
                  <xsl:apply-templates select="/project/namespace">
                    <xsl:sort select="@name" />
                    <xsl:with-param name="parent_name" select="''" />
                  </xsl:apply-templates>
                </ul>
            </div>
        </xsl:if>

        <xsl:if test="count(/project/package) > 0">
            <h1><a href="#">Packages</a></h1>
            <div style="padding: 0px;">
                <xsl:if test="count(/project/package) &lt; 2">
                <ul id="packages-" class="deadtree">
                  <xsl:apply-templates select="/project/package">
                    <xsl:sort select="@name" />
                  </xsl:apply-templates>
                </ul>
                </xsl:if>

                <xsl:if test="count(/project/package) > 1">
                <ul id="packages-" class="filetree">
                  <xsl:apply-templates select="/project/package">
                    <xsl:sort select="@name" />
                  </xsl:apply-templates>
                </ul>
                </xsl:if>
            </div>
        </xsl:if>

        <!--<h1><a href="#">Files</a></h1>-->
        <!--<div>-->
            <!--<ul id="files-" class="filetree">-->
            <!--<xsl:apply-templates select="file">-->
                <!--<xsl:sort select="@path" />-->
            <!--</xsl:apply-templates>-->
            <!--</ul>-->
        <!--</div>-->
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
                  <small>
                      <xsl:value-of select="docblock/description"/>
                  </small>
                </a>
              </span>
            </li>
          </xsl:for-each>
          <xsl:for-each select="function">
            <li>
              <span class="function">
                <a href="{$root}{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" /><br/>
                  <small>
                      <xsl:value-of select="docblock/description"/>
                  </small>
                </a>
              </span>
            </li>
          </xsl:for-each>
          <xsl:for-each select="class|interface">
            <li>
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#{full_name}" target="content">
                  <xsl:value-of select="name" /><br/>
                  <small>
                      <xsl:value-of select="docblock/description"/>
                  </small>
                </a>
              </span>
            </li>
          </xsl:for-each>
        </ul>
      </li>
  </xsl:template>

  <xsl:template match="package">
    <xsl:variable name="package" select="@name" />
    <xsl:if test="(count(/project/file/class[docblock/tag[@name='package'][@description=$package]]) > 0)
    or (count(/project/file/interface[docblock/tag[@name='package'][@description=$package]]) > 0)
    or (count(/project/file[docblock/tag[@name='package'][@description=$package]]/function) > 0)
    or ($package = '')">
      <li class="closed">
        <span class="folder">
          <xsl:if test="@name=''">Default</xsl:if>
          <xsl:if test="not(@name='')">
            <xsl:value-of select="@name" />
          </xsl:if>
        </span>
        <ul id="packages_{$package}" class="filetree">

          <!-- List all subpackages and their classes -->
          <xsl:for-each select="subpackage">
            <xsl:sort select="." />
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
                        <br />
                        <small>
                          <xsl:value-of select="docblock/description" />
                        </small>
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
                        <br />
                        <small>
                          <xsl:value-of select="docblock/description" />
                        </small>
                      </a>
                    </span>
                  </li>
                </xsl:for-each>

              </ul>
            </li>
          </xsl:for-each>

          <!-- List all functions whose file has a package which matches @name but no subpackage OR which have no package and $package is empty -->
          <xsl:for-each select="
          /project/file[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]/function
          |/project/file[not(docblock/tag[@name='package']) and $package='']/function">
            <xsl:sort select="name" />
            <li class="closed">
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#::{name}()" target="content">
                  <xsl:value-of select="name" />
                  <br />
                  <small>
                    <xsl:value-of select="docblock/description" />
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
            <xsl:sort select="name" />
            <li class="closed">
              <span class="{name()}">
                <a href="{$root}{../@generated-path}#{full_name}" target="content">
                  <xsl:value-of select="name" />
                  <br />
                  <small>
                    <xsl:value-of select="docblock/description" />
                  </small>
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
                  <br />
                  <small>
                    <xsl:value-of select="docblock/description" />
                  </small>
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
                  <br />
                  <small>
                    <xsl:value-of select="docblock/description" />
                  </small>
                </a>
              </span>
            </li>
          </xsl:for-each>
        </ul>
      </li>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>
