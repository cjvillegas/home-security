<?php
/*
  |--------------------------------------------------------------------------
  | Our Custom Globally Accessible Methods
  |--------------------------------------------------------------------------
  |
  | Methods defined here are globally accessible within our application, this file is auto-loaded
  | in our composer.json file. If you want to add new methods that you deemed should be globally available
  | then add it here. Just make sure you follow the naming convention to prevent collisions to laravel's
  | internal global methods.
  */

/**
 * Get the current timezone from the configuration/environment
 * if no timezone config found the function is configured to fallback
 * into GMT.
 *
 * @param string $timezone
 *
 * @return string
 */
function __env_timezone($timezone = 'GMT'): string
{
    return env('UK_TIMEZONE', $timezone);
}
