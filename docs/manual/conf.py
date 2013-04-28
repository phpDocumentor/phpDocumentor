import sys, os

project   = u'phpDocumentor'
copyright = u'2012, Mike van Riel'
version   = '2.0.0'
release   = '2.0.0a10'

sys.path.append(os.path.abspath('.exts'))
extensions     = ['sphinx.ext.intersphinx', 'sphinx.ext.ifconfig', 'plantuml']
templates_path = ['.templates']
source_suffix  = '.rst'
master_doc     = 'index'

exclude_patterns = ['.build']
pygments_style = 'sphinx'

# -- Options for HTML output ---------------------------------------------------

html_theme = 'default'
html_title = 'phpDocumentor'
#html_favicon = None
html_static_path = ['.static']

# If true, an OpenSearch description file will be output, and all pages will
# contain a <link> tag referring to it.  The value of this option must be the
# base URL from which the finished HTML is served.
#html_use_opensearch = ''

# Output file base name for HTML help builder.
htmlhelp_basename = 'phpDocumentor'

# -- Options for LaTeX output --------------------------------------------------

latex_paper_size = 'a4'
#latex_font_size = '10pt'

# (source start file, target name, title, author, documentclass [howto/manual]).
latex_documents = [
  ('for-users', 'phpDocumentor.tex', u'phpDocumentor', u'Mike van Riel', 'manual'),
  ('for-template-builders', 'phpDocumentor-for-template-builders.tex', u'phpDocumentor', u'Mike van Riel', 'manual'),
  ('for-developers', 'phpDocumentor-for-developers.tex', u'phpDocumentor', u'Mike van Riel', 'manual'),
  ('for-developers/serialization', 'phpDocumentor-serialization.tex', u'phpDocumentor', u'Mike van Riel', 'manual'),
]

# The name of an image file (relative to this directory) to place at the top of
# the title page.
#latex_logo = None

# For "manual" documents, if this is true, then toplevel headings are parts,
# not chapters.
#latex_use_parts = False

# Documents to append as an appendix to all manuals.
#latex_appendices = []

# -- Options for manual page output --------------------------------------------

# One entry per manual page. List of tuples
# (source start file, name, description, authors, manual section).
man_pages = [
    ('index', 'phpDocumentor', u'phpDocumentor', [u'Mike van Riel'], 1)
]

# -- Options for Epub output ---------------------------------------------------

# Bibliographic Dublin Core info.
epub_title      = u'phpDocumentor'
epub_author     = u'Mike van Riel'
epub_publisher  = u'Mike van Riel'
epub_copyright  = u'2012, Mike van Riel'
epub_scheme     = 'http://www.phpdoc.org'
epub_identifier = 'http://www.phpdoc.org'

# A unique identification for the text.
#epub_uid = ''

# HTML files that should be inserted before the pages created by sphinx.
# The format is a list of tuples containing the path and title.
#epub_pre_files = []

# HTML files shat should be inserted after the pages created by sphinx.
# The format is a list of tuples containing the path and title.
#epub_post_files = []

# A list of files that should not be packed into the epub file.
#epub_exclude_files = []

# The depth of the table of contents in toc.ncx.
#epub_tocdepth = 3

# Allow duplicate toc entries.
#epub_tocdup = True

# Example configuration for intersphinx: refer to the Python standard library.
intersphinx_mapping = {'http://docs.python.org/': None}

# UML diagramming tool
plantuml = ['java', '-jar', '.exts/plantuml.jar']
plantuml_latex_output_format = 'pdf'