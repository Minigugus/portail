<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// Prod:
		$this->call([
			SemestersTableSeeder::class,
			AssosTypesTableSeeder::class,
			AssosTableSeeder::class,
			VisibilitiesTableSeeder::class,
			ContactsTypesTableSeeder::class,
			ContactsTableSeeder::class,
			PermissionsTableSeeder::class,
			RolesTableSeeder::class,
		]);

		if (config('app.debug', false)) {
			$this->call([
				UsersTableSeeder::class,
				GroupsTableSeeder::class,
				RoomsTableSeeder::class,
				ArticlesTableSeeder::class,
				PartnersTableSeeder::class,
			]);
		}
	}
}
