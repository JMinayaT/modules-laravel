<?php
namespace JMinayaT\Modules\Tests\Command;

use JMinayaT\Modules\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class moduleCreateCommandTest extends TestCase
{
    use DatabaseTransactions;
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_create_module()
    {
        $code = $this->artisan('module:create', ['module' => 'Blog']);
        $this->assertTrue(true);
    }
}