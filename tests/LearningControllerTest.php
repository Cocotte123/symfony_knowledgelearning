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
    
    /**
     * Test display list of themas  with check presence of thema's name
     */
    public function testRoute_app_learning(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/learning');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Voici nos thèmes');
        $this->assertSelectorTextContains('h5', 'test_thema');
    }

    /**
     * Test display logged user with check presence of name
     */
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
    
    /**
     * Test display list of cursuses with check presence of cursus's name
     */
    public function testRoute_app_learning_cursus(): void
    {
        $client = static::createClient();
        

        $crawler = $client->request('GET', '/learning/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Cursus d\'initiation test_cursus');
    }

    /**
     * Test display list of lessons for a cursus with check presence of a lesson
     */
    public function testRoute_app_learning_lesson(): void
    {
        $client = static::createClient();
        

        $crawler = $client->request('GET', '/learning/lesson/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h5', 'Cursus d\'initiation test_cursus');
        $this->assertSelectorExists('h5:contains("test_lesson1")');
    }

    
    
}
