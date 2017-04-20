<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoriesTest extends TestCase {
	use DatabaseMigrations;

	/** @test */
	function guests_may_not_create_categories() {
		//$this->withExceptionHandling();
		$this->post('/api/category')
		  ->assertRedirect('/login');
	}

}
