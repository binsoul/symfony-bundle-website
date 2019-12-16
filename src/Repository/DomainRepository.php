<?php

declare(strict_types=1);

namespace BinSoul\Symfony\Bundle\Website\Repository;

use BinSoul\Symfony\Bundle\Doctrine\Repository\AbstractRepository;
use BinSoul\Symfony\Bundle\Website\Entity\DomainEntity;
use Doctrine\Persistence\ManagerRegistry;

class DomainRepository extends AbstractRepository
{
    /**
     * Constructs an instance of this class.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(DomainEntity::class, $registry);
    }

    /**
     * @return DomainEntity[]
     */
    public function loadAll(): array
    {
        /** @var DomainEntity[] $result */
        $result = $this->getRepository()->findBy([]);

        return $result;
    }

    public function load(int $id): ?DomainEntity
    {
        /** @var DomainEntity|null $result */
        $result = $this->getRepository()->find($id);

        return $result;
    }

    public function save(DomainEntity $entity, bool $flush = true): void
    {
        $manager = $this->getManager();
        $manager->persist($entity);

        if ($flush) {
            $manager->flush($entity);
        }
    }
}
