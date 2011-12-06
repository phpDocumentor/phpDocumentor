<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />
  <xsl:include href="../old_ocean/search.xsl" />

  <xsl:template match="/project" name="frames_sidebar">
    <xsl:call-template name="search">
      <xsl:with-param name="root" select="$root" />
      <xsl:with-param name="search_template" select="$search_template" />
    </xsl:call-template>

    <input id="search_box" />

    <xsl:if test="count(/project/namespace[@name != 'default']) > 0">
    <div class="section">
      <h1>Namespaces</h1>
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
    <ul id="files_{$file}" class="treeview-docblox">
      <xsl:for-each select="class">
        <li>
          <span>
            <a href="{$root}/{../@generated-path}#{name}" target="content">
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
        <xsl:if test="not(@name='')"><xsl:value-of select="@name" /></xsl:if>
      </span>
      <xsl:variable name="package" select="@name" />
      <ul id="packages_{$package}" class="filetree">
        <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and not(docblock/tag[@name='subpackage'])]">
          <li class="closed">
            <span class="{name()}">
              <a href="{$root}{../@generated-path}#{name}" target="content">
                <xsl:value-of select="name" />
              </a><br />
              <small>
                <xsl:value-of select="docblock/description" />
              </small>
            </span>
          </li>
        </xsl:for-each>
        <xsl:for-each select="subpackage">
          <li class="closed">
          <xsl:variable name="subpackage" select="." />
          <span class="folder">
            <xsl:if test="$subpackage=''">Default</xsl:if>
            <xsl:if test="not($subpackage='')">
              <xsl:value-of select="$subpackage" />
            </xsl:if>
          </span>
          <ul id="packages_{$package}_{$subpackage}" class="treeview-docblox">
            <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]|/project/file/interface[docblock/tag[@name='package'][@description=$package] and docblock/tag[@name='subpackage'][@description=$subpackage]]">
            <li class="{name()}">
              <span>
                <a href="{$root}{../@generated-path}#{name}" target="content">
                  <xsl:value-of select="name" />
                </a><br />
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
          <li>
            <span class="{name()}">
              <a href="{$root}{../@generated-path}#{name}" target="content">
                <xsl:value-of select="name" />
              </a><br />
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