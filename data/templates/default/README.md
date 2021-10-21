Default Template
================

This template was designed to slot into most websites and provide a large degree of customizability with little effort.

External assets
---------------

The template leans on Google Fonts, icons, tailwind and some other external requirements; these are all built, using 
webpack, into a series of distributable assets. This is all done by the phpDocumentor development team and the build 
assets are included in this template.

> This is done to ensure people using phpDocumentor do not rely on Javascript and webpack as a dependency; in the future
> we can investigate whether we can provide this as an option for even more customization options.

As a contributor, to (re)built the distribution files; you only need to run the following command in this directory:

```shell
$ webpack
```
