<?php

include 'config.php';

if (checkuser(false)) {
    redirectTo('login.php');
} else {
    redirectTo('main.php');
}
