<?php

include("config.php");

if (empty($lang)) {
    $lang = $default_lang;
}

$found = 0;
if (!empty($newlang)) {
    if (!preg_match("/^[\w]+$/", $newlang)) {
        $lang = $default_lang;
    } else {
        foreach ($avail_lang as $key => $value) {
            if ($newlang == $value['file']) {
                $lang = $newlang;
                SetCookie("lang", $lang, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
                $found = 1;
                break;
            }
        }

        if ($found == 0) {
            $lang = $default_lang;
        }
    }
    $lang = $lang . ".inc";
} elseif (!empty($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'] . ".inc";
}

// Подключение языкового файла
if (empty($lang)) {
    $lang = $default_lang . ".inc";
}

include("languages/$lang");
$title = $l_login_title;

$template_data = [
    'title' => $title,
    'l_login_email' => $l_login_email,
    'l_login_pw' => $l_login_pw,
    'l_login_title' => $l_login_title,
    'l_login_newp' => $l_login_newp,
    'l_login_prbs' => $l_login_prbs,
    'l_login_emailus' => $l_login_emailus,
    'l_forums' => $l_forums,
    'l_rankings' => $l_rankings,
    'l_login_settings' => $l_login_settings,
    'l_login_lang' => $l_login_lang,
    'l_login_change' => $l_login_change,
    'username' => isset($username) ? $username : '',
    'password' => isset($password) ? $password : '',
    'admin_mail' => isset($admin_mail) ? $admin_mail : '',
    'link_forums' => isset($link_forums) ? $link_forums : '',
    'avail_lang' => $avail_lang,
    'lang' => $lang,
    'gamepath' => isset($gamepath) ? $gamepath : '',
    'gamedomain' => isset($gamedomain) ? $gamedomain : ''
];

include("login.tpl.php");
