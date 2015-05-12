<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
      <div class="section">
        <h1>Namespaces</h1>
        <!--<input type="checkbox" onclick="$('.treeview-phpdoc small').toggle(!this.checked);" checked="">Compact view<br />-->
        <ul id="namespaces-" class="filetree">
          <xsl:apply-templates select="/project/namespace">
            <xsl:sort select="@name" />
            <xsl:with-param name="parent_name" select="''" />
          </xsl:apply-templates>
        </ul>
      </div>
    </xsl:if>

    <xsl:if test="count(/project/package) > 0">
      <div class="section">
        <h1>Packages</h1>

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

    <!--<h1>Files</h1>-->
    <!--<div id="file">-->
    <!--<xsl:apply-templates select="file">-->
    <!--<xsl:sort select="@path" />-->
    <!--</xsl:apply-templates>-->
    <!--</div>-->
  </xsl:template>

  <xsl:template match="file">
    <h2>
      <xsl:value-of select="@path" />
    </h2>
    <xsl:variable name="file" select="@hash" />
    <ul id="files_{$file}" class="treeview-phpdoc">
      <xsl:for-each select="class">
        <li>
          <span>
            <a href="{$root}/{../@generated-path}#{name}">
              <xsl:value-of select="name" />
            </a>
          </span>
          <small>
            <xsl:value-of select="docblock/description" />
          </small>
        </li>
      </xsl:for-each>
    </ul>
  </xsl:template>

  <xsl:template match="package">
    <li class="closed">
      <span class="folder">
        <xsl:if test="@name=''">Default</xsl:if>
        <xsl:if test="not(@name='')">
          <xsl:value-of select="@name" />
        </xsl:if>
      </span>
      <xsl:variable name="package" select="@name" />
      <ul id="packages_{$package}" class="filetree">

        <!-- List all classes that have a package which matches @name but no subpackage OR which have no package and $package is empty -->
        <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]|/project/file/class[not(docblock/package) and $package='']|/project/file/interface[not(docblock/package) and $package='']">
          <xsl:sort select="name"/>
          <li class="closed">
            <span class="{name()}">
              <a href="{$root}{../@generated-path}#{name}">
                <xsl:value-of select="name" />
              </a>
              <br />
              <small>
                <xsl:value-of select="docblock/description" />
              </small>
            </span>
          </li>
        </xsl:for-each>

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
              <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]|/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]">
                <xsl:sort select="name" />
                <li class="{name()}">
                  <span>
                    <a href="{$root}{../@generated-path}#{name}">
                      <xsl:value-of select="name" />
                    </a>
                    <br />
                    <small>
                      <xsl:value-of select="docblock/description" />
                    </small>
                  </span>
                </li>
              </xsl:for-each>
            </ul>
          </li>
        </xsl:for-each>
      </ul>
    </li>
  </xsl:template>

  <xsl:template match="namespace">
    <xsl:param name="parent_name" />
    <xsl:variable name="full_name" select="concat($parent_name, @name)" />

    <li class="closed">
      <span class="folder">
        <xsl:if test="@name=''">Default</xsl:if>
        <xsl:if test="not(@name='')">
          <xsl:value-of select="@name" />
        </xsl:if>
      </span>
      <ul>
        <xsl:for-each select="/project/file/class[@namespace=$full_name]|/project/file/interface[@namespace=$full_name]">
          <xsl:sort select="name" />
          <li>
            <span class="{name()}">
              <a href="{$root}{../@generated-path}#{name}">
                <xsl:value-of select="name" />
              </a>
              <br />
              <small>
                <xsl:value-of select="docblock/description" />
              </small>
            </span>
          </li>
        </xsl:for-each>
        <xsl:apply-templates select="namespace">
          <xsl:sort select="@name" />
          <xsl:with-param name="parent_name" select="concat($full_name, '\')" />
        </xsl:apply-templates>
      </ul>
    </li>
  </xsl:template>

</xsl:stylesheet>