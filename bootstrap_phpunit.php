<?php

declare(strict_types=1);

require 'bootstrap.php';

$language = 'english';

$languageFileMain = sprintf('languages/%s.php', $language);
$languageFileSub = sprintf('languages/%s%s', $language, $_SERVER['PHP_SELF']);

include $languageFileMain;

if (file_exists($languageFileSub)) {
    include $languageFileSub;
}
