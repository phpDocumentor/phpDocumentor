<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/project">
    <xsl:apply-templates select="/project/class[name=$class]" />
  </xsl:template>

  <xsl:template match="class|interface">

    <div>
      <div class="tabs">
        <ul>
          <li><a href="#description_{name}">Description</a></li>
          <xsl:if test="count(method) > 0">
          <li><a href="#methods_{name}">Methods</a></li>
          </xsl:if>
          <xsl:if test="count(property) > 0">
          <li><a href="#properties_{name}">Properties</a></li>
          </xsl:if>
          <xsl:if test="count(constant) > 0">
          <li><a href="#constants_{name}">Constants</a></li>
          </xsl:if>
        </ul>

        <div id="description_{name}">
          <div class="nb-properties ui-corner-all ui-widget-content">
            <strong>Properties</strong><br />
            <hr />
            <table class="nb-class-properties">
            <tr><th>Extends</th><td><xsl:value-of select="extends" /></td></tr>
            <tr><th>Implements</th><td>
              <xsl:for-each select="implements">
                <xsl:value-of select="." /><br />
              </xsl:for-each>
            </td></tr>
            <xsl:for-each select="docblock/tag">
              <xsl:sort select="@name" />
              <tr><th><xsl:value-of select="@name" /></th><td><a title="{.}" href="{@link}" style="border-bottom: 1px dashed green; cursor: help"><xsl:value-of select="@excerpt" /></a></td></tr>
            </xsl:for-each>
            <tr><th>Abstract</th><td><xsl:if test="@abstract='true'">Yes</xsl:if><xsl:if test="@abstract != 'true'">No</xsl:if></td></tr>
            <tr><th>Final</th><td><xsl:if test="@final='true'">Yes</xsl:if><xsl:if test="@final != 'true'">No</xsl:if></td></tr>
            </table>
          </div>
          <h2>Description</h2>
          <xsl:if test="not(docblock/description) and not(docblock/long-description)">
            <strong><em>No description is available</em></strong>
          </xsl:if>
          <strong><xsl:value-of select="docblock/description" /></strong><br />
          <xsl:value-of disable-output-escaping="yes" select="docblock/long-description" /><br />
          <div style="clear: both"></div>
        </div>

        <xsl:if test="count(method) > 0">
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
        </xsl:if>

        <xsl:if test="count(property) > 0">
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
        </xsl:if>

        <xsl:if test="count(constant) > 0">
        <div id="constants_{name}">
          <h2>Constants</h2>
          <xsl:for-each select="constant">
            <xsl:sort select="name" />

            <xsl:apply-templates select="." />
          </xsl:for-each>
        </div>
        </xsl:if>
      </div>
    </div>
  </xsl:template>

  <xsl:template match="method|function">
    <a id="{../name}::{name}()" />
    <h3>
      <span class="nb-faded-text">
        <xsl:if test="not(docblock/tag[@name='return']/@type)">n/a</xsl:if>
        <xsl:if test="docblock/tag[@name='return']/@type">
          <xsl:if test="docblock/tag[@name='return']/@link">
            <a href="{$root}/files/{docblock/tag[@name='return']/@link}"><xsl:value-of select="docblock/tag[@name='return']/@type" /></a>
          </xsl:if>
          <xsl:if test="not(docblock/tag[@name='return']/@link)">
            <xsl:value-of select="docblock/tag[@name='return']/@type" />
          </xsl:if>
        </xsl:if>
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
        <xsl:value-of select="docblock/tag[@name='var']/@type" />&#160;
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