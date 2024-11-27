<?php

namespace App\Tests;

use App\Entity\User;
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
                ->setIsActivated(true);
        
        $this->assertTrue($user->getEmail() === 'testCreateUser@test.com');
        $this->assertTrue($user->getPassword() === '$2y$13$uYwsRpLZvtRlqIdCisRIhusgG/YaiViaTza1DyGs1/guzDCrkaKA2');
        $this->assertTrue($user->getLastName() === 'Lastname');
        $this->assertTrue($user->getFirstName() === 'FirstName');
        $this->assertTrue($user->isIsActivated() === true);


    }
}
