Building your own branding
==========================

Developing your own branding consists of two steps:


1. Creating a theme
2. Creating a template

Most of the time it is only necessary to replace the graphical
layout with that of your own project. This is an easy process and
requires little explanation on how theming and templating actually
works. Please consult the chapter *Quick version* when the above is
what you desire.

Do you want to control every detail of how your template looks then
it is recommended to read the chapter *Elaborate version*; this
will provide more theory on how branding works in DocBlox.

Quick version
-------------

As described the creation of your own branding consists of 2
things: a theme and a template. I will describe the difference here
in short so you know what each does:


1. Theme, a theme is the visual part; this contains the template
   used to render the looks for your documentation.
2. Template, DocBlox supports the output of different pages; even
   your own. The DocBlox Template determines which template files in
   your theme will be *transformed* into HTML pages. It is even
   possible to copy files and generate graphs using the transformation
   rules in the DocBlox Template.

I will try to give a step-by-step description how to create your
own branding:

Theme
~~~~~


1. Create a new folder in the ``<docblox>/data/themes`` folder with
   the name of your intended theme. Please do *not* use spaces in this
   path name as the XSL Writer (or actually libxsl) cannot handle
   that.
2. Create a new folder ``css`` in your theme's folder
3. Create a new CSS file called ``theme.css`` in your theme's
   ``css`` folder
4. Add the following content to ``css/theme.css``:

   ::

       @import url('navigation.css');
       @import url('api-content.css');
       @import url('default.css');

5. If all you need is to change some or all of the styles you can
   just override them in the theme.css stylesheet; for most projects
   this should work out since DocBlox has been designed to be as
   flexible as possible when it comes to CSS styling.


*If changing the CSS is not enough but you need to change the layout:*


1. create a new file called ``index.xsl`` in your theme's folder
   with the following skeleton code:

   ::

       <xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
         <xsl:output indent="yes" method="html" />
       
         <xsl:template match="/">
           <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
             <head>
               <title><xsl:value-of select="$title" /></title>
               <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
               <link rel="stylesheet" href="{$root}css/black-tie/jquery-ui-1.8.2.custom.css" type="text/css" />
               <link rel="stylesheet" href="{$root}css/theme.css" type="text/css" />
               <script type="text/javascript" src="{$root}js/jquery-1.4.2.min.js"></script>
               <script type="text/javascript" src="{$root}js/jquery-ui-1.8.2.custom.min.js"></script>
             </head>
             <body>
       
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
       
             </body>
           </html>
         </xsl:template>
       
       </xsl:stylesheet>

2. Change the skeleton code above to suit your design and or
   Javascript.

   **Please note:** The iframes will only match 100% of the height of
   your browser without scrollbars if you create your design within
   the table with id #page. If you add other elements outside of this
   table in the body then you will have scrollbars.

   *If you are able to achieve 100% height iframes with a header in all browsers: please contact me and contribute the code as I would love to dump the table.*


Template
~~~~~~~~

Elaborate version
-----------------


