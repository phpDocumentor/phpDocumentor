<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml"
    xmlns:dbx="http://phpdoc.org/xsl/functions"
    xmlns:func="http://exslt.org/functions"
    extension-element-prefixes="func"
    exclude-result-prefixes="dbx">

    <!-- https://fosswiki.liip.ch/display/BLOG/How+to+define+your+own+XSLT+functions+with+EXSLT -->
    <func:function name="dbx:ucfirst">
        <xsl:param name="str"/>
        <xsl:param name="strLen" select="string-length($str)"/>
        <xsl:variable name="firstLetter" select="substring($str,1,1)"/>
        <xsl:variable name="restString" select="substring($str,2,$strLen)"/>
        <xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
        <xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
        <xsl:variable name="translate"
                      select="translate($firstLetter,$lower,$upper)"/>
        <func:result select="concat($translate,$restString)"/>
    </func:function>

    <xsl:template name="ucfirst">
        <xsl:param name="str"/>
        <xsl:param name="strLen" select="string-length($str)"/>
        <xsl:variable name="firstLetter" select="substring($str,1,1)"/>
        <xsl:variable name="restString" select="substring($str,2,$strLen)"/>
        <xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
        <xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
        <xsl:variable name="translate"
                      select="translate($firstLetter,$lower,$upper)"/>
        <xsl:value-of select="concat($translate,$restString)"/>
    </xsl:template>

        <!-- Concatenate items with links with a given separator, based on: http://symphony-cms.com/download/xslt-utilities/view/22517/-->
  <xsl:template name="implodeTypes">
    <xsl:param name="items" />
    <xsl:param name="separator" select="' | '" />

    <xsl:for-each select="$items">
      <xsl:if test="position() &gt; 1">
        <xsl:value-of select="$separator" />
      </xsl:if>

      <xsl:if test="@link">
        <a href="{$root}files/{@link}">
          <xsl:value-of select="." />
        </a>
      </xsl:if>
      <xsl:if test="not(@link)">
        <xsl:value-of select="." />
      </xsl:if>
    </xsl:for-each>
  </xsl:template>

    <xsl:template name="doctrine">
        <xsl:if test="count(docblock/tag[@plugin = 'doctrine']) > 0">
            <strong>Doctrine</strong>
            <table class="argument-info" style="font-size: 1.0em;">
                <thead>
                    <tr>
                        <th>Annotation</th>
                        <th>Field name</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <xsl:for-each select="docblock/tag[@plugin='doctrine']">
                    <xsl:variable name="doctrine_row_class">
                        <xsl:if test="position() mod 2 = 0">odd</xsl:if>
                        <xsl:if test="position() mod 2 = 1">even</xsl:if>
                    </xsl:variable>

                    <xsl:if test="count(argument) = 0">
                        <tr class="{$doctrine_row_class}">
                            <td style="vertical-align: top; font-variant: small-caps; text-transform: lowercase; font-size: 1.3em">
                                <xsl:apply-templates select="@link"/>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </xsl:if>

                    <xsl:for-each select="argument">
                        <tr class="{$doctrine_row_class}">
                            <xsl:if test="position() = 1">
                                <td rowspan="{count(../argument)}"
                                    style="vertical-align: top; font-variant: small-caps; text-transform: lowercase; font-size: 1.3em">
                                    <xsl:apply-templates select="../@link"/>
                                </td>
                            </xsl:if>
                            <td>
                                <xsl:value-of select="@field-name"/>
                            </td>
                            <td>
                                <xsl:value-of select="."/>
                            </td>
                        </tr>
                    </xsl:for-each>
                </xsl:for-each>
            </table>
        </xsl:if>
    </xsl:template>


  <xsl:template match="docblock/description[.!='']|docblock[description[.='']]/tag[@name='var']/@description">
    <div class="short-description">
      <xsl:value-of select="." disable-output-escaping="yes" />
    </div>
  </xsl:template>

  <xsl:template match="docblock/long-description">
    <div class="long-description">
      <xsl:value-of select="php:function('phpDocumentor\Plugin\Core\Xslt\Extension::markdown', string())" disable-output-escaping="yes" />
    </div>
  </xsl:template>

  <xsl:template match="docblock/tag">
    <dt>
      <xsl:call-template name="ucfirst">
        <xsl:with-param name="str" select="@name" />
      </xsl:call-template>
    </dt>
    <dd>
        <xsl:choose>
            <xsl:when test="@link">
                <a>
                    <xsl:attribute name="href">
                         <xsl:if test="not(@name = 'license' or @name = 'link' or @name = 'author')">
                             <xsl:value-of select="$root"/>files/
                         </xsl:if>
                         <xsl:value-of select="@link" />
                    </xsl:attribute>
                    <xsl:value-of select="@description" disable-output-escaping="yes"/>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="@description" disable-output-escaping="yes"/>
            </xsl:otherwise>
        </xsl:choose>
        &#160;
    </dd>
  </xsl:template>

    <!-- Any Doctrine related tag must not be shown in the details listing but
     is shown as a separate table -->
    <xsl:template match="docblock/tag[@plugin='doctrine']"/>

  <xsl:template match="docblock/tag[@name='var']|docblock/tag[@name='param']">
    <dt>
      <xsl:if test="@variable">
        <xsl:value-of select="@variable" />
      </xsl:if>
      <xsl:if test="@variable = ''">
        <xsl:value-of select="../../name" />
      </xsl:if>
    </dt>
    <dd>
      <xsl:if test="@type != ''">
        <xsl:apply-templates select="@type" /><br />
      </xsl:if>
      <em><xsl:value-of select="@description" disable-output-escaping="yes" /></em>
    </dd>
  </xsl:template>

  <xsl:template match="docblock/tag[@name='return']">
      <xsl:if test="type = ''">void</xsl:if>
      <xsl:if test="type != ''">
          <xsl:call-template name="implodeTypes">
              <xsl:with-param name="items" select="type" />
          </xsl:call-template>
      </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag[@name='throws']">
      <tr>
        <td>
            <xsl:if test="type != ''">
                <xsl:call-template name="implodeTypes">
                    <xsl:with-param name="items" select="type" />
                </xsl:call-template>
            </xsl:if>
        </td>
        <td>
            <em><xsl:value-of select="@description" disable-output-escaping="yes" /></em>
        </td>
    </tr>
  </xsl:template>

  <xsl:template match="docblock/tag/@type">
    <xsl:if test="not(.)">n/a</xsl:if>
    <xsl:if test=".">
      <xsl:if test="../@link">
        <a href="{$root}files/{../@link}">
          <xsl:value-of select="." />
        </a>
      </xsl:if>
      <xsl:if test="not(../@link)">
        <xsl:if test=". = ''">void</xsl:if>
        <xsl:if test=". != ''"><xsl:value-of select="." /></xsl:if>
      </xsl:if>
    </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag/type">
    <xsl:if test="not(.)">n/a</xsl:if>
    <xsl:if test=".">
        <xsl:call-template name="implodeTypes">
            <xsl:with-param name="items" select="."/>
        </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template match="docblock/tag/@link">
    <xsl:if test="not(../@description)">n/a</xsl:if>
    <xsl:if test="../@description">
      <xsl:if test="../@link">
        <a href="{$root}files/{../@link}">
          <xsl:value-of select="../@description" disable-output-escaping="yes"/>
        </a>
      </xsl:if>
      <xsl:if test="not(../@link)">
        <xsl:value-of select="../@description" disable-output-escaping="yes" />
      </xsl:if>
    </xsl:if>
  </xsl:template>

</xsl:stylesheet>
