<?php

$self = \BNT\Controller\InfoPubliclistController::as($self);

header('Content-Type: text/plain');

foreach ($self->info as $key => $value) {
    echo $key . ":" . $value . "\n";
}
