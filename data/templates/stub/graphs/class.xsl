<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html"/>
    <xsl:include href="../layout.xsl"/>

    <xsl:template match="/project" mode="contents">
        <div class="row">
            <div class="span12">
                <div class="well wrapper">
                    <div id="viewer" class="viewer"></div>
                </div>
            </div>
        </div>

        <script src="{$root}js/jquery.iviewer.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(window).resize(function(){
                $("#viewer").height($(window).height() - 260);
            });

            $(document).ready(function() {
                $("#viewer").iviewer({src: "classes.svg", zoom_animation: false});
                $('#viewer img').bind('dragstart', function(event){
                    event.preventDefault();
                });
            });
        </script>
    </xsl:template>

</xsl:stylesheet>