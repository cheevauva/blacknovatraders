<?php

use BNT\Ship\DAO\ShipsGetRankingDAO;
use BNT\Ship\DAO\ShipGetNotDestroyedExcludeXenobeCountDAO;

$disableRegisterGlobalFix = true;

include 'config.php';

$getRanking = ShipsGetRankingDAO::new($container);
$getRanking->sort = fromGET('sort');
$getRanking->max_rank = $max_rank;
$getRanking->serve();

$rows = $getRanking->ranking;
$num_players = ShipGetNotDestroyedExcludeXenobeCountDAO::new($container)->count;
$current_sort = $rankingData['sort'];

include 'tpls/ranking.tpl.php';
