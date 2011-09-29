# -*- coding: utf-8 -*-
"""
    sphinxcontrib.plantuml
    ~~~~~~~~~~~~~~~~~~~~~~

    Embed PlantUML diagrams on your documentation.

    :copyright: Copyright 2010 by Yuya Nishihara <yuya@tcha.org>.
    :license: BSD, see LICENSE for details.
"""
import os, subprocess
try:
    from hashlib import sha1
except ImportError:  # Python<2.5
    from sha import sha as sha1
from docutils import nodes
from docutils.parsers.rst import directives
from sphinx.errors import SphinxError
from sphinx.util.compat import Directive
from sphinx.util.osutil import ensuredir, ENOENT

class PlantUmlError(SphinxError):
    pass

class plantuml(nodes.General, nodes.Element):
    pass

class UmlDirective(Directive):
    """Directive to insert PlantUML markup

    Example::

        .. uml::
           :alt: Alice and Bob

           Alice -> Bob: Hello
           Alice <- Bob: Hi
    """
    has_content = True
    option_spec = {'alt': directives.unchanged}

    def run(self):
        node = plantuml()
        node['uml'] = '\n'.join(self.content)
        node['alt'] = self.options.get('alt', None)
        return [node]

def generate_name(self, node):
    key = sha1(node['uml'].encode('utf-8')).hexdigest()
    fname = 'plantuml-%s.png' % key
    imgpath = getattr(self.builder, 'imgpath', None)
    if imgpath:
        return ('/'.join((self.builder.imgpath, fname)),
                os.path.join(self.builder.outdir, '_images', fname))
    else:
        return fname, os.path.join(self.builder.outdir, fname)

def generate_plantuml_args(self):
    if isinstance(self.builder.config.plantuml, basestring):
        args = [self.builder.config.plantuml]
    else:
        args = list(self.builder.config.plantuml)
    args.extend('-pipe -charset utf-8'.split())
    return args

def render_plantuml(self, node):
    refname, outfname = generate_name(self, node)
    if os.path.exists(outfname):
        return refname  # don't regenerate
    ensuredir(os.path.dirname(outfname))
    f = open(outfname, 'wb')
    try:
        try:
            p = subprocess.Popen(generate_plantuml_args(self), stdout=f,
                                 stdin=subprocess.PIPE, stderr=subprocess.PIPE)
        except OSError, err:
            if err.errno != ENOENT:
                raise
            raise PlantUmlError('plantuml command %r cannot be run'
                                % self.builder.config.plantuml)
        serr = p.communicate(node['uml'].encode('utf-8'))[1]
        if p.returncode != 0:
            raise PlantUmlError('error while running plantuml\n\n' + serr)
        return refname
    finally:
        f.close()

def html_visit_plantuml(self, node):
    try:
        refname = render_plantuml(self, node)
    except PlantUmlError, err:
        self.builder.warn(str(err))
        raise nodes.SkipNode
    self.body.append(self.starttag(node, 'p', CLASS='plantuml'))
    self.body.append('<img src="%s" alt="%s" />\n'
                     % (self.encode(refname),
                        self.encode(node['alt'] or node['uml'])))
    self.body.append('</p>\n')
    raise nodes.SkipNode

def latex_visit_plantuml(self, node):
    try:
        refname = render_plantuml(self, node)
    except PlantUmlError, err:
        self.builder.warn(str(err))
        raise nodes.SkipNode
    self.body.append('\\includegraphics{%s}' % self.encode(refname))
    raise nodes.SkipNode

def setup(app):
    app.add_node(plantuml,
                 html=(html_visit_plantuml, None),
                 latex=(latex_visit_plantuml, None))
    app.add_directive('uml', UmlDirective)
    app.add_config_value('plantuml', 'plantuml', 'html')
