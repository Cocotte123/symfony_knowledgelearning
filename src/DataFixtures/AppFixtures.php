<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Thema;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Order;
use App\Entity\Orderdetail;
use App\Entity\UserCursusLesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    
    public function load(ObjectManager $manager): void
    {
        //new activated user
        $user1 = new User();

        $user1->setEmail('activated.user@test.com')
            ->setRoles(['ROLE_CLIENT'])
            //->setPassword
            ->setLastName('Martin')
            ->setFirstName('Jean')
            ->setIsActivated(true);

        $password = $this->userPasswordHasher->hashPassword($user1,'password1234567');
        $user1->setPassword($password);
        $manager->persist($user1);

        //new unactivated user
        $user2 = new User();

        $user2->setEmail('unactivated.user@test.com')
            ->setRoles([])
            //->setPassword
            ->setLastName('Blanc')
            ->setFirstName('Michel');
            

        $password = $this->userPasswordHasher->hashPassword($user2,'password1234567');
        $user2->setPassword($password);
        $manager->persist($user2);

        //new admin user
        $user3 = new User();

        $user3->setEmail('admin.user@test.com')
            ->setRoles(['ROLE_ADMIN'])
            //->setPassword
            ->setLastName('Noiret')
            ->setFirstName('Philippe')
            ->setIsActivated(true);

        $password = $this->userPasswordHasher->hashPassword($user3,'password1234567');
        $user3->setPassword($password);
        $manager->persist($user3);

        //new thema

        $thema = new Thema();
        $thema->setName('test_thema');
        $manager->persist($thema);

        //new cursus

        $cursus = new Cursus();
        $cursus->setName('test_cursus')
                ->setLevel('d\'initiation')
                ->setPrice(22.22)
                ->setThema($thema)
                ->setNbLessons(2);
        
        $manager->persist($cursus);

        //new lesson1
        $lesson1 = new Lesson();
        $lesson1->setName('test_lesson1')
                ->setNumber(1)
                ->setPrice(11.11)
                ->setVideo('https://www.youtube.com/embed/0qtoLnp_p7o?si=rou5N-x45Q2hm997')
                ->setText('texte de test')
                ->setCursus($cursus);
        $manager->persist($lesson1);

        //new lesson2
        $lesson2 = new Lesson();
        $lesson2->setName('test_lesson2')
                ->setNumber(2)
                ->setPrice(10.10)
                ->setVideo('https://www.youtube.com/embed/ynBinxdfra0?si=HZSQ4MtUa2gCHkKK')
                ->setText('texte de test2')
                ->setCursus($cursus);
        $manager->persist($lesson2);

        //new order
        $order = new Order();
        $order  ->setCartId('123456')
                ->setUser($user1);
        $manager->persist($order);

        //new orderdetail
        $orderDetail1 = new Orderdetail();
        $orderDetail1    ->setRepository('cursus')
                        ->setLearningId(1)
                        ->setPrice(22.22)
                        ->setOrdernumber($order);
        $manager->persist($orderDetail1);

        // new usercursuslesson
        $userCursusLesson1 = new UserCursusLesson();
        $userCursusLesson1    ->setIsValidated(true)
                             ->setUser($user1)
                             ->setCursus($cursus)
                             ->setLearning($lesson1);
        $manager->persist($userCursusLesson1);

        // new usercursuslesson
        $userCursusLesson2 = new UserCursusLesson();
        $userCursusLesson2    ->setIsValidated(true)
                             ->setUser($user1)
                             ->setCursus($cursus)
                             ->setLearning($lesson2);
        $manager->persist($userCursusLesson2);
        
        $manager->flush();
    }
}
