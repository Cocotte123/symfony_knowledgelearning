<?php

namespace App\Tests;

use App\Entity\Cursus;
use App\Entity\Thema;
use App\Entity\Lesson;
use App\Entity\UserCursusLesson;
use PHPUnit\Framework\TestCase;

class CursusTest extends TestCase
{
    public function testCreateCursus(): void
    {
        $cursus = new Cursus();
        $thema = new Thema();

        $cursus   ->setName('cursus test')
                ->setLevel('level')
                ->setPrice(11.11)
                ->setThema($thema)
                ->setNbLessons(1);
        
        $this->assertTrue($cursus->getName() === 'cursus test');
        $this->assertTrue($cursus->getLevel() === 'level');
        //$this->assertTrue($cursus->getPrice() === '11.11');
        $this->assertTrue($cursus->getThema() === $thema);
        $this->assertTrue($cursus->getNbLessons() === 1);
    }


    public function testAddGetRemoveLesson(): void
    {
        $cursus = new Cursus();
        $lesson = new Lesson();

        $this->assertEmpty($cursus->getLessons());

        $cursus->addLesson($lesson);
        $this->assertContains($lesson,$cursus->getLessons());

        $cursus->removeLesson($lesson);
        $this->assertEmpty($cursus->getLessons());
    }

    public function testAddGetRemoveUserCursusLesson(): void
    {
        $cursus = new Cursus();
        $ucl = new UserCursusLesson();

        $this->assertEmpty($cursus->getUserCursusLessons());

        $cursus->addUserCursusLesson($ucl);
        $this->assertContains($ucl,$cursus->getUserCursusLessons());

        $cursus->removeUserCursusLesson($ucl);
        $this->assertEmpty($cursus->getUserCursusLessons());
    }
}
