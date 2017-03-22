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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => 'password',
        'remember_token' => str_random(10),
    ];

});

$factory->define(App\UserProfile::class, function (Faker\Generator $faker) {

	return [
	  'telephone' => $faker->phoneNumber,
		'billing_id' => function () {return factory(App\Address::class)->create()->id;},
		'delivery_id' => function () {return factory(App\Address::class)->create()->id;}
	];

});

$factory->define(App\Role::class, function (Faker\Generator $faker) {

	return [
		'name' => $faker->jobTitle
	];
});

$factory->define(App\Address::class, function (Faker\Generator $faker) {
	return [
		'street' => $faker->streetAddress,
		'city'=> $faker->city,
		'postcode' => $faker->postcode
	];
});



