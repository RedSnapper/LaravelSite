<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Team;
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

	/** @test All these access tests need putting into their own functions. */
	function an_authenticated_user_can_create_a_category() {

		$param = User::find(1);
		$user = $this->signIn($param);
		//not without a team.
		$this->assertTrue($user->cannot('MEDIA_ACCESS'));

		$category = Category::section("MEDIA")->first()->descendants(false)->first();

		//the user can access the category, but not .
		$this->assertTrue($user->can('ACCESS',[$category]));
		//in the scope of media..
		$this->assertTrue($user->cannot('MEDIA_ACCESS',[$category]));

		$team = Team::first(); //This is Otsuka, by default Param has media access to this.

		//These are 'magic' activities.
		$this->assertTrue($user->can('MEDIA_ACCESS',[$team]));
		$this->assertTrue($user->can('MEDIA_ACCESS',[$team,$category]));
		$this->assertTrue($user->can('MEDIA_MODIFY',[$team]));
		$this->assertTrue($user->can('MEDIA_MODIFY',[$team,$category]));

	}

}
