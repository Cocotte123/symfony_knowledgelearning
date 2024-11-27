<?php

namespace App\Tests;

use App\Entity\Thema;
use PHPUnit\Framework\TestCase;

class ThemaTest extends TestCase
{
    public function testCreateThema(): void
    {
        $thema = new Thema();

        $thema  ->setName('thema test');
                
        
        $this->assertTrue($thema->getName() === 'thema test');
        
    }
}
