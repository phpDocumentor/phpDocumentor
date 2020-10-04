<?php
class Issue2440
{
    /**
     * Function with a inline dev notes.
     *
     * - This function should show in the docs like normal.
     * - The dev notes should not show in the docs.
     *
     * {@internal Dev note with single closing brace.}
     *
     * {@internal Dev note old-style, double closing brace.}}
     *
     * @return bool
     */
    function inlineInternal() {}
}
