<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testCreateOrder(): void
    {
        $user = new User();
        $order = new Order();

        $order  ->setCartId('123456')
                ->setUser($user);
                
        
        $this->assertTrue($order->getCartId() === '123456');
        $this->assertTrue($order->getUser() === $user);
        
    }
}
