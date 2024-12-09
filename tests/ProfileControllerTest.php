<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Cursus;
use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{
    public function testRoute_app_profile(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        
        
    }

    public function testRoute_app_profile_update(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('POST', '/profile/update');

        //$userModifyButton = $crawler->selectButton('user_modify_form[update]');
        //$form = $crawler->selectButton('user_modify_form[update]')->form([
        //    'user_modify_form[lastName]' => 'Lapin',
        //    'user_modify_form[firstName]' => 'Coco'
        //]);
        $crawler = $client->submitForm('user_modify_form[update]', [
            'user_modify_form[lastName]' => 'Lapin',
            'user_modify_form[firstName]' => 'Coco'
        ]);
        
        
        //$crawler = $client->submit($form);
        $crawler = $client->request('GET', '/profile');

        $this->assertResponseIsSuccessful();
        
    }

    public function testRoute_app_profile_orders(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/profile/orders');

        

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('th:contains("1")');
        
    }

    public function testRoute_app_profile_orderdetails(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/profile/orderdetails/1');

        

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("Détails de la commande n° 1")');
        $this->assertSelectorExists('th:contains("test_cursus")');
    }

    public function testRoute_app_profile_learnings(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', 'profile/learnings');

        

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('th:contains("test_cursus")');
        $this->assertSelectorExists('th:contains("test_lesson1")');
    }

    public function testRoute_app_profile_certifications(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/profile/certifications');

        

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h5:contains("test_cursus")');
        $this->assertSelectorExists('h2:contains("Mes certifications")');
    }
}