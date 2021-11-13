<?php

/**
 * This is a well documented action.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action('good_doc_static_action', $option, $old_value, $value);

/**
 * This is a well documented dynamic action.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action('good_doc_dynamic_action_' . $option, $old_value, $value);

/**
 * This is a well documented dynamic action.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action("good_doc_double_quotes_dynamic_action_$option", $old_value, $value);

/**
 * This is an action missing the "since" line.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action('missing_since_static_action', $option, $old_value, $value);

/**
 * This is a dynamic action missing the "since" line.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action('missing_since_dynamic_action_' . $option, $old_value, $value);

/**
 * This is a dynamic action missing the "since" line.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @param string $option Name of the option to update.
 * @param mixed $old_value The old option value.
 * @param mixed $value The new option value.
 */
do_action("missing_since_double_quotes_dynamic_action_$option", $old_value, $value);

/**
 * This is an action missing a "param" line.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $value The new option value.
 */
do_action('missing_param_static_action', $option, $old_value, $value);

/**
 * This is a well documented dynamic action.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $value The new option value.
 */
do_action('missing_param_dynamic_action_' . $option, $old_value, $value);

/**
 * This is a well documented dynamic action.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
 * Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
 *
 * @since 2.9.0
 *
 * @param string $option Name of the option to update.
 * @param mixed $value The new option value.
 */
do_action("missing_param_double_quotes_dynamic_action_$option", $old_value, $value);

do_action('no_doc_static_action', $option, $old_value, $value);
do_action('no_doc_dynamic_action_' . $option, $old_value, $value);
do_action("no_doc_double_quotes_dymanic_action_$option", $old_value, $value);
