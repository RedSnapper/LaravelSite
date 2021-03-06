<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Address;
use App\Models\Role;
use App\Models\Segment;
use App\Models\Layout;
use App\Models\Team;
use App\Models\Category;
use App\Models\Activity;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {

	return [
	  'name'           => $faker->name,
	  'email'          => $faker->unique()->safeEmail,
	  'password'       => 'password',
	  'remember_token' => str_random(10),
	];
});

$factory->define(UserProfile::class, function (Faker\Generator $faker) {

	return [
	  'telephone'   => $faker->phoneNumber,
	  'billing_id'  => function () {
		  return factory(Address::class)->create()->id;
	  },
	  'delivery_id' => function () {
		  return factory(Address::class)->create()->id;
	  }
	];
});

$factory->define(Role::class, function (Faker\Generator $faker) {
	return [
	  'name'        => $faker->unique()->jobTitle,
	  'category_id' => function () {
		  return factory(Category::class)->create()->id;
	  }
	];
});

$factory->define(Team::class, function (Faker\Generator $faker) {

	return [
	  'name'        => explode('.', $faker->unique()->userName)[0],
	  'category_id' => function () {
		  return factory(Category::class)->create()->id;
	  }
	];
});

$factory->define(Activity::class, function (Faker\Generator $faker) {
	$name = $faker->unique()->firstNameFemale;
	return [
	  'name'        => strtoupper($name),
	  'label'       => $name,
	  'category_id' => function () {
		  return factory(Category::class)->create()->id;
	  }
	];
});

$factory->define(Segment::class, function (Faker\Generator $faker) {

	return [
	  'name'        => $faker->unique()->firstName,
	  'docs'        => $faker->catchPhrase,
	  'syntax'      => strtoupper($faker->fileExtension),
	  'category_id' => function () {
		  return factory(Category::class)->create()->id;
	  }
	];
});

$factory->define(Layout::class, function (Faker\Generator $faker) {
	return [
	  'name'        => $faker->unique()->lastName,
	  'category_id' => function () {
		  return factory(Category::class)->create()->id;
	  }
	];
});

$factory->define(Address::class, function (Faker\Generator $faker) {
	return [
	  'street'   => $faker->streetAddress,
	  'city'     => $faker->city,
	  'postcode' => $faker->postcode
	];
});

$factory->define(Category::class, function (Faker\Generator $faker) {
	return [
	  'name'    => $faker->unique()->firstNameFemale,
	  'parent'  => 1,
	  'section' => false
	];
});




