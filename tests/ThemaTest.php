<?php

namespace App\Tests;

use App\Entity\Thema;
use App\Entity\Cursus;
use PHPUnit\Framework\TestCase;

class ThemaTest extends TestCase
{
    public function testCreateThema(): void
    {
        $thema = new Thema();

        $thema  ->setName('thema test');
                
        
        $this->assertTrue($thema->getName() === 'thema test');
        
    }

    public function testAddGetRemoveCursus(): void
    {
        $thema = new Thema();
        $cursus = new Cursus();

        $this->assertEmpty($thema->getCursuses());

        $thema->addCursus($cursus);
        $this->assertContains($cursus,$thema->getCursuses());

        $thema->removeCursus($cursus);
        $this->assertEmpty($thema->getCursuses());
    }
}
