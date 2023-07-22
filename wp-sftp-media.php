<?php

/*
Plugin Name: WordPress SFTP Media Library
Plugin URI: http://wordpress.stackexchange.com/questions/74180/upload-images-to-remote-server
Description: Uploads media attachments to a remote server and removes them locally.
Version: 1.0
Author: Bogdan Arizancu
Author URI: https://github.com/bogdanarizancu
*/

namespace WPSFTPMediaLibrary;

// Load composer dependencies, namely phpseclib/phpseclib.
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

/**
 * Loads PSR-4-style plugin classes.
 */
function classloader($class)
{
    static $ns_offset;
    if (strpos($class, __NAMESPACE__ . '\\') === 0) {
        if ($ns_offset === null) {
            $ns_offset = strlen(__NAMESPACE__) + 1;
        }
        include __DIR__ . '/src/' . strtr(substr($class, $ns_offset), '\\', '/') . '.php';
    }
}
spl_autoload_register(__NAMESPACE__ . '\classloader');

add_filter('wp_generate_attachment_metadata', [(new Plugin()), 'uploadRemoteRemoveLocally']);
