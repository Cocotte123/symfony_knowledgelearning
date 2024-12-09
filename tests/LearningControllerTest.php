<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Cursus;
use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LearningControllerTest extends WebTestCase
{
    
    public function testRoute_app_learning(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/learning');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Voici nos thèmes');
        $this->assertSelectorTextContains('h5', 'test_thema');
    }

    public function testShouldDisplayLoggedUser(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);
               
        $client->loginUser($loggedUser);
   
        $crawler = $client->request('GET', '/learning');
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bonjour Jean Martin');
        
    }
    
    public function testRoute_app_learning_cursus(): void
    {
        $client = static::createClient();
        

        $crawler = $client->request('GET', '/learning/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Cursus d\'initiation test_cursus');
    }

    public function testRoute_app_learning_lesson(): void
    {
        $client = static::createClient();
        

        $crawler = $client->request('GET', '/learning/lesson/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Cursus d\'initiation test_cursus');
        $this->assertSelectorExists('h5:contains("test_lesson1")');
    }

    
    
}
