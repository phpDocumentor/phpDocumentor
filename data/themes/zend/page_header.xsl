<?xml version="1.0"?>
<!DOCTYPE xsl:stylesheet [<!ENTITY nbsp "&#160;">]>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="/project" name="page_header">
    <div class="content">
    <div class="sub-nav">
      <ul>
        <li>
          <a href="http://framework.zend.com/docs/overview" target="_top">Overview</a>
          <span class="divider">|</span>
          <span class="arrow">&nbsp;</span>
        </li>
        <li>
          <a href="http://framework.zend.com/docs/quickstart" target="_top">QuickStart</a>
          <span class="divider">|</span>
          <span class="arrow">&nbsp;</span>
        </li>
        <li>
          <a href="index.html" target="_top">APIs</a>
          <span class="divider">|</span>
          <span class="arrow">&nbsp;</span>
        </li>
        <li>
          <a href="http://framework.zend.com/manual/manual" target="_top">Reference Guide</a>
          <span class="divider">|</span>
          <span class="arrow">&nbsp;</span>
        </li>
        <li>
          <a href="http://framework.zend.com/docs/translations" target="_top">Translations</a>
          <span class="divider">|</span>
          <span class="arrow">&nbsp;</span>
        </li>
        <li>
          <a href="http://framework.zend.com/docs/multimedia" target="_top">Multimedia</a>
          <span class="arrow">&nbsp;</span>
        </li>
      </ul>
    </div>

    <div class="sub-page-main-header-api-documentation">
      <h2>API Documentation</h2>
    </div>

    <div class="dotted-line" />
    </div>
  </xsl:template>

</xsl:stylesheet>