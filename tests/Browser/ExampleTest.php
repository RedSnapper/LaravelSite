<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends DuskTestCase
{

    public function testBasicExample()
    {
			$this->browse(function ($browser) {
				$browser->visit('/login')
					->type('email', 'ben@redsnapper.net')
					->type('password', 'password')
					->press('Login')
					->assertPathIs('/');
			});

    }
}
