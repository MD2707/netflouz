<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class EpisodeTest extends TestCase
{
    private $user;

    public function testAddition()
    {
        $this->user = new User();
        $this->user->setName('usertest');
        $this->assertEquals('usertest', $this->user->getName());
    }
}
// commande pour installer PHPUnit :composer require --dev phpunit/phpunit
// commande pour lancer le test :./vendor/bin/phpunit
