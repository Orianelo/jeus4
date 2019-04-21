<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getAllTables($id){
        return $this->createQueryBuilder('g')
            ->innerJoin('g.j2', 'p')
            ->andWhere('p.id != :id')
            ->andWhere('g.j1 is NULL')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getEnCours1($id){
        return $this->createQueryBuilder('g')
            ->innerJoin('g.j1', 'p1')
            ->andWhere('p1.id = :id1')
            ->setParameter('id1', $id)
            ->getQuery()
            ->getResult();
    }

    public function getEnCours2($id){
        return $this->createQueryBuilder('g')
            ->innerJoin('g.j2', 'p2')
            ->andWhere('p2.id = :id2')
            ->setParameter('id2', $id)
            ->getQuery()
            ->getResult();
    }

    public function getFinished(){
        try {
            return $this->createQueryBuilder('g')
                ->select('count(g.id)')
                ->andWhere('g.etat = true')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getNonFinished(){
        try {
            return $this->createQueryBuilder('g')
                ->select('count(g.id)')
                ->andWhere('g.etat = false')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getLatestPlayed(){
        return $this->createQueryBuilder('g')
            ->orderBy('g.date_en_cours', 'DESC')
            ->andWhere('g.etat=false')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

    }

    public function getLatestFinish(){
        return $this->createQueryBuilder('g')
            ->orderBy('g.date_fin', 'DESC')
            ->andWhere('g.etat=true')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

    }

    public function getVictory($victory){
        try {
            return $this->createQueryBuilder('g')
                ->select('count(g.id)')
                ->andWhere('g.type_victoire = :val')
                ->setParameter('val', $victory)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }

    }

    public function nbPartiesJouees($player){
        try {
            return $this->createQueryBuilder('g')
                ->select('count(g.id)')
                ->andWhere('g.j1 = :joueur')
                ->orWhere('g.j2 = :joueur')
                ->setParameter('joueur', $player)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getPartiesFinies($player){
        return $this->createQueryBuilder('g')
            ->orderBy('g.date_fin', 'DESC')
            ->andWhere('g.j1 = :joueur')
            ->orWhere('g.j2 = :joueur')
            ->andWhere('g.gagnant = 1')
            ->orWhere('g.gagnant = 2')
            ->setParameter('joueur', $player)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Game[] Returns an array of Game objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
