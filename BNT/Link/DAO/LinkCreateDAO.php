<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use Psr\Container\ContainerInterface;

class LinkCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public array $link;
    public int $id;

    #[\Override]
    public function serve(): void
    {
        $this->link['link_id'] = $this->id = (int) $this->rowCreate('links', $this->link);
    }

    public static function call(ContainerInterface $container, array $link): self
    {
        $self = self::new($container);
        $self->link = $link;
        $self->serve();

        return $self;
    }
}
