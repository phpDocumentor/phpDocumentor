<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output indent="yes" method="html" />

    <xsl:template name="chrome-head">
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/template.css" type="text/css" />
        <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"> </script>
        <script type="text/javascript" src="{$root}js/jquery.tools.min.js"> </script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"> </script>
        <script type="text/javascript" src="{$root}js/template.js"> </script>
    </xsl:template>

</xsl:stylesheet>
