<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Link\DAO\LinksByStartDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;

class GameFindSectorForAttackServant extends \UUA\Servant
{

    public array $playerinfo;
    public protected(set) int $sector;

    #[\Override]
    public function serve(): void
    {
        global $sector_max;

        $this->sector = 0;

        // Obtain a target link
        $links = LinksByStartDAO::call($this->container, $this->playerinfo['sector'])->links;

        foreach ($links as $link) {
            $sector = SectorByIdDAO::call($this->container, $link['link_dest'])->sector;
            $zone = ZoneByIdDAO::call($this->container, $sector['zone_id'])->zone;

            // Dest link must allow attacking
            // 33% Chance of replacing destination link with this one
            //  Unless there is no dest link, choose this one
            if ($zone['allow_attack'] == 'Y' && rand(0, 2) == 0) {
                $this->sector = $link['link_dest'];
            }

            if (!empty($this->sector)) {
                break;
            }
        }

        // If there is no acceptable link, use a worm hole.
        if (empty($this->sector)) {
            $wormto = rand(1, ($sector_max - 15));
            $limitloop = 1;

            while (empty($this->sector) && $limitloop < 15) {
                $sector = SectorByIdDAO::call($this->container, $wormto)->sector;
                $zone = ZoneByIdDAO::call($this->container, $sector['zone_id'])->zone;

                if ($zone['allow_attack'] == 'Y') {
                    $this->sector = $wormto;
                }

                $wormto++;
                $wormto++;
                $limitloop++;
            }
        }
    }
}
