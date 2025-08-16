<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SaasInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tallstarter:install-saas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will install the Saas functionality in your Laravel application.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ask to confirm before proceeding
        if (! $this->confirm('This will install the Saas functionality in your Laravel application. This should only be run in a fresh app. Are you sure you want to proceed?')) {
            return;
        }
//        $this->addEnvironmentKeys();
//        $this->addDependencies();
        $this->publishFiles();
    }

    public function addEnvironmentKeys(): void
    {
        /**
         * add the following lines to the .env file
         *
         * STRIPE_KEY=pk_test_
         * STRIPE_SECRET=sk_test_
         * STRIPE_WEBHOOK_SECRET=whsec_
         */

        $env = file_get_contents(base_path('.env'));
        $env .= "\nSTRIPE_KEY=\nSTRIPE_SECRET=\nSTRIPE_WEBHOOK_SECRET=\n";
        file_put_contents(base_path('.env'), $env);

        $env = file_get_contents(base_path('.env.example'));
        $env .= "\nSTRIPE_KEY=\nSTRIPE_SECRET=\nSTRIPE_WEBHOOK_SECRET=\n";
        file_put_contents(base_path('.env.example'), $env);

        $this->info('Environment keys added successfully. Please add the values to the .env file yourself.');

    }

    public function addDependencies(): void
    {

        // add "laravel/cashier" to the "require" section of the composer.json file
        $this->info('Adding Laravel Cashier to composer.json file...');
        shell_exec('composer require laravel/cashier');

        $this->info('Publishing Cashier migrations...');
        $this->call('vendor:publish', [
            '--provider' => 'Laravel\Cashier\CashierServiceProvider',
            '--tag' => 'cashier-migrations',
        ]);

        $this->info('Running migrations...');
        $this->call('migrate');

    }

    public function publishFiles(): void
    {
        $this->info('Publishing files...');

//        shell_exec('cp -r ' . base_path('stubs/saas') . '/* ' . base_path());

        $this->info('Files published successfully. Now:');

        $this->info('- add the values to STRIPE_KEY, STRIPE_SECRET, and STRIPE_WEBHOOK_SECRET in your .env file.');

        $this->info('- change the PlanSeeder file to add your own plans. Then seed it: php artisan db:seed --class=PlanSeeder');

        $this->info('- run "php artisan migrate" to run the migrations.');

    }
}
