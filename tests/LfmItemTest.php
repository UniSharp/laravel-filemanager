<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

class LfmItemTest extends TestCase
{
    public function tearDown()
    {
        m::close();

        parent::tearDown();
    }

    public function testTest()
    {
        $this->assertTrue(true);
    }
}
