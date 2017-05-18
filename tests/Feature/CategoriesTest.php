<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class CategoriesTest extends TestCase {

	/** @test */
	function unauthorized_users_may_not_create_categories() {

		$this->post('/api/category')
		  ->assertRedirect('/login');

		$this->signIn();

		$child = make(Category::class, ['parent' => 1]);

		$response = $this->json('POST', '/api/category', $child->toArray());

		$response
			->assertStatus(422);
	}

	/** @test */
	function an_authenticated_user_can_create_a_category() {

		$param = User::find(1);
		$user = $this->signIn($param);
		assertTrue($user->can('MEDIA_ACCESS'));

		$category = Category::section("MEDIA")->first()->descendants(false)->first();
		//Source

		//Needs a team also
		assertTrue($user->cannot('access',[$category]));

	}

}
