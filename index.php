<?php

include("config.php");
include("languages/$lang");

connectdb();

if (checklogin(false)) {
    header('Location: login.php');
} else {
    header('Location: main.php');
}
