<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Game\Servant\GameCalculateTriptimeServant;

class MoveRSController extends MoveController
{

    public protected(set) bool $isRSMove = true;
    public protected(set) int $engage = 0;

    #[\Override]
    protected function preProcess(): void
    {
        parent::preProcess();

        $this->title = $this->t('l_rs_title');
        $this->engage = $this->fromQueryParams('engage')->asInt();

        $calculateTriptime = GameCalculateTriptimeServant::new($this->container);
        $calculateTriptime->ship = $this->playerinfo;
        $calculateTriptime->sector = $this->sector;
        $calculateTriptime->serve();

        $this->turns = $calculateTriptime->triptime;
        $this->energyScooped = $calculateTriptime->energyScooped;
    }

    #[\Override]
    protected function checkAccess(): void
    {

    }

    protected function clearedDefencesRoute(): string
    {
        return route('rsmove', [
            'sector' => $this->sector,
            'engage' => $this->engage,
        ]);
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/movers.tpl.php');
    }
}
