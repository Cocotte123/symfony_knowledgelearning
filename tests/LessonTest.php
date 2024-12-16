<?php

namespace App\Tests;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\UserCursusLesson;
use PHPUnit\Framework\TestCase;

class LessonTest extends TestCase
{
    public function testCreateLesson(): void
    {
        $cursus = new Cursus();
        $lesson = new Lesson();

        $lesson ->setName('lesson test')
                ->setNumber(1)
                ->setPrice(11.11)
                ->setCursus($cursus)
                ->setVideo('video')
                ->setText('text');
        
        $this->assertTrue($lesson->getName() === 'lesson test');
        $this->assertTrue($lesson->getNumber() === 1);
        $this->assertTrue($lesson->getPrice() === 11.11);
        $this->assertTrue($lesson->getCursus() === $cursus);
        $this->assertTrue($lesson->getVideo() === 'video');
        $this->assertTrue($lesson->getText() === 'text');
    }

    public function testAddGetRemoveUserCursusLesson(): void
    {
        $lesson = new Lesson();
        $ucl = new UserCursusLesson();

        $this->assertEmpty($lesson->getUserCursusLessons());

        $lesson->addUserCursusLesson($ucl);
        $this->assertContains($ucl,$lesson->getUserCursusLessons());

        $lesson->removeUserCursusLesson($ucl);
        $this->assertEmpty($lesson->getUserCursusLessons());
    }
}
