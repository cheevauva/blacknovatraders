<?php

include("config.php");
include("languages/$lang");

connectdb();

if (!checklogin(false)) {
    header('Location: index.php');
    die;
}

include 'new.tpl.php';

