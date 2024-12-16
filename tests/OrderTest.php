<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Orderdetail;
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

    public function testAddGetRemoveOrderDetail(): void
    {
        $order = new Order();
        $od = new Orderdetail();

        $this->assertEmpty($order->getOrderdetails());

        $order->addOrderdetail($od);
        $this->assertContains($od,$order->getOrderdetails());

        $order->removeOrderdetail($od);
        $this->assertEmpty($order->getOrderdetails());
    }
}
