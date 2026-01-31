<?php

include("config.php");
include("languages/$lang");
connectdb();

$rows = sqlGetRankingData($sort, $max_rank);
$num_players = sqlGetNumPlayers();

$current_sort = $rankingData['sort'];

include 'ranking.tpl.php';
