<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Role;
use App\Models\Menu;



class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'Super Admin']);
        $editorRole = Role::create(['name' => 'Editor']);
        // Buat Menu
        $dashboard = Menu::create(['name' => 'Dashboard', 'route' => 'dashboard']);
        $users = Menu::create(['name' => 'User Management', 'route' => 'admins']);
        $settings = Menu::create(['name' => 'Settings', 'route' => 'settings']);

        // Assign Menu ke Role
        $adminRole->menus()->attach([$dashboard->id, $users->id]);
        $editorRole->menus()->attach([$dashboard->id]);

        // Buat Admin
        $admin = Admin::create([
            'name' => 'Admin Satu',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::create([
            'name' => 'customer satu',
            'full_name' => 'customer satu',
            'email' => 'customer@example.com',
            'status' => 'active',
            'phone_number' => '08123456789',
            'password' => bcrypt('password'),
        ]);

        $admin->roles()->attach($adminRole->id);

    }
}
