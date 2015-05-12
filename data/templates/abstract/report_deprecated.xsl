<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="chrome.xsl" />

  <xsl:template name="content">

      <div id="content">

          <h1>Deprecated elements</h1>
          <xsl:if test="count(//docblock/tag[@name='deprecated']) &lt; 1">
              <div class="success_notification">No deprecated elements have been
                  found in this project.
              </div>
          </xsl:if>
          <div id="marker-accordion">
            <xsl:for-each select="/project/file">
                <xsl:if test=".//docblock/tag[@name='deprecated']">
                    <h3>
                        <a href="#">
                            <xsl:value-of select="@path"/>
                            <small><xsl:value-of select="count(.//docblock/tag[@name='deprecated'])"/></small>
                        </a>
                    </h3>
                    <div>
                        <table>
                            <tr><th>Type</th><th>Line</th><th>Description</th></tr>
                            <xsl:for-each select=".//docblock/tag[@name='deprecated']">
                                <xsl:sort select="line"/>
                                <tr>
                                    <xsl:if test="name() = 'tag'">
                                        <td><xsl:value-of select="@name"/></td>
                                    </xsl:if>
                                    <xsl:if test="name() != 'tag'">
                                        <td><xsl:value-of select="name()"/></td>
                                    </xsl:if>
                                    <td><xsl:value-of select="@line"/></td>
                                    <xsl:if test="name() = 'tag'">
                                        <td>
                                            <xsl:value-of select="@description" disable-output-escaping="yes"/>
                                        </td>
                                    </xsl:if>
                                    <xsl:if test="name() != 'tag'">
                                        <td>
                                            <xsl:value-of select="." disable-output-escaping="yes"/>
                                        </td>
                                    </xsl:if>
                                </tr>
                            </xsl:for-each>
                        </table>
                    </div>
                </xsl:if>
            </xsl:for-each>
        </div>

    </div>
  </xsl:template>

</xsl:stylesheet>