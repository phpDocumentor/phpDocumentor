
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output indent="yes" method="html" />

    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
                <title>
                    <xsl:choose>
                        <xsl:when test="$title != ''">
                            <xsl:value-of select="$title" />
                        </xsl:when>
                        <xsl:otherwise>DocBlox Documentation</xsl:otherwise>
                    </xsl:choose>
                </title>
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                <link rel="stylesheet" href="{$root}css/aristo/jquery-ui-1.8.7.custom.css" type="text/css" />
                <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
                <script type="text/javascript" src="{$root}js/jquery-1.6.2.min.js"></script>
                <script type="text/javascript" src="{$root}js/jquery-ui-1.8.16.light.min.js"></script>
                
            </head>
            <body class="chrome">
                <div id="page">
                    <div id="db-header">
                        <xsl:call-template name="search"/>
                        <div id="menubar">
                            <xsl:call-template name="menubar" />
                        </div>
                    </div>
                    <div id="content-container" class="resizable">
                        <div id="sidebar">
                            <div id="searchbar" class="ui-layout-north">
                                <input type="text" name="search" value="" id="search" />
                                <div class="ui-icon ui-icon-close"> </div>
                            </div>
                            <xsl:call-template name="nav" />
                        </div>
                        <div id="contents">
                            <div id="tabs">
                                <ul>
                                    <li>
                                        <a href="#tabs-1">Home</a>
                                    </li>
                                </ul>
                                <div id="tabs-1">
                                    <iframe src="{$root}content.html" frameBorder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                <script type="text/javascript" src="{$root}js/jquery.splitter.js"></script>
                <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
                <script type="text/javascript" src="{$root}js/jquery.jstree.js"></script>
                <!--<script type="text/javascript" src="{$root}js/jquery.tools.min.js"></script>-->
                <script type="text/javascript" src="{$root}js/jquery.once.js"></script>
                <script type="text/javascript" src="{$root}js/default.js"></script>
            </body>
        </html>
    </xsl:template>

</xsl:stylesheet>