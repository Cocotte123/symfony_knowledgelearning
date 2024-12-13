<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BoughtLessonControllerTest extends WebTestCase
{
    /**
     * Test display lesson's page with check presence of the title
     */
    public function testRoute_app_bought_lesson(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/bought/lesson/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1:contains("test_lesson1")');
        
        
    }

    /**
     * Test display cursus's page with check presence of the cursus and lessons
     */
    public function testRoute_app_bought_cursus(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/bought/cursus/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1:contains("Cursus d\'initiation test_cursus")');
        
        $this->assertSelectorExists('p:contains("Leçon n°1 sur 2")');
        
    }

    /**
     * Test validation of a lesson with check presence of confirmation's text
     */
    public function testRoute_app_bought_lesson_validate(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);

        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
               
        $client->loginUser($loggedUser);
        $crawler = $client->request('GET', '/bought/lesson/validate/1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h2:contains("La leçon "test_lesson1" est validée!!!!!")');
        
        
        
    }
}
