<?php

/**
 * index.php
 *
 * @author Jay Trees <github.jay@grandel.anonaddy.me>
 */

define('VERSION', '0.4.0');
define('ROOT', __DIR__);

/**
 * Include
 */
require 'vendor/autoload.php';

$include = new Grandel\IncludeDirectory(__DIR__ . '/src/classes');
$include = new Grandel\IncludeDirectory(__DIR__ . '/src/functions');

/**
 * Config
 */
$configPath = __DIR__ . '/' . 'src/config/config.php';

if (file_exists($configPath)) {
    require $configPath;
}

/**
 * Database
 */
$database = false;
$options  = false;

if (
       defined('DATABASE_HOST')
    && defined('DATABASE_NAME')
    && defined('DATABASE_USER')
    && defined('DATABASE_PASSWORD')
) {
    $database = new wishthis\Database(
        DATABASE_HOST,
        DATABASE_NAME,
        DATABASE_USER,
        DATABASE_PASSWORD
    );

    /**
     * Options
     */
    $options = new wishthis\Options($database);
}

/**
 * Session
 */
$sessionLifetime = 2592000; // 1 Month

session_set_cookie_params($sessionLifetime, '/');
session_start();

/** Refresh lifetime */
$session = session_get_cookie_params();

setcookie(
    session_name(),
    session_id(),
    time() + $sessionLifetime,
    $session['path'],
    $session['domain'],
    $session['secure'],
    $session['httponly']
);

/**
 * API
 */
if (isset($api)) {
    return;
}

/**
 * Language
 */
/** Determine Locale */
$userLocale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$locales    = array_filter(
    array_map(
        function ($value) {
            if ('po' === pathinfo($value, PATHINFO_EXTENSION)) {
                return pathinfo($value, PATHINFO_FILENAME);
            }
        },
        scandir(ROOT . '/translations')
    )
);
$locale     = \Locale::lookup($locales, $userLocale, false, 'en');

/** Load Translation */
$translationFilepath = ROOT . '/translations/' . $locale . '.po';
$translations        = null;

if (file_exists($translationFilepath)) {
    $loader       = new \Gettext\Loader\PoLoader();
    $translations = $loader->loadFile($translationFilepath);
}

/**
 * Install
 */
if (!$options || !$options->getOption('isInstalled')) {
    $page = 'install';
}

/**
 * User
 */
if ($options) {
    $user = new wishthis\User();
}

/**
 * Update
 *
 * Check for update every 24 hours.
 */
use Github\Client;

if ($options && $options->getOption('isInstalled')) {
    $updateLastChecked = $options->getOption('updateLastChecked');

    if (!$updateLastChecked || time() - $updateLastChecked >= 86400) {
        try {
            $client  = new Client();
            $release = $client->api('repo')->releases()->latest('grandeljay', 'wishthis');
            $tag     = $release['tag_name'];
            $version = str_replace('v', '', $tag);

            if (-1 === version_compare($options->version, $version)) {
                $options->setOption('updateAvailable', true);
            }
        } catch (\Github\Exception\RuntimeException $th) {
            echo wishthis\Page::warning($th->getMessage());
        }

        $options->setOption('updateLastChecked', time());
    }
}

/**
 * Wishlist
 */
if (!isset($_GET['page']) && isset($_GET['wishlist'])) {
    $page = 'wishlist';
}

/**
 * Page
 */
if (!isset($page)) {
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
}
$pagePath = 'src/pages/' . $page . '.php';

if (file_exists($pagePath)) {
    require $pagePath;
} else {
    http_response_code(404);
    ?>
    <h1>Not found</h1>
    <p>The requested URL was not found on this server.</p>
    <?php
    echo $pagePath;
    die();
}
