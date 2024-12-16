<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test create of a user with check response
     */
    public function testRoute_app_register(): void
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/register');

        $registerButton = $crawler->selectButton('registration_form[save]');
        $form = $crawler->selectButton('registration_form[save]')->form([
            'registration_form[lastName]' => 'Lapin',
            'registration_form[firstName]' => 'Coco',
            'registration_form[email]' => 'registration.test@test.com',
            'registration_form[plainPassword]' => 'registrationmdp'
        ]);
        
      
        $crawler = $client->submit($form);
        
        $crawler = $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        
    }

        
}
