# Custom Highlight Templates

The highlight.php library takes its highlighting metadata/rules
from highlight.js. That library builds the highlighting metadata/rules
in Node and then outputs them as JSON:

* Source Language files: https://github.com/highlightjs/highlight.js/tree/master/src/languages
* Final Language files: https://github.com/scrivo/highlight.php/tree/master/Highlight/languages

In a few cases, we've extended the language rules, which (in theory)
should make it back upstream to highlight.js. These files began
as copies of the .json files (which were then prettified) then extended.

A few things we've learned about how the language files work:

* `begin` is the regex that marks the beginning of something
* `end` is optional. Without it, `begin` will be used, and as
    soon as it finds a non-matching character, it will stop.
    If you have a situation where using begin is causing
    over-matching, then you can use end to tell it exactly where
    to stop.
* `excludeEnd` can be used to match an entire string with `begin`
    and `end`, but then only apply the class name to the part
    matched by `start`. This was useful with `::` where we wanted
    to match `::` THEN some valid string (to avoid over-matching
    `::` in other situations). But, we only wanted the class name
    applied to the `start` part (the `::` part).
* `contains` the way for building embedded rules. `function` is
    a nice example, which outlines the `start` and `end` and then
    also outlines some items that will be embedded inside of it.
