<?php

namespace App\Repository;

use App\Entity\Orderdetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LessonRepository;
use App\Repository\CursusRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Orderdetail>
 *
 * @method Orderdetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orderdetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orderdetail[]    findAll()
 * @method Orderdetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderdetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orderdetail::class);
    }

    public function add(Orderdetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Orderdetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function orderbymonth() {


        $query= $this->createQueryBuilder('od');
        $query->select( 'YEAR(od.created_at) as year, MONTH(od.created_at) as month, SUM(od.price) as total, COUNT(od.ordernumber) as nb_orders' );
        
        $query->groupby('year,month');
        $query ->orderBy('year,month', 'DESC');
        return $query->getQuery()->getResult();
        
       
    }


   
//    /**
//     * @return Orderdetail[] Returns an array of Orderdetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Orderdetail
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
