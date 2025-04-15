<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CreateSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a super admin user (optional)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // ask if to proceed with creating a super admin
        if (!$this->confirm('Do you want to create a super admin user?', true)) {
            $this->info('Super admin creation skipped.');
            return CommandAlias::SUCCESS;
        }

        $this->line('Create the first super admin user for your new application');
        // ask for the user's name
        $name = $this->ask('What is the users name?');

        // ask for the user's email
        $email = $this->ask('What is the users email?');

        // ask for the user's password
        $password = $this->secret('What is the users password?');

        // ask if the user is a super admin
        $isSuperAdmin = $this->confirm('Is this user a super admin?');

        // create the user
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'locale' => 'en',
        ]);

        // assign the role
        if ($isSuperAdmin) {
            $user->assignRole('Super Admin');
        }

        // output the user
        $this->info('User created successfully. Now go and build something amazing!');

        return CommandAlias::SUCCESS;
    }
}
