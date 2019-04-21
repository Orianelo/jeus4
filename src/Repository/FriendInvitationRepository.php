<?php

namespace App\Repository;

use App\Entity\FriendInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FriendInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method FriendInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method FriendInvitation[]    findAll()
 * @method FriendInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendInvitationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FriendInvitation::class);
    }

    public function getNumberInvitation($id){
        try {
            return $this->createQueryBuilder('fi')
                ->select('count(fi.id)')
                ->andWhere('fi.joueurInvite = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        }
    }

    // /**
    //  * @return FriendInvitation[] Returns an array of FriendInvitation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FriendInvitation
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
