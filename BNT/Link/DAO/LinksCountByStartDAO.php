<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use Psr\Container\ContainerInterface;

class LinksCountByStartDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $start;
    public int $count;

    #[\Override]
    public function serve(): void
    {
        $this->count = (int) $this->db()->column("SELECT COUNT(*) FROM links WHERE link_start= :sectorId", [
            'sectorId' => $this->start,
        ]);
    }

    public static function call(ContainerInterface $container, int $start): self
    {
        $self = self::new($container);
        $self->start = $start;
        $self->serve();

        return $self;
    }
}
