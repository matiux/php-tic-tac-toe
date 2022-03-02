<?php

declare(strict_types=1);

namespace TicTacToe\Play\Infrastructure\Domain\Doctrine\Aggregate;

use DDDStarterPack\Aggregate\Infrastructure\Doctrine\Repository\DoctrineRepository;
use PDO;
use TicTacToe\Play\Domain\Aggregate\Matches;
use TicTacToe\Play\Domain\Aggregate\Play;
use TicTacToe\Play\Domain\Aggregate\PlayId;

class DoctrineMatches extends DoctrineRepository implements Matches
{
    public function nextId(): PlayId
    {
        return PlayId::create();
    }

    public function add(Play $aPlay): void
    {
        $this->em->persist($aPlay);
        $this->em->flush();
    }

    protected function getEntityAliasName(): string
    {
        return 'p';
    }

    public function withId(PlayId $playId): null|Play
    {
        /** @var null|Play $play */
        $play = $this->em->createQueryBuilder()
            ->select($this->getEntityAliasName())
            ->from($this->getEntityClassName(), $this->getEntityAliasName())
            ->where(sprintf('%s.playId = :playId', $this->getEntityAliasName()))
            ->setParameter('playId', $playId, PDO::PARAM_STR)
            ->getQuery()
            ->getOneOrNullResult();

        return $play;
    }

    public function update(Play $aPlay): void
    {
        $this->em->persist($aPlay);
        $this->em->flush();
    }
}
