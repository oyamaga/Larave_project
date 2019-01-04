<?php

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Database\AdminTablesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTablesSeeder::class);
        factory(App\Admin\Models\TestUser::class, 5)->create();
        // $this->call(UsersTableSeeder::class);
    }
}
