<?php

namespace App\Repository;

use App\Entity\UserCursusLesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<UserCursusLesson>
 *
 * @method UserCursusLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCursusLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCursusLesson[]    findAll()
 * @method UserCursusLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCursusLessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCursusLesson::class);
    }

    public function add(UserCursusLesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCursusLesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function existlessonbyuser($userId,$lessonId) {
        $query= $this->createQueryBuilder('ucl');
           
           $query->where('ucl.user = :user');
           $query->andWhere('ucl.learning = :learning');
           $query ->setParameter('user', $userId);
           $query ->setParameter('learning', $lessonId);
           
               
        return $query->getQuery()->getResult();

    }

    public function existcursusbyuser($userId,$cursusId) {
        $query= $this->createQueryBuilder('ucl');
           $query->select('ucl.id');
           $query->where('ucl.user = :user');
           $query->andWhere('ucl.cursus = :cursus');
           $query ->setParameter('user', $userId);
           $query ->setParameter('cursus', $cursusId);
           $query ->setMaxResults( '1' );

           
               
        return $query->getQuery()->getResult();

    }

    public function certificationbyuser($userId) {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT IDENTITY(ucl.cursus) as cursus, sum(ucl.isValidated) as certificated, c.nbLessons
            FROM App\Entity\UserCursusLesson ucl
            LEFT JOIN ucl.cursus c
            GROUP BY ucl.cursus,ucl.user,c.nbLessons
            HAVING ucl.user = :user AND certificated = c.nbLessons'
        )->setParameter('user', $userId);
        return $query->getResult();
    }


//    /**
//     * @return UserCursusLesson[] Returns an array of UserCursusLesson objects
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

//    public function findOneBySomeField($value): ?UserCursusLesson
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
