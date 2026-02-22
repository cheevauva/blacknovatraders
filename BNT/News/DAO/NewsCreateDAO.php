<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use Psr\Container\ContainerInterface;

class NewsCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    public array $news;

    #[\Override]
    public function serve(): void
    {
        $this->rowCreate('news', $this->news);
    }

    public static function call(ContainerInterface $container, array $news): self
    {
        $self = self::new($container);
        $self->news = $news;
        $self->serve();

        return $self;
    }
}
