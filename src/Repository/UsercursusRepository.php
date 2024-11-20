<?php

namespace App\Repository;

use App\Entity\Usercursus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usercursus>
 *
 * @method Usercursus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usercursus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usercursus[]    findAll()
 * @method Usercursus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsercursusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usercursus::class);
    }

    public function add(Usercursus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Usercursus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function learningbymonth() {


        $query= $this->createQueryBuilder('uc');
        $query->select( 'YEAR(uc.created_at) as year, MONTH(uc.created_at) as month, uc.repository,uc.learning_id, COUNT(CONCAT(uc.repository,uc.learning_id)) as nb' );
        
        $query->groupby('year,month,uc.repository,uc.learning_id');
        $query ->orderBy('year,month', 'DESC');
        return $query->getQuery()->getResult();
        
       
    }

//    /**
//     * @return Usercursus[] Returns an array of Usercursus objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Usercursus
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
