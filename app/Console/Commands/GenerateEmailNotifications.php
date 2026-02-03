<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class GenerateEmailNotifications extends Command
{
    protected $signature = 'email:generate-notifications';

    protected $description = 'Generate email notifications based on user search history';

    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $this->info('Generating email notifications...');

        try {
            $count = $this->emailService->generateNotificationsForUsers();
            
            $this->info("Successfully generated {$count} email notifications.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error generating notifications: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
