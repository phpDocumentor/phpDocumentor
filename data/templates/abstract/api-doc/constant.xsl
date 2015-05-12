<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml"
    xmlns:dbx="http://phpdoc.org/xsl/functions"
    exclude-result-prefixes="dbx">
    <xsl:output indent="yes" method="html"/>

    <xsl:template match="constant">
        <a id="{../full_name}::{name}" class="anchor"/>

        <div class="constant">
            <xsl:attribute name="class">
                <xsl:value-of select="concat(name(), ' publicC')"/>
                <xsl:if test="inherited_from"> inherited_from </xsl:if>
            </xsl:attribute>

            <a href="#" class="gripper">
                <img src="{$root}images/icons/arrow_right.png" alt="&gt;"/>
                <img src="{$root}images/icons/arrow_down.png" alt="V" style="display: none;"/>
            </a>

            <code class="title">
                <img src="{$root}images/icons/constant.png" alt="Constant"/>
                <xsl:value-of select="docblock/tag[@name='var']/type"/>&#160;
                <span class="highlight">
                    <xsl:value-of select="name"/>
                </span>
                = <xsl:value-of select="value"/>
            </code>

            <div class="description">
                <xsl:if test="inherited_from">
                    <span class="attribute">inherited</span>
                </xsl:if>

                <xsl:apply-templates select="docblock/description"/>
                <xsl:if test="inherited_from">
                    <small class="inherited_from">Inherited from:
                        <xsl:apply-templates select="docblock/tag[@name='inherited_from']/@link"/>
                    </small>
                </xsl:if>
            </div>

            <div class="code-tabs">
                <xsl:apply-templates select="docblock/long-description"/>

                <xsl:if test="docblock/tag">
                    <dl class="constant-info">
                        <xsl:apply-templates select="docblock/tag">
                            <xsl:sort select="dbx:ucfirst(@name)"/>
                        </xsl:apply-templates>
                    </dl>
                </xsl:if>
            </div>
            <div class="clear"></div>
        </div>
    </xsl:template>

</xsl:stylesheet>