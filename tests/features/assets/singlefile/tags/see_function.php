<?php
/**
 * This file contains utility functions.
 *
 * @package Utility Functions
 */

namespace SmartFactory;

/**
 * Echoes the text with escaping of HTML special charaters.
 *
 * @param string $text
 * The text to be escaped.
 *
 * @return void
 *
 * @see  escape_html()
 *
 * @uses escape_html()
 */
function echo_html($text)
{
    echo escape_html($text);
} // echo_html

/**
 * Escapes the HTML special characters in the text.
 *
 * @param string $text
 * The text to be escaped.
 *
 * @return string
 * Returns the text with escaped HTML special charaters.
 */
function escape_html($text)
{
    return htmlspecialchars($text, ENT_QUOTES);
} // escape_html
