<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="themes/default/chrome.xsl" />

  <xsl:template match="/project">
    <div id="nb-toolbar">
      <a href="{$root}/markers.html">Markers</a>
    </div>

    <div class="nb-sidebar">
      <div class="tabs">
        <ul>
          <li><a href="#namespace">Namespaces</a></li>
          <li><a href="#package">Packages</a></li>
          <li><a href="#file">Files</a></li>
        </ul>

        <div id="namespace">
          <xsl:for-each select="/project/namespace">
            <xsl:sort select="." />
            <h4>
              <xsl:if test=".=''">Default</xsl:if>
              <xsl:if test="not(.='')">
                <xsl:value-of select="." />
              </xsl:if>
            </h4>
            <xsl:variable name="namespace" select="." />
            <ul>
              <xsl:apply-templates select="//class[@namespace=$namespace]">
                <xsl:sort select="name" />
              </xsl:apply-templates>
            </ul>
          </xsl:for-each>
        </div>

        <div id="package">
        <xsl:for-each select="/project/package">
          <xsl:sort select="." />
          <h4>
            <xsl:if test=".=''">Default</xsl:if>
            <xsl:if test="not(.='')"><xsl:value-of select="."/></xsl:if>
          </h4>
          <xsl:variable name="package" select="."/>
          <ul>
          <xsl:apply-templates select="//class[docblock/tag[@name='package'][.=$package]]">
            <xsl:sort select="name" />
          </xsl:apply-templates>
          </ul>
        </xsl:for-each>
        </div>

        <div id="file">
          <ul style="margin-left: -35px">
          <xsl:for-each select="/project/file">
            <xsl:sort select="@path" />
            <li style="margin-bottom: 7px;">
              <strong><xsl:value-of select="@path"/></strong>
              <ul style="margin-left: -25px">
              <xsl:apply-templates select="class">
                <xsl:sort select="name" />
              </xsl:apply-templates>
              </ul>
            </li>
          </xsl:for-each>
          </ul>
        </div>
      </div>
    </div>

    <div class="nb-right-of-sidebar">
      <a href="classes.svg">Click here to view the full version</a><br />
      <embed src="classes.svg" width="700" />
    </div>
  </xsl:template>

  <xsl:template match="class">
    <li><a href="{../@generated-path}#{name}"><xsl:value-of select="name" /></a></li>
  </xsl:template>

</xsl:stylesheet>