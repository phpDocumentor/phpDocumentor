Linker
======

The linker is a :term:`compiler pass` that is responsible for substituting a *Fully Qualified Structural Element Name*
(FQSEN) in the Project Descriptor's Files section, and any of its children, with an `object alias`_ to the Descriptor
object belonging to that FQSEN.

The linker needs to be fed with information on how to find a FQSEN and optionally which type of FQSEN is expected.
This can be done by providing Substitutions that describes the field names for a class that must be replaced.

Activity Diagram
----------------

.. uml::

    start

    :#d1ed57: Load linking rules;

        while (linking rules for ProjectDescriptor remaining?)
            :#d1ed57: Find target;
            if (rule is scan) then (yes)
                :#d1ed57: find applicable linking rules based\non class name;
                :#d1ed57: apply linking rules for target;
            else (no)
                if (rule is replace) then (yes)
                    :#d1ed57: Retrieve value using getter;
                    if (value is FQSEN) then (yes)
                    :#d1ed57: Find FQSEN's Descriptor(s) in index;
                        if (Descriptor's type matches limitation or no limitation) then (yes)
                            :#d1ed57: Sort Descriptors in order\nof precedence;
                            :#d1ed57: Write first Descriptor to field\n using setter;
                        else (no)
                            if (Descriptor type does not match limitation) then (yes)
                                :#d1ed57: record error;
                            endif
                        endif
                    endif
                endif
            endif
        endwhile

        stop

.. _`object alias`: http://php.net/manual/en/language.oop5.references.php
