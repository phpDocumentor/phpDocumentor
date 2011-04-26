<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="file">
    <script>
      $(document).ready(function() {
        $('dl.function-info, dl.constant-info, dl.property-info').prev().css('cursor', 'pointer');
        $('dl.function-info, dl.constant-info, dl.property-info').prev().click(function() {
          if ($(this).next().is(':hidden'))
          {
            $(this).children('img')[0].src = '<xsl:value-of select="$root"/>images/arrow_down.gif';
          } else
          {
            $(this).children('img')[0].src = '<xsl:value-of select="$root"/>images/arrow_right.gif';
          }
          $(this).next().slideToggle();
        });
      $('dl.function-info, dl.constant-info, dl.property-info').hide();
      $('dl.function-info img, dl.constant-info img, dl.property-info img').show();
      });
    </script>
    <a name="top"/>
    <h1 class="file"><xsl:value-of select="@path" /></h1>

    <div class="file_menu">
      <xsl:if test="count(include) > 0"><a href="#includes">Includes</a>&#160;</xsl:if>
      <xsl:if test="count(function) > 0"><a href="#functions">Functions</a>&#160;</xsl:if>
      <xsl:if test="count(constant) > 0"><a href="#constants">Constants</a>&#160;</xsl:if>
      <xsl:if test="count(class) > 0"><a href="#classes">Classes</a>&#160;</xsl:if>
      <xsl:if test="count(interface) > 0"><a href="#interfaces">Interfaces</a></xsl:if>
    </div>

    <dl class="file-info">
      <xsl:apply-templates select="docblock/tag">
        <xsl:sort select="@name" />
      </xsl:apply-templates>

      <xsl:if test="count(constant) > 0">
      <dt>Constants</dt>
        <xsl:for-each select="constant">
          <dd><a class="constant" href="#{../full_name}::{name}"><xsl:value-of select="name" /></a></dd>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="count(function) > 0">
      <dt>Functions</dt>
        <xsl:for-each select="function">
          <dd><a class="function" href="#{../full_name}::{name}()"><xsl:value-of select="name" /></a></dd>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="count(class) > 0">
      <dt>Classes</dt>
        <xsl:for-each select="class">
          <dd><a class="class" href="#{full_name}"><xsl:value-of select="name" /></a></dd>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="count(interface) > 0">
      <dt>Interfaces</dt>
        <xsl:for-each select="interface">
          <dd><a class="interface" href="#{full_name}"><xsl:value-of select="name" /></a></dd>
        </xsl:for-each>
      </xsl:if>
    </dl>

    <xsl:if test="docblock/description|docblock/long-description">
      <h2>Description</h2>
      <xsl:apply-templates select="docblock/description" />
      <xsl:apply-templates select="docblock/long-description" />
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