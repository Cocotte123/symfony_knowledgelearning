<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\USerCursusLesson;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser(): void
    {
        $user = new User();

        $user   ->setEmail('testCreateUser@test.com')
                ->setPassword('$2y$13$uYwsRpLZvtRlqIdCisRIhusgG/YaiViaTza1DyGs1/guzDCrkaKA2')
                ->setLastName('Lastname')
                ->setFirstName('FirstName')
                ->setIsActivated(true)
                ->setRoles(['ROLE_TEST']);
        
        $this->assertTrue($user->getEmail() === 'testCreateUser@test.com');
        $this->assertTrue($user->getPassword() === '$2y$13$uYwsRpLZvtRlqIdCisRIhusgG/YaiViaTza1DyGs1/guzDCrkaKA2');
        $this->assertTrue($user->getLastName() === 'Lastname');
        $this->assertTrue($user->getFirstName() === 'FirstName');
        $this->assertTrue($user->isIsActivated() === true);
        $this->assertTrue($user->getRoles() === ['ROLE_TEST','ROLE_USER']);


    }

    public function testAddGetRemoveOrder(): void
    {
        $user = new User();
        $order = new Order();

        $this->assertEmpty($user->getOrders());

        $user->addOrder($order);
        $this->assertContains($order,$user->getOrders());

        $user->removeOrder($order);
        $this->assertEmpty($user->getOrders());
    }

    public function testAddGetRemoveUserCursusLesson(): void
    {
        $user = new User();
        $ucl = new UserCursusLesson();

        $this->assertEmpty($user->getUserCursusLessons());

        $user->addUserCursusLesson($ucl);
        $this->assertContains($ucl,$user->getUserCursusLessons());

        $user->removeUserCursusLesson($ucl);
        $this->assertEmpty($user->getUserCursusLessons());
    }

    
}
