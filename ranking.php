<?php

use BNT\Ship\ShipRanking\DAO\ShipRankingTopScoreCachedDAO;
use BNT\Ship\ShipRanking\DAO\ShipRankingTopTurnsUsedCachedDAO;
use BNT\Ship\ShipRanking\DAO\ShipRankingTopRatingCachedDAO;
use BNT\Ship\View\ShipView;

require_once './config.php';

loadlanguage($lang);

connectdb();

echo twig()->render('ranking/ranking.twig', [
    'shipsAsTopScore' => ShipView::map(ShipRankingTopScoreCachedDAO::call($container)),
    'shipsAsTopTurnsUsed' => ShipView::map(ShipRankingTopTurnsUsedCachedDAO::call($container)),
    'shipsAsTopRating' => ShipView::map(ShipRankingTopRatingCachedDAO::call($container)),
]);
