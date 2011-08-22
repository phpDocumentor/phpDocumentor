<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="file">
    <script type="text/javascript" src="{$root}js/menu.js"/>
    <script>
      $(document).ready(function() {
        $('a.gripper').click(function() {
            $(this).nextAll('div.code-tabs').slideToggle();
            $(this).children('img').toggle();
            return false;
        });
        $('div.code-tabs').hide();
        $('a.gripper').show();
        $('div.code-tabs:empty').prevAll('a.gripper').html('');
        $('div.file-nav').show();
      });
    </script>
      <h1 class="file"><xsl:value-of select="@path" /></h1>
      <div class="file-nav">
          <ul id="file-nav">
              <li><a href="#top">Global</a></li>

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
                      <img src="{$root}images/icons/function.png" height="14"/>
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
                      <img src="{$root}images/icons/class.png" height="14"/>
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
      </div>

      <a name="top"/>
      <xsl:if test="docblock/description|docblock/long-description">
          <div id="file-description">
              <xsl:apply-templates select="docblock/description"/>
              <xsl:apply-templates select="docblock/long-description"/>
          </div>
      </xsl:if>

      <xsl:if test="count(docblock/tag) > 0">
      <dl class="file-info">
          <xsl:apply-templates select="docblock/tag">
            <xsl:sort select="@name" />
          </xsl:apply-templates>
      </dl>
      </xsl:if>

    <xsl:if test="count(constant) > 0">
    <a name="constants" />
    <h2>Constants</h2>
    <div>
      <xsl:apply-templates select="constant"/>
    </div>
    </xsl:if>

    <xsl:if test="count(function) > 0">
    <a name="functions" />
    <h2>Functions</h2>
    <div>
      <xsl:apply-templates select="function">
        <xsl:sort select="name" />
      </xsl:apply-templates>
    </div>
    </xsl:if>

    <xsl:if test="count(class) > 0">
    <a name="classes" />
    <xsl:apply-templates select="class">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

    <xsl:if test="count(interface) > 0">
    <a name="interfaces" />
    <xsl:apply-templates select="interface">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

  </xsl:template>

</xsl:stylesheet>