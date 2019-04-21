<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function getConnect(){
        try {
            return $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->andWhere('p.connexion = true')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getOthersPlayers($id){
        return $this->createQueryBuilder('p')
            ->orderBy('p.username', 'ASC')
            ->andWhere('p.id != :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getConnects($id){
        return $this->createQueryBuilder('p')
            ->orderBy('p.username', 'ASC')
            ->andWhere('p.id != :id')
            ->andWhere('p.connexion = 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function getDisconnect(){
        try {
            return $this->createQueryBuilder('p')
                ->select('count(p.id)')
                ->andWhere('p.connexion = false')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    public function getLatestConnect(){
        return $this->createQueryBuilder('p')
            ->orderBy('p.date_co', 'DESC')
            ->andWhere('p.connexion=true')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

    }

    public function getClassement($points){
        try {
            return $this->createQueryBuilder('p')
                ->andWhere('p.points >= :points')
                ->setParameter('points', $points)
                ->select('count(p.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    // /**
    //  * @return Player[] Returns an array of Player objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Player
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
