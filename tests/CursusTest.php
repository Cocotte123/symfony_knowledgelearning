<?php

namespace App\Tests;

use App\Entity\Cursus;
use App\Entity\Thema;
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
}
