<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Test login with check presence of user's name
     */
    public function testRoute_app_login(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $registerButton = $crawler->selectButton('Connexion');
        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'activated.user@test.com',
            'password' => 'password1234567'
        ]);
        
      
        $crawler = $client->submit($form);
        $crawler = $client->request('GET', '/learning');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bonjour Jean Martin');
    }

    /**
     * Test logout with check absence of learning's name
     */
    public function testRoute_app_logout(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(User::class);
        $loggedUser = $repository->findOneBy(['email' => 'activated.user@test.com']);;
        $client->loginUser($loggedUser);

        $crawler = $client->request('GET', '/logout');

        $crawler = $client->request('GET', '/learning');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('h1', 'Bonjour Jean Martin');
    }
}
