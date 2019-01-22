<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * By Nacho 2
 * By Nacho 3
 *4
<<<<<<< HEAD
 * juanra
=======
 * 22012019 de vuelta
>>>>>>> e47a7c4e38be3303117a0c770e98ba9fbcad4f8a
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';
