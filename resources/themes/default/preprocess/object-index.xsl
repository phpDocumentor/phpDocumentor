<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <xsl:if test="count(/project/namespace) > 0">
    <h1>Namespaces</h1>
    <!--<input type="checkbox" onclick="$('.treeview-docblox small').toggle(!this.checked);" checked="">Compact view<br />-->

    <ul id="namespaces-" class="treeview-docblox">
      <xsl:apply-templates select="/project/namespace">
        <xsl:sort select="@name" />
        <xsl:with-param name="parent_name" select="''" />
      </xsl:apply-templates>
    </ul>
    </xsl:if>

    <h1>Packages</h1>
    <ul id="packages-" class="treeview-docblox">
      <xsl:apply-templates select="/project/package">
        <xsl:sort select="@name" />
      </xsl:apply-templates>
    </ul>

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
          <div class="content">
            <span>
              <a href="{$root}/{../@generated-path}#{name}">
                <xsl:value-of select="name" />
              </a>
            </span>
            <small>
              <xsl:value-of select="docblock/description" />
            </small>
          </div>
        </li>
      </xsl:for-each>
    </ul>
  </xsl:template>

  <xsl:template match="package">
    <li class="package closed">
      <div class="content">
        <span>
          <xsl:if test="@name=''">Default</xsl:if>
          <xsl:if test="not(@name='')"><xsl:value-of select="@name" /></xsl:if>
        </span>
      </div>
      <xsl:variable name="package" select="@name" />
      <ul id="packages_{$package}" class="treeview-docblox">
        <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][.=$package] and not(docblock/tag[@name='subpackage'])]">
          <li class="closed">
            <div class="content">
              <span>
                <a href="{$root}/{../@generated-path}#{name}">
                  <xsl:value-of select="name" />
                </a>
              </span>
              <small>
                <xsl:value-of select="docblock/description" />
              </small>
            </div>
          </li>
        </xsl:for-each>
        <xsl:for-each select="subpackage">
          <li class="closed">
          <xsl:variable name="subpackage" select="." />
          <div class="content">
            <span>
              <xsl:if test="$subpackage=''">Default</xsl:if>
              <xsl:if test="not($subpackage='')">
                <xsl:value-of select="$subpackage" />
              </xsl:if>
            </span>
          </div>
          <ul id="packages_{$package}_{$subpackage}" class="treeview-docblox">
            <xsl:for-each select="/project/file/class[docblock/tag[@name='package'][.=$package] and docblock/tag[@name='subpackage'][.=$subpackage]]">
            <li>
              <div class="content">
                <span>
                  <a href="{$root}/{../@generated-path}#{name}">
                    <xsl:value-of select="name" />
                  </a>
                </span>
                <small>
                  <xsl:value-of select="docblock/description" />
                </small>
              </div>
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

    <li class="namespace closed">
      <div class="content">
        <span>
          <xsl:if test="@name=''">Default</xsl:if>
          <xsl:if test="not(@name='')">
            <xsl:value-of select="@name" />
          </xsl:if>
        </span>
      </div>
      <ul>
        <xsl:for-each select="/project/file/class[@namespace=$full_name]">
          <li>
            <div class="content">
              <span>
                <a href="{$root}/{../@generated-path}#{name}">
                  <xsl:value-of select="name" />
                </a>
              </span>
              <small>
                <xsl:value-of select="docblock/description" />
              </small>
            </div>
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