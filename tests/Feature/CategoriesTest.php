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
	function guests_may_not_create_categories() {

		$this->post('/api/category')
		  ->assertRedirect('/login');
	}

	/** @test */
	function an_authenticated_user_can_create_a_category() {

		$root = create(Category::class);

		$user = create(User::class);

		$role = create(Role::class);
		$role->givePermissionToCategory($root);

		$user->roles()->save($role);

		$this->signIn($user);

		$child = make(Category::class,['parent'=>$root->id]);

		$response = $this->json('POST', '/api/category', $child->toArray());

		$response
		  ->assertStatus(201);

		//$this->get($response->headers->get('Location'))
		//  ->assertSee($child->name)
		//  ->assertSee($thread->body);
	}

}
