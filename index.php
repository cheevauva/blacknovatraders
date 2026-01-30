<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin(false)) {
    $no_body = 1;
    include 'index.tpl.php';
} else {
    header('Location: main.php');
}
