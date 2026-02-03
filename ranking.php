<?php

include 'config.php';

$rows = shipsGetRankingData($sort, $max_rank);
$num_players = shipsGetNotDestroyedExcludeXenobeCount();
$current_sort = $rankingData['sort'];

include 'tpls/ranking.tpl.php';
