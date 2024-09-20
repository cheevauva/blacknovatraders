<?php

declare(strict_types=1);

use BNT\News\DAO\NewsRetrieveManyByCriteriaDAO;

require_once './config.php';
loadlanguage($lang);

connectdb();

$startdate = new \DateTimeImmutable($_GET['startdate'] ?? 'now');

$previousday = $startdate->sub(new \DateInterval('P1D'));
$nextday = $startdate->add(new \DateInterval('P1D'));

$retrieveNews = new NewsRetrieveManyByCriteriaDAO;
$retrieveNews->dateFrom = $previousday;
$retrieveNews->dateTo = $nextday;
$retrieveNews->sortByNewsIdDESC = true;
$retrieveNews->serve();

echo twig()->render('news.twig', [
    'news' => $retrieveNews->news,
    'startdate' => $startdate,
    'nextday' => $nextday,
    'previousday' => $previousday,
]);
