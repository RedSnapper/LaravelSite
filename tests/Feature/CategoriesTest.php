<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Team;
use Tests\TestCase;

class CategoriesTest extends TestCase {

	protected function getFirstMediaCategory() {
		return Category::section("MEDIA")->first()->descendants(false)->first(); //'Source'
	}

	/** @test Check category seeding was good */
	function category_seeding_was_good() {
		$source =  $this->getFirstMediaCategory();
		$this->assertEquals('Source',$source->name,"Seeding (Categories) appears not to have been loaded. 
		Category 'Source' expected for first descendant of MEDIA");
	}

	/** @test */
	function unauthorized_users_may_not_create_categories() {
		$this->post('/api/category')->assertRedirect('/login');
		$child = make(Category::class, ['parent' => 1]);
		$response = $this->json('POST', '/api/category', $child->toArray());
		$response->assertStatus(401);
	}

	/** @test For team-contextualised access within the scope of media, a team is necessary for access */
	function authenticated_user_needs_team_context() {
		$user = $this->signIn();
		$this->assertTrue($user->cannot('MEDIA_ACCESS'));
	}

	/** @test category access/modification itself is controlled outside of a team context */
	function authenticated_user_can_access_category_instance() {
		$user = $this->signIn();
		$source = $this->getFirstMediaCategory();
		$this->assertTrue($user->can('ACCESS',[$source]));
		$this->assertTrue($user->can('MODIFY',[$source]));
	}

	/** @test Media-Category access with a team context is acceptable. */
	function authenticated_user_can_access_media_category_with_team() {
		$user = $this->signIn();
		$source = $this->getFirstMediaCategory();
		$team = Team::first(); //This is 'Otsuka', by default Param has media access to this.
		$this->assertTrue($user->can('MEDIA_ACCESS',[$team])); //general access according to team 'Otsuka'.
		$this->assertTrue($user->can('MEDIA_MODIFY',[$team]));
		$this->assertTrue($user->can('MEDIA_ACCESS',[$team,$source])); //access to media in the 'source' category for team 'Otsuka'.
		$this->assertTrue($user->can('MEDIA_MODIFY',[$team,$source])); //modify media in the 'source' category for team 'Otsuka'.
	}

	/** @test creating/deleting media categories by an authenticated user. */
	function an_authenticated_user_can_create_and_delete_a_category() {
		$this->signIn();
		$source = $this->getFirstMediaCategory(); //gate is tested above.
		$category = new Category(['name'=>'TestCategory','parent'=>$source]);
		$category->save();
		$created = Category::reference("TestCategory","MEDIA")->first();
		$createdAndFound = $created ? $created->name == "TestCategory" : false;
		$this->assertTrue($createdAndFound,"Category creation failed.");
		$category->delete();
		$deleted = Category::reference("TestCategory","MEDIA")->first();
		$this->assertNull($deleted,"Category appears not to have been deleted.");
	}

}
