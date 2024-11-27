<?php

namespace App\Tests;

use App\Entity\Orderdetail;
use App\Entity\Order;
use PHPUnit\Framework\TestCase;

class OrderdetailTest extends TestCase
{
    public function testCreateOrderdetail(): void
    {
        $orderDetail = new Orderdetail();
        $order = new Order();

        $orderDetail    ->setRepository('test repository')
                        ->setLearningId(1)
                        ->setPrice(11.11)
                        ->setOrdernumber($order);
                
        
        $this->assertTrue($orderDetail->getRepository() === 'test repository');
        $this->assertTrue($orderDetail->getLearningId() === 1);
        $this->assertTrue($orderDetail->getPrice() === 11.11);
        $this->assertTrue($orderDetail->getOrdernumber() === $order);
    }
}
