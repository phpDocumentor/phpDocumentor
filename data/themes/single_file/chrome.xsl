<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title><xsl:value-of select="$title" /></title>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
        <link rel="stylesheet" href="{$root}css/default.css" type="text/css" />
        <style type="">
          body
          {
            background: white;
          }
          #header
          {
            background: white;
            height: auto;
            padding: 0px;
            margin: 0px;
            margin-bottom: 10px;
          }
          #maincontainer, #content_container, #content
          {
            width: 100%;
            margin: 0px;
            padding: 0px;
          }
        </style>
      </head>
      <body>
        <div id="maincontainer">

          <div id="header">
            <h1><xsl:value-of select="//@title" disable-output-escaping="yes"/></h1>
          </div>

          <div id="content_container">
            <xsl:apply-templates />
          </div>

        </div>

      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>