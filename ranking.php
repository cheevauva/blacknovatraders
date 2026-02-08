<?php

include 'config.php';

$rows = BNT\ShipFunc::shipsGetRankingData($sort, $max_rank);
$num_players = BNT\ShipFunc::shipsGetNotDestroyedExcludeXenobeCount();
$current_sort = $rankingData['sort'];

include 'tpls/ranking.tpl.php';
