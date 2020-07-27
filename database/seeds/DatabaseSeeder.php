<?php

use App\Organization;
use Illuminate\Database\Seeder;
use Faker\Factory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $count = Organization::count();
        $organization = new Organization;
        $organization->title = ++$count.' - '.Factory::create()->company;
        $organization->domain = Factory::create()->domainName;
        $organization->save();
        factory(App\User::class, 3)->create();
    }
}
