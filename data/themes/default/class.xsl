<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/project">
    <xsl:apply-templates select="/project/class[name=$class]" />
  </xsl:template>

  <xsl:template match="class|interface">
    <h3><xsl:value-of select="name" /></h3>
    <div class="properties">
      <h1>Properties</h1>
      <label class="property-key">Extends</label>
      <div class="property-value"><xsl:value-of select="extends" />&#160;</div>
      <label class="property-key">Implements</label>
      <div class="property-value">
        <xsl:for-each select="implements"><xsl:value-of select="." /><br /></xsl:for-each>&#160;
      </div>
      <xsl:for-each select="docblock/tag">
        <xsl:sort select="@name" />
        <label class="property-key"><xsl:value-of select="@name" /></label>
        <div class="property-value">
          <xsl:if test="@link and @link != ''"><a title="{.}" href="{@link}"><xsl:value-of select="." /></a></xsl:if>
          <xsl:if test="not(@link) or @link = ''"><a title="{.}"><xsl:value-of select="." /></a></xsl:if>
            &#160;
        </div>
      </xsl:for-each>
      <label class="property-key">Abstract</label>
      <div class="property-value">
        <xsl:if test="@abstract='true'">Yes</xsl:if>
        <xsl:if test="@abstract != 'true'">No</xsl:if>&#160;
      </div>
      <label class="property-key">Final</label>
      <div class="property-value">
        <xsl:if test="@final='true'">Yes</xsl:if>
        <xsl:if test="@final != 'true'">No</xsl:if>&#160;
      </div>
    </div>

    <h4>Description</h4>
    <xsl:if test="not(docblock/description) and not(docblock/long-description)">
      <strong><em>No description is available</em></strong>
    </xsl:if>
    <xsl:if test="docblock/description">
    <em><xsl:value-of select="docblock/description" disable-output-escaping="yes" /></em><br />
    </xsl:if>
    <xsl:if test="docblock/long-description">
    <xsl:value-of select="docblock/long-description" disable-output-escaping="yes" /><br />
    </xsl:if>

    <xsl:if test="count(method) > 0">
    <div id="methods_{name}">
      <h4>Methods</h4>
      <xsl:for-each select="method">
        <xsl:sort select="name" />
        <a style="font-style: italic;" href="#{../name}::{name}()"><xsl:value-of select="name" /></a>,
      </xsl:for-each>

      <xsl:for-each select="method">
        <xsl:sort select="name" />
        <xsl:apply-templates select="." />
      </xsl:for-each>
    </div>
    </xsl:if>

    <xsl:if test="count(property) > 0">
    <div id="properties_{name}">
      <h4>Properties</h4>
      <xsl:for-each select="property">
        <xsl:sort select="name" />
        <a href="#{../name}::{name}">
          <xsl:value-of select="name" />
        </a>,
      </xsl:for-each>

      <xsl:for-each select="property">
        <xsl:sort select="name" />
        <xsl:apply-templates select="." />
      </xsl:for-each>
    </div>
    </xsl:if>

    <xsl:if test="count(constant) > 0">
    <div id="constants_{name}">
      <h4>Constants</h4>
      <xsl:for-each select="constant">
        <xsl:sort select="name" />

        <xsl:apply-templates select="." />
      </xsl:for-each>
    </div>
    </xsl:if>
    <div style="clear: both"></div>
  </xsl:template>

  <xsl:template match="method|function">
    <div class="method">
      <a id="{../name}::{name}()" />
      <h3>
        <xsl:value-of select="name" />
        <span class="nb-faded-text">(
          <xsl:for-each select="argument">

            <xsl:variable name="variable_name" select="name" />
            <xsl:variable name="variable_type" select="../docblock/tag[@name='param' and @variable=$variable_name]/@type" />
            <xsl:if test="../docblock/tag[@name='param' and @variable=$variable_name]/@link">
              <a href="{$root}{../docblock/tag[@name='param' and @variable=$variable_name]/@link}">
                <xsl:value-of select="$variable_type" />
              </a>
            </xsl:if>
            <xsl:if test="not(../docblock/tag[@name='param' and @variable=$variable_name]/@link)">
              <xsl:value-of select="$variable_type" />
            </xsl:if>
            &#160;
            <xsl:value-of select="$variable_name" />

            <xsl:if test="default != ''">
            = <xsl:value-of select="default" disable-output-escaping="yes"/>
            </xsl:if>,
          </xsl:for-each>
          )
        </span>
        :
        <span class="nb-faded-text">
          <xsl:if test="not(docblock/tag[@name='return']/@type)">n/a</xsl:if>
          <xsl:if test="docblock/tag[@name='return']/@type">
            <xsl:if test="docblock/tag[@name='return']/@link">
              <a href="{$root}{docblock/tag[@name='return']/@link}">
                <xsl:value-of select="docblock/tag[@name='return']/@type" />
              </a>
            </xsl:if>
            <xsl:if test="not(docblock/tag[@name='return']/@link)">
              <xsl:value-of select="docblock/tag[@name='return']/@type" />
            </xsl:if>
          </xsl:if>
        </span>

      </h3>
      <xsl:if test="docblock/description != '' or docblock/long-description != ''">
        <h4>Description</h4>
      </xsl:if>
      <xsl:if test="docblock/description != ''">
        <em><xsl:value-of select="docblock/description" disable-output-escaping="yes" /></em><br />
      </xsl:if>
      <xsl:if test="docblock/long-description != ''">
        <small><xsl:value-of select="docblock/long-description" disable-output-escaping="yes" /></small><br />
      </xsl:if>
      <xsl:if test="count(argument) > 0">
      <h4>Arguments</h4>
      <table>
        <thead><tr><th>Name</th><th>Type</th><th>Description</th><th>Default</th></tr></thead>
        <tbody>
          <xsl:for-each select="argument">
            <xsl:variable name="variable_name" select="name" />
            <xsl:variable name="variable_type" select="../docblock/tag[@name='param' and @variable=$variable_name]/@type" />
            <xsl:variable name="variable_description" select="../docblock/tag[@name='param' and @variable=$variable_name]/." />
            <tr>
              <td>
                <xsl:value-of select="$variable_name" />
              </td>
              <td>
                <xsl:if test="not($variable_type)">n/a</xsl:if>
                <xsl:if test="$variable_type">
                  <xsl:if test="../docblock/tag[@name='param' and @variable=$variable_name]/@link">
                    <a href="{$root}{../docblock/tag[@name='param' and @variable=$variable_name]/@link}">
                      <xsl:value-of select="$variable_type" />
                    </a>
                  </xsl:if>
                  <xsl:if test="not(../docblock/tag[@name='param' and @variable=$variable_name]/@link)">
                    <xsl:value-of select="$variable_type" />
                  </xsl:if>
                </xsl:if>
              </td>
              <td>
                <xsl:value-of select="$variable_description" disable-output-escaping="yes"/>
              </td>
              <td>
                <xsl:value-of select="default" disable-output-escaping="yes" />
              </td>
            </tr>
          </xsl:for-each>
        </tbody>
      </table>
      </xsl:if>

      <h4>Return value</h4>
      <table>
        <thead>
          <tr>
            <th>Type</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <xsl:if test="not(docblock/tag[@name='return']/@type)">n/a</xsl:if>
              <xsl:if test="docblock/tag[@name='return']/@type">
                <xsl:if test="docblock/tag[@name='return']/@link">
                  <a href="{$root}{docblock/tag[@name='return']/@link}">
                    <xsl:value-of select="docblock/tag[@name='return']/@type" />
                  </a>
                </xsl:if>
                <xsl:if test="not(docblock/tag[@name='return']/@link)">
                  <xsl:value-of select="docblock/tag[@name='return']/@type" />
                </xsl:if>
              </xsl:if>
            </td>
            <td>
              <xsl:if test="not(docblock/tag[@name='return'])">n/a</xsl:if>
              <xsl:value-of select="docblock/tag[@name='return']" />
            </td>
          </tr>
        </tbody>
      </table>

      <xsl:if test="docblock/tag[@name != 'return' and @name != 'param']">
        <h4>Tags</h4>
        <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
        <xsl:for-each select="docblock/tag[@name != 'return' and @name != 'param']">
          <tr>
            <td>
              <xsl:value-of select="@name" />
            </td>
            <td>
                <xsl:if test="@link and @link != ''"><a href="{$root}{@link}"><xsl:value-of select="." /></a></xsl:if>
                <xsl:if test="not(@link) or @link = ''"><xsl:value-of select="." /></xsl:if>
            </td>
          </tr>
        </xsl:for-each>
        </tbody>
        </table>
      </xsl:if>
    </div>

  </xsl:template>

  <xsl:template match="property">
    <div class="method">
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
        <xsl:value-of select="docblock/description" disable-output-escaping="yes" />
      </em>
      <br />
      <xsl:if test="docblock/long-description">
        <small>
          <xsl:value-of select="docblock/long-description" />
        </small>
        <br />
      </xsl:if>
      <br />
    </div>

  </xsl:template>

  <xsl:template match="constant">
    <a id="{../name}::{name}" />
    <div class="constant">
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
        <xsl:value-of select="docblock/description" disable-output-escaping="yes" />
      </em>
      <br />
      <xsl:if test="docblock/long-description">
        <small>
          <xsl:value-of select="docblock/long-description" />
        </small>
        <br />
      </xsl:if>
      <br />
    </div>
  </xsl:template>
</xsl:stylesheet>