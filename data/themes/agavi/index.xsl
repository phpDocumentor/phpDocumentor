<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Language" content="en" />
        <meta http-equiv="imagetoolbar" content="no" />

        <!--<base href="http://www.agavi.org/" />-->

        <title>Documentation - Agavi</title>

        <meta name="robots" content="all" />
        <meta name="description" content="Agavi is a powerful, scalable PHP5 application framework that follows the MVC paradigm. It enables developers to write clean, maintainable and extensible code. Agavi puts choice and freedom over limiting conventions, and focuses on sustained quality rather than short-sighted decisions." />
        <meta name="keywords" content="xhtml, html, css, dhtml, javascript, sql, development, webdesign, seo_friendly, win, soa, webservices, internationalization, soap, web, mvc, framework, php, i18n, webservice, l10n, framework, library, open source, component, model, view, controller, database, orm, template, engine, caching, validation" />
        <meta name="copyright" content="Copyright 2008 Bitextender GmbH" />

        <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
        <link rel="stylesheet" type="text/css" href="http://www.agavi.org/css/globals.css" />
        <link rel="stylesheet" type="text/css" href="http://www.agavi.org/css/screen.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="http://www.agavi.org/css/documentation.css" />
        <link rel="stylesheet" type="text/css" href="http://www.agavi.org/css/sh_style.css" />
        <link rel="stylesheet" media="only screen and (max-device-width: 480px)" type="text/css" href="http://www.agavi.org/css/ios.css" />
        <link rel="stylesheet" media="only screen and (max-device-width: 960px) and (-webkit-min-device-pixel-ratio: 2)" type="text/css" href="http://www.agavi.org/css/ios.css" />
        <link type="text/css" rel="stylesheet" media="only screen and (device-width: 768px)" href="http://www.agavi.org/css/ios.css" />
        <!--<script type="text/javascript" src="shjs/sh_main.min.js"></script>-->
        <!--<script type="text/javascript" src="shjs/sh_php.min.js"></script>-->
        <!--<script type="text/javascript" src="shjs/sh_xml.min.js"></script>-->
        <script type="text/javascript" src="{$root}js/jquery-1.6.1.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.cookie.js"></script>
        <script type="text/javascript" src="{$root}js/jquery.treeview.js"></script>
        <!--<script type="text/javascript" src="js/jquery.scrollTo.js"></script>-->
        <!--<script type="text/javascript" src="js/documentation.js"></script>-->
      </head>
      <body>
        <div id="agavi_wrapper">
          <div id="agavi_header_container">
            <div id="agavi_header">
              <div id="agavi_mainnav">
                <ul>
                  <li>
                    <a href="/download">Download</a>
                  </li>
                  <li class="active">
                    <a href="/documentation">Docs</a>
                  </li>
                  <li>
                    <a href="/support">Support</a>
                  </li>
                  <li>
                    <a href="http://trac.agavi.org/">Code</a>
                  </li>
                  <li>
                    <a href="http://blog.agavi.org/">Blog</a>
                  </li>
                </ul>
                <a href="/" id="agavi_logo">
                  <img src="http://www.agavi.org/images/logo.png" alt="Agavi" />
                </a>
              </div>
            </div>
          </div>
          <div id="agavi_content_container">
            <div id="agavi_content" class="agavi_content_documentation">

              <div id="agavi_subnav">
                <h1>API Documentation</h1>
              </div>

              <div id="main">

                <table id="page">
                  <tr>
                    <td id="sidebar">
                      <iframe name="nav" id="nav" src="{$root}nav.html" />
                    </td>
                    <td id="contents">
                      <iframe name="content" id="content" src="{$root}content.html" />
                    </td>
                  </tr>
                </table>

              </div>
            </div>
          </div>
        </div>
        <div id="agavi_footer_container">
          <a id="bx" href="http://www.bitextender.com/" title="Bitextender GmbH">
            <img src="images/bx.png" alt="Bitextender" />
          </a>
          © 2005–2011 The Agavi Project. Site design by
          <a href="http://queridodesign.net/">QUERIDO
            <b>DESIGN</b>
          </a>
          , original logo by Dariusz Zieliński.
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>