<?php

include("config.php");
include("languages/$lang");

$found = 0;

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

include("tpls/login.tpl.php");
