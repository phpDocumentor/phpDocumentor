<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml"
    xmlns:dbx="http://phpdoc.org/xsl/functions"
    exclude-result-prefixes="dbx">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="file">
      <h1 class="file">
          <xsl:value-of select="@path" />
          <xsl:if test="source">
            <a href="{$root}source/{@path}.html"><img src="{$root}images/icons/view_source.png" alt="View Source" border="0"/></a>
          </xsl:if>
      </h1>

      <div id="file-nav-box">
          <div id="file-nav-options">
              Show:
              <img src="{$root}images/icons/visibility_public.png" alt="Public" id="show-public"/>
              <img src="{$root}images/icons/visibility_protected.png" alt="Protected" id="show-protected"/>
              <img src="{$root}images/icons/visibility_private.png" alt="Private" id="show-private"/>
              <span class="attribute" id="show-inherited">inherited</span>
          </div>

          <div id="file-nav-container">
              <ul id="file-nav">
                  <xsl:if test="constant">
                  <li>
                      <a href="#constants">
                          <img src="{$root}images/icons/constant.png" height="14" />
                          Constants
                      </a>
                      <ul>
                      <xsl:for-each select="constant">
                          <xsl:sort select="name" />
                          <li><a href="#::{name}"><xsl:value-of select="name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>

                  <xsl:if test="constant">
                  <li>
                      <a href="#functions">
                          <img src="{$root}images/icons/function.png" alt="Function" height="14"/>
                          Functions
                      </a>
                      <ul>
                      <xsl:for-each select="function">
                          <xsl:sort select="name"/>
                          <li><a href="#::{name}()"><xsl:value-of select="name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>

                  <xsl:if test="class">
                  <li>
                      <a href="#classes">
                          <img src="{$root}images/icons/class.png" alt="Class" height="14"/>
                          Classes
                      </a>
                      <ul>
                      <xsl:for-each select="class">
                          <xsl:sort select="full_name"/>
                          <li><a href="#{full_name}"><xsl:value-of select="full_name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>
              </ul>
              <div style="clear: left;"></div>
          </div>

          <div id="file-nav-tab">
              Table of Contents
          </div>
      </div>

      <a name="top" class="anchor"/>
      <xsl:if test="docblock/description|docblock/long-description">
          <div id="file-description">
              <xsl:apply-templates select="docblock/description"/>
              <xsl:apply-templates select="docblock/long-description"/>
          </div>
      </xsl:if>

      <xsl:if test="count(docblock/tag) > 0">
      <dl class="file-info">
          <xsl:apply-templates select="docblock/tag">
            <xsl:sort select="dbx:ucfirst(@name)" />
          </xsl:apply-templates>
      </dl>
      </xsl:if>

    <xsl:if test="count(constant) > 0">
    <a name="constants" class="anchor" />
    <h2>Constants</h2>
    <div>
      <xsl:apply-templates select="constant"/>
    </div>
    </xsl:if>

    <xsl:if test="count(function) > 0">
    <a name="functions" class="anchor" />
    <h2>Functions</h2>
    <div>
      <xsl:apply-templates select="function">
        <xsl:sort select="name" />
      </xsl:apply-templates>
    </div>
    </xsl:if>

    <xsl:if test="count(class) > 0">
    <a name="classes" class="anchor" />
    <xsl:apply-templates select="class">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

    <xsl:if test="count(interface) > 0">
    <a name="interfaces" class="anchor" />
    <xsl:apply-templates select="interface">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

  </xsl:template>

</xsl:stylesheet>