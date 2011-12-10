<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="chrome.xsl"/>

    <xsl:template name="content">
        <script type="text/javascript" src="{$root}js/jquery.panzoom.js"></script>
        <script>
            $(document).ready(function () {
                $('#pan img').panZoom({
                    'directedit': true,
                    'debug':      false,
                    'zoom_step':  9
                });
                $('#pan img').panZoom('fit');
            });
        </script>

        <div id="content">
            <div style="font-size: 10px; white-space: normal;">
                The following actions are supported in this diagram:
                <ul>
                    <li>
                        <b>Zoom</b>, you can use the scrollwheel, or double-click,
                        to zoom in or out
                    </li>
                    <li>
                        <b>Move</b>, you can move around by dragging
                        the Diagram
                    </li>
                </ul>
            </div>

            <div id="pan" style="width: 600px; height: 600px; overflow: hidden"><img src="classes.svg" /></div>
        </div>
    </xsl:template>

</xsl:stylesheet>