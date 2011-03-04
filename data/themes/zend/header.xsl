<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />
  <xsl:include href="../frames/chrome.xsl" />

  <xsl:template match="/project">
    <div id="top">
      <div class="top">
        <h1 class="logo">
          <a href="http://framework.zend.com/" title="ZF Zend Framework">ZF Zend Framework</a>
        </h1>

        <p class="behind-the-site">
          <a href="http://framework.zend.com/behind-the-site">
            <img src="images/behind-the-site.gif" alt="Behind the Site" title="Behind the Site" border="0" />
          </a>
        </p>
      </div>
    </div>
  </xsl:template>

</xsl:stylesheet>