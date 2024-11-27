<?php

namespace App\Tests;

use App\Entity\UserCursusLesson;
use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lesson;
use PHPUnit\Framework\TestCase;

class UserCursusLessonTest extends TestCase
{
    public function testCreateUserCursusLesson(): void
    {
        $userCursusLesson = new UserCursusLesson();
        $user = new User();
        $cursus = new Cursus();
        $learning = new Lesson();

        $userCursusLesson    ->setIsValidated(false)
                             ->setUser($user)
                             ->setCursus($cursus)
                             ->setLearning($learning);
                
        
        $this->assertTrue($userCursusLesson->isIsValidated() === false);
        $this->assertTrue($userCursusLesson->getUser() === $user);
        $this->assertTrue($userCursusLesson->getCursus() === $cursus);
        $this->assertTrue($userCursusLesson->getLearning() === $learning);
    }
}
