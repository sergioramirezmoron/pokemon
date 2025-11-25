<?php

namespace App\Repository;

use App\Entity\LinePokemon;
use App\Entity\Pokemon;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.numPokemon', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchByName($name): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :val')
            ->setParameter('val', '%' . $name . '%')
            ->orderBy('p.numPokemon', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchByNameInPokedex(string $name, User $user): array
    {
        $pokedexIds = array_filter(array_map(
            static fn (LinePokemon $linePokemon) => $linePokemon->getPokemon()?->getId(),
            $user->getLinePokemon()->toArray()
        ));

        if (empty($pokedexIds)) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.id IN (:pokedex)')
            ->setParameter('pokedex', $pokedexIds)
            ->andWhere('p.name LIKE :val')
            ->setParameter('val', '%' . $name . '%')
            ->orderBy('p.numPokemon', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Pokemon[] Returns an array of Pokemon objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pokemon
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
