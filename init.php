<?php

declare(strict_types=1);

use BNT\Config\DAO\ConfigReadDAO;
use BNT\Ship\DAO\ShipByTokenDAO;
use UUA\Container\Container;
use BNT\ADODB\ADOPDO;

if (empty($disableRegisterGlobalFix)) {
    foreach ($_POST as $k => $v) {
        if (!isset($GLOBALS[$k])) {
            ${$k} = $v;
        }
    }
    foreach ($_GET as $k => $v) {
        if (!isset($GLOBALS[$k])) {
            ${$k} = $v;
        }
    }

    foreach ($_COOKIE as $k => $v) {
        if (!isset($GLOBALS[$k])) {
            ${$k} = $v;
        }
    }
}

$db = new ADOPDO(sprintf("%s:host=%s;port=%s;dbname=%s;charset=utf8mb4", $db_type, $dbhost, $dbport, $dbname), $dbuname, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$container = new Container(fn($c) => [
    'db' => $db,
]);

if (empty($disableConfigRewrite)) {
    try {
        foreach (ConfigReadDAO::call($container)->config as $name => $value) {
            $$name = $value;
        }
    } catch (\Exception $ex) {
        
    }
}

$playerinfo = null;
$token = $_COOKIE['token'] ?? uuidv7();

if (empty($disableAutoLogin) && !empty($token)) {
    $playerinfo = ShipByTokenDAO::call($container, (string) $token)->ship;
}

$language = $default_lang;

if (!empty($playerinfo['lang'])) {
    $language = $playerinfo['lang'];
} else {
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        switch (mb_strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2))) {
            case 'ru':
                $language = 'russian';
                break;
            case 'en':
                $language = 'english';
                break;
        }
    }
}

$languageFileMain = sprintf('languages/%s.php', $language);
$languageFileSub = sprintf('languages/%s%s', $language, $_SERVER['PHP_SELF']);

include $languageFileMain;

if (file_exists($languageFileSub)) {
    include $languageFileSub;
}

if (!empty($_COOKIE['token'])) {
    setcookie("token", $_COOKIE['token'], time() + (3600 * 24) * 365, $gamepath, $gamedomain);
}
