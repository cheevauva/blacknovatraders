<?php

declare(strict_types=1);

namespace BNT\Controller;

class AdminConfigController extends BaseController
{

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->module === 'univedit' && $this->operation === 'doexpand') {
            $radius = (int) fromPOST('radius', new \Exception('radius'));
            ConfigUpdateDAO::call($this->container, [
                'universe_size' => $radius,
            ]);

            db()->q('UPDATE universe SET distance = FLOOR(RAND() * :radius) WHERE 1 = 1', [
                'radius' => $radius + 1,
            ]);
        }
    }
}
