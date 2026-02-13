<?php

declare(strict_types=1);

namespace BNT\News\DAO;

use Psr\Container\ContainerInterface;

class NewsByDateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public string $date;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $news;

    #[\Override]
    public function serve(): void
    {
        $this->news = $this->db()->fetchAll("SELECT * FROM news WHERE date = :date ORDER BY news_id DESC", [
            'date' => $this->date,
        ]);
    }

    public static function call(ContainerInterface $container, string $date): self
    {
        $self = self::new($container);
        $self->date = $date;
        $self->serve();

        return $self;
    }
}
