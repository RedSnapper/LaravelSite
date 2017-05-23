<?php
/**
 * Part of site
 * User: ben ©2017 Red Snapper Ltd.
 * Date: 23/05/2017 07:30
 */

namespace Tests\Feature;

use Tests\TestCase;

class EnvironmentTest extends TestCase {
	/** @test Check User seeding was good */
	function user_seeding_was_good() {
		$user = $this->signIn();
		$this->assertEquals('Param',$user->name,"Seeding appears not to have been loaded. Username 'Param' expected for user 1");
	}

}