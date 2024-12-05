<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Enter admin name: ');
        $email = $this->ask('Enter admin email address: ');
        $password = $this->secret('Enter admin password: ');

        if(User::where('email', $email)->exists()) {
            $this->error('The user with this email is already exists.');
            return 1;
        }

        // Allow to create only 1 admin user.
        // if( User::where('role', 'admin') -> exists() ){
        //     $this -> error('The admin user is already exists.') ;
        //     return 1 ;
        // }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin'
        ]);

        if($user) {
            $this->info('Successfully create admin user.');
        } 
        else {
            $this->error('Fail to create admin user.');
        }

        return 0;
    }
}
