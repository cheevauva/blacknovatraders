<?php

include 'config.php';

if (checklogin(false)) {
    redirectTo('login.php');
} else {
    redirectTo('main.php');
}
