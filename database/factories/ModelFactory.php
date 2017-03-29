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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => 'password',
        'remember_token' => str_random(10),
    ];

});

$factory->define(UserProfile::class, function (Faker\Generator $faker) {

	return [
	  'telephone' => $faker->phoneNumber,
		'billing_id' => function () {return factory(Address::class)->create()->id;},
		'delivery_id' => function () {return factory(Address::class)->create()->id;}
	];

});

$factory->define(Role::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->unique()->jobTitle
	];
});

$factory->define(Segment::class, function (Faker\Generator $faker) {
	return [
		'name' => $faker->unique()->firstName,
		'docs' => $faker->catchPhrase
	];
});

$factory->define(Layout::class, function (Faker\Generator $faker) {
		return [
			'name' => $faker->unique()->lastName
		];
});

$factory->define(Address::class, function (Faker\Generator $faker) {
	return [
		'street' => $faker->streetAddress,
		'city'=> $faker->city,
		'postcode' => $faker->postcode
	];
});



