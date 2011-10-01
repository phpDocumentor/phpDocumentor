<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:dbx="http://docblox-project.org/xsl/functions">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="file">
    <!--<script type="text/javascript" src="{$root}js/menu.js"/>-->
    <script><![CDATA[

      function filterElements()
      {
        inherited = !$('a#show-inherited').hasClass('deselected');
        public    = !$('a#show-public').hasClass('deselected');
        protected = !$('a#show-protected').hasClass('deselected');
        private   = !$('a#show-private').hasClass('deselected');

        $('div.public').each(function(index, val) {
            $(val).toggle(public && !($(val).hasClass('inherited_from') && !inherited));
        });
        $('div.protected').each(function(index, val) {
            $(val).toggle(protected && !($(val).hasClass('inherited_from') && !inherited));
        });
        $('div.private').each(function(index, val) {
            $(val).toggle(private && !($(val).hasClass('inherited_from') && !inherited));
        });
      }

      $(document).ready(function() {
        $('a.gripper').click(function() {
            $(this).nextAll('div.code-tabs').slideToggle();
            $(this).children('img').toggle();
            return false;
        });

        $('div.method code span.highlight,div.function code span.highlight,div.constant code span.highlight,div.property code span.highlight').css('cursor', 'pointer');

        $('div.method code span.highlight,div.function code span.highlight,div.constant code span.highlight,div.property code span.highlight').click(function() {
            $(this).parent().nextAll('div.code-tabs').slideToggle();
            $(this).parent().prevAll('a.gripper').children('img').toggle();
            return false;
        });

        $('div.code-tabs').hide();
        $('a.gripper').show();
        $('div.code-tabs:empty').prevAll('a.gripper').html('');
        $('div#file-nav').show();

        $('a#show-public, a#show-protected, a#show-private, a#show-inherited').click(function(){
            $(this).toggleClass('deselected');
            if ($(this).hasClass('deselected')) {
              $(this).fadeTo('fast', '0.4');
            } else {
              $(this).fadeTo('fast', '1.0');
            }
            filterElements();
            return false;
        });
        $('a#show-protected, a#show-private').click();

        $("div#file-nav").hover(function() {
            console.debug('down');
            $("div#file-nav-container").slideDown(400);
        }, function() {
            console.debug('up');
            $("div#file-nav-container").slideUp(400);
        });
      });

      ]]>
    </script>
      <h1 class="file"><xsl:value-of select="@path" /></h1>
      <div id="file-nav">
          <div id="file-nav-options">
              Show:
              <a href="#" id="show-public"><img src="{$root}images/icons/visibility_public.png"/></a>
              <a href="#" id="show-protected"><img src="{$root}images/icons/visibility_protected.png"/></a>
              <a href="#" id="show-private"><img src="{$root}images/icons/visibility_private.png"/></a>
              <a href="#" id="show-inherited"><span class="attribute">inherited</span></a>
          </div>

          <div id="file-nav-container">
              <ul id="file-nav">
                  <xsl:if test="constant">
                  <li>
                      <a href="#constants">
                          <img src="{$root}images/icons/constant.png" height="14" />
                          Constants
                      </a>
                      <ul>
                      <xsl:for-each select="constant">
                          <xsl:sort select="name" />
                          <li><a href="#::{name}"><xsl:value-of select="name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>

                  <xsl:if test="constant">
                  <li>
                      <a href="#functions">
                          <img src="{$root}images/icons/function.png" height="14"/>
                          Functions
                      </a>
                      <ul>
                      <xsl:for-each select="function">
                          <xsl:sort select="name"/>
                          <li><a href="#::{name}()"><xsl:value-of select="name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>

                  <xsl:if test="class">
                  <li>
                      <a href="#classes">
                          <img src="{$root}images/icons/class.png" height="14"/>
                          Classes
                      </a>
                      <ul>
                      <xsl:for-each select="class">
                          <xsl:sort select="full_name"/>
                          <li><a href="#{full_name}"><xsl:value-of select="full_name"/></a></li>
                      </xsl:for-each>
                      </ul>
                  </li>
                  </xsl:if>
              </ul>
              <div style="clear: left;"></div>
          </div>

          <div id="file-nav-tab">
              Table of Contents
          </div>
      </div>

      <a name="top" class="anchor"/>
      <xsl:if test="docblock/description|docblock/long-description">
          <div id="file-description">
              <xsl:apply-templates select="docblock/description"/>
              <xsl:apply-templates select="docblock/long-description"/>
          </div>
      </xsl:if>

      <xsl:if test="count(docblock/tag) > 0">
      <dl class="file-info">
          <xsl:apply-templates select="docblock/tag">
            <xsl:sort select="dbx:ucfirst(@name)" />
          </xsl:apply-templates>
      </dl>
      </xsl:if>

    <xsl:if test="count(constant) > 0">
    <a name="constants" class="anchor" />
    <h2>Constants</h2>
    <div>
      <xsl:apply-templates select="constant"/>
    </div>
    </xsl:if>

    <xsl:if test="count(function) > 0">
    <a name="functions" class="anchor" />
    <h2>Functions</h2>
    <div>
      <xsl:apply-templates select="function">
        <xsl:sort select="name" />
      </xsl:apply-templates>
    </div>
    </xsl:if>

    <xsl:if test="count(class) > 0">
    <a name="classes" class="anchor" />
    <xsl:apply-templates select="class">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

    <xsl:if test="count(interface) > 0">
    <a name="interfaces" class="anchor" />
    <xsl:apply-templates select="interface">
      <xsl:sort select="name" />
    </xsl:apply-templates>
    </xsl:if>

  </xsl:template>

</xsl:stylesheet>