<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/project">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Docs for file
          <xsl:value-of select="@path" />
        </title>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
        <link rel="stylesheet" href="./css/black-tie/jquery-ui-1.7.3.custom.css" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery-ui-1.7.2.custom.min.js"></script>
      </head>
      <body>

        <xsl:apply-templates select="/project/class[name=$class]" />

      </body>
    </html>

  </xsl:template>

  <xsl:template match="class">

    <div>
      <div class="tabs">
        <ul>
          <li><a href="#description_{name}">Description</a></li>
          <li><a href="#methods_{name}">Methods</a></li>
          <li><a href="#properties_{name}">Properties</a></li>
          <li><a href="#constants_{name}">Constants</a></li>
        </ul>

        <div id="description_{name}">
          <div class="nb-properties ui-corner-all ui-widget-content">
            <strong>Properties</strong><br />
            <hr />
            <table class="nb-class-properties">
            <tr><th>Extends</th><td><xsl:value-of select="extends" /></td></tr>
            <xsl:for-each select="docblock/tag">
              <xsl:sort select="@name" />
              <tr><th><xsl:value-of select="@name" /></th><td><xsl:value-of select="." /></td></tr>
            </xsl:for-each>
            <tr><th>Abstract</th><td><xsl:if test="@abstract='true'">Yes</xsl:if><xsl:if test="@abstract != 'true'">No</xsl:if></td></tr>
            <tr><th>Final</th><td><xsl:if test="@final='true'">Yes</xsl:if><xsl:if test="@final != 'true'">No</xsl:if></td></tr>
            </table>
          </div>
          <h2>Description</h2>
          <strong><xsl:value-of select="docblock/description" /></strong><br />
          <xsl:value-of disable-output-escaping="yes" select="docblock/long-description" /><br />
          <div style="clear: both"></div>
        </div>

        <div id="methods_{name}">
          <h2>Methods</h2>
          <div class="nb-sidebar">

            <script>
              function searchMethod(input)
              {
                jQuery(input).parent().children('div.results').find('a').show();
                jQuery(input).parent().children('div.results').find('a').not('a[href*="'+jQuery('input#search_method').val()+'"]').hide();
              }
            </script>
            <input type="text" id="search_method" name="search_method" onkeyup="searchMethod(this)"/>
            <div class="results">
            <xsl:for-each select="method">
              <xsl:sort select="name" />
              <a href="#{../name}::{name}()">
                <xsl:value-of select="name" />
                <br />
              </a>
            </xsl:for-each>
            </div>
          </div>

          <div class="nb-right-of-sidebar">
            <xsl:for-each select="method">
              <xsl:sort select="name" />
              <xsl:apply-templates select="." />
            </xsl:for-each>
          </div>
          <div style="clear: both"></div>
        </div>

        <div id="properties_{name}">
          <h2>Properties</h2>
          <div class="nb-sidebar">
            <xsl:for-each select="property">
              <xsl:sort select="name" />
              <a href="#{../name}::{name}">
                <xsl:value-of select="name" />
              </a>
              <br />
            </xsl:for-each>
          </div>

          <div class="nb-right-of-sidebar">
            <xsl:for-each select="property">
              <xsl:sort select="name" />
              <xsl:apply-templates select="." />
            </xsl:for-each>
          </div>
        </div>

        <div id="constants_{name}">
          <h2>Constants</h2>
          <xsl:for-each select="constant">
            <xsl:sort select="name" />

            <xsl:apply-templates select="." />
          </xsl:for-each>
        </div>
      </div>
    </div>
  </xsl:template>

  <xsl:template match="method|function">
    <a id="{../name}::{name}()" />
    <h3>
      <span class="nb-faded-text">
        <xsl:if test="not(docblock/tag[@name='return']/@type)">n/a</xsl:if><xsl:value-of select="docblock/tag[@name='return']/@type" />
      </span>
      &#160;<xsl:value-of select="name" />
      <span class="nb-faded-text">(
        <xsl:for-each select="argument">
          <xsl:variable name="variable_name" select="name" />
          <xsl:value-of select="../docblock/tag[@name='param' and @variable=$variable_name]/@type" />&#160;<xsl:value-of select="$variable_name" /> =
          <xsl:value-of select="default" disable-output-escaping="yes"/>,
        </xsl:for-each>
        )
      </span>
    </h3>
    <em><xsl:value-of select="docblock/description" /></em><br />
    <xsl:if test="docblock/long-description"><small><xsl:value-of select="docblock/long-description" /></small><br /></xsl:if>
    <br />
    <hr />
  </xsl:template>

  <xsl:template match="property">
    <a id="{../name}::{name}" />
    <h3>
      <span class="nb-faded-text">
        <xsl:value-of select="docblock/tag[@name='var']/." />&#160;
        <xsl:value-of select="@visibility" />&#160;
        <xsl:if test="@static='true'">static&#160;</xsl:if>
        <xsl:if test="@final='true'">final&#160;</xsl:if>
      </span>
      <xsl:value-of select="name" />
    </h3>
    <em>
      <xsl:value-of select="docblock/description" />
    </em>
    <br />
    <xsl:if test="docblock/long-description">
      <small>
        <xsl:value-of select="docblock/long-description" />
      </small>
      <br />
    </xsl:if>
    <br />
    <hr />

  </xsl:template>

  <xsl:template match="constant">
    <a id="{../name}::{name}" />
    <h3>
      <span class="nb-faded-text">
        <xsl:value-of select="docblock/tag[@name='var']/." />&#160;
      </span>
      <xsl:value-of select="name" />
      <span class="nb-faded-text">
       = <xsl:value-of select="value" />
      </span>
    </h3>
    <em>
      <xsl:value-of select="docblock/description" />
    </em>
    <br />
    <xsl:if test="docblock/long-description">
      <small>
        <xsl:value-of select="docblock/long-description" />
      </small>
      <br />
    </xsl:if>
    <br />
    <hr />
  </xsl:template>
</xsl:stylesheet>