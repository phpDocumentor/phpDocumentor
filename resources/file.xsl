<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="themes/default/chrome.xsl" />
  <xsl:include href="class.xsl" />

  <xsl:template match="/project">
    <div id="nb-toolbar"><a href="{$root}/index.html">Back to overview</a></div>
    <xsl:apply-templates select="/project/file[@path=$file]" />
  </xsl:template>

  <xsl:template match="/project/file">
    <div class="tabs">
      <ul>
        <li><a href="#file_description">Description</a></li>
        <xsl:if test="function">
        <li><a href="#file_functions">Functions</a></li>
        </xsl:if>
        <xsl:if test="constant">
        <li><a href="#file_constants">Constants</a></li>
        </xsl:if>
        <xsl:for-each select="interface">
        <li><a href="#{name}"><xsl:value-of select="name" /></a></li>
        </xsl:for-each>
        <xsl:for-each select="class">
        <li><a href="#{name}"><xsl:value-of select="name" /></a></li>
        </xsl:for-each>
      </ul>

      <div id="file_description">
        <div class="nb-properties ui-corner-all ui-widget-content">
          <strong>Properties</strong><br />
          <hr />
          <table class="nb-class-properties">
            <xsl:for-each select="docblock/tag">
              <xsl:sort select="@name" />
              <tr><th><xsl:value-of select="@name" /></th><td><xsl:value-of select="." /></td></tr>
            </xsl:for-each>
          </table>
        </div>
        <h2>Description</h2>
        Docblock subject<br />
        Docblock long description <br />

        <h3>Includes</h3>
        <xsl:for-each select="include">
          <xsl:value-of select="name" />&#160;<span class="nb-faded-text">(<xsl:value-of select="@type" />)</span><br />
        </xsl:for-each>
      </div>

      <xsl:if test="function">
      <div id="file_functions">
        <div class="nb-sidebar">
          <xsl:for-each select="function">
            <a href="#{../name}::{name}()">
              <xsl:value-of select="name" />
            </a>
            <br />
          </xsl:for-each>
        </div>

        <div class="nb-right-of-sidebar">
          <h2>Functions</h2>
          <xsl:for-each select="function">
            <xsl:apply-templates select="." />
          </xsl:for-each>
        </div>
      </div>
      </xsl:if>

      <xsl:if test="constant">
      <div id="file_constants">
        <h2>Constants</h2>
        <xsl:for-each select="constant">
          <xsl:apply-templates select="." />
        </xsl:for-each>
      </div>
      </xsl:if>

      <xsl:for-each select="interface">
      <div id="{name}">
        <xsl:apply-templates select="."/>
      </div>
      </xsl:for-each>

      <xsl:for-each select="class">
      <div id="{name}">
        <xsl:apply-templates select="."/>
      </div>
      </xsl:for-each>
    </div>
  </xsl:template>

</xsl:stylesheet>