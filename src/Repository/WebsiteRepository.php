<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\Website\Entity\WebsiteEntity;
use Doctrine\Persistence\ManagerRegistry;

class WebsiteRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(WebsiteEntity::class, $registry);
    }

    /**
     * @return WebsiteEntity[]
     */
    public function loadAll(): array
    {
        /** @var WebsiteEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?WebsiteEntity
    {
        /** @var WebsiteEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function save(WebsiteEntity $entity, bool $flush = true): void
    {
        $manager = $this->getManager();
        $manager->persist($entity);

        if ($flush) {
            $manager->flush($entity);
        }
    }
}
