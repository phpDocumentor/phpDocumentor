<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="../abstract/chrome.xsl"/>
    <xsl:include href="../abstract/chrome/header.xsl"/>
    <xsl:include href="../abstract/chrome/menu.xsl"/>
    <xsl:include href="../abstract/chrome/footer.xsl"/>

    <xsl:template name="content">
        <table id="page">
            <thead>
                <tr><td colspan="2" id="db-header">
                    <xsl:call-template name="page-header" />
                </td></tr>
                <tr><td colspan="2" id="db-menu">
                    <xsl:call-template name="page-menu" />
                </td></tr>
            </thead>
            <tbody>
                <tr>
                    <td id="sidebar">
                        <iframe name="nav" id="nav" src="{$root}nav.html"/>
                    </td>
                    <td id="contents">
                        <iframe name="content" id="content" src="{$root}content.html"/>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr><td colspan="2" id="db-footer">
                    <xsl:call-template name="page-footer"/>
                </td></tr>
            </tfoot>
        </table>
    </xsl:template>

</xsl:stylesheet>