system.debug
============

This event is triggered any time phpDocumentor logs an action.

At certain places in the code a logging event is triggered by invoking the method
``$this->log()`` (which is defined in the Layer Superclass of each component.).

This method has **two** arguments:

========= ============================================================
Name      Description
========= ============================================================
message   The message that needs to be logged.
priority  The priority or urgency of the logging, ranging from 0 to 7
          where the lowest number is the most crucial error or logging
========= ============================================================

Typical uses for this event is grabbing the logging events and sending them to
a collector or outputting them.

