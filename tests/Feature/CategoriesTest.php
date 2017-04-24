<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoriesTest extends TestCase {
	use DatabaseTransactions;

	/** @test */
	function unauthorized_users_may_not_create_categories() {

		$this->post('/api/category')
		  ->assertRedirect('/login');

		$root = create(Category::class);

		$this->signIn();

		$child = make(Category::class, ['parent' => $root->id]);

		$response = $this->json('POST', '/api/category', $child->toArray());

		$response
			->assertStatus(422);
	}

	/** @test */
	function an_authenticated_user_can_create_a_category() {

		$root = create(Category::class);

		$user = $this->signIn();

		$user->roles()->first()->givePermissionToCategory($root);
		
		$child = make(Category::class, ['parent' => $root->id]);

		$response = $this->json('POST', '/api/category', $child->toArray());

		$response
		  ->assertStatus(201)
		  ->assertJsonFragment(['name' => $child->name]);
	}

}
