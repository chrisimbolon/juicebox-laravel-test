<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmailJob;
use App\Models\User;
use Illuminate\Console\Command;

class SendWelcomeEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:welcome {userId : The ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually dispatch welcome email job for a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        
        // Find the user
        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ User with ID {$userId} not found!");
            return Command::FAILURE;
        }

        // Dispatch the welcome email job
        SendWelcomeEmailJob::dispatch($user);

        $this->info("✅ Welcome email job dispatched for user: {$user->name} ({$user->email})");
        $this->info("📧 Check your queue worker to see the job processed!");
        
        return Command::SUCCESS;
    }
}