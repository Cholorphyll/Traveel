<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailNotification;
use App\Models\User;
use App\Models\UserEmailPreference;
use App\Mail\LocationRecommendationMail;
use App\Mail\HotelDealsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendScheduledEmails extends Command
{
    protected $signature = 'email:send-scheduled {--limit=50}';

    protected $description = 'Send scheduled email notifications to users';

    public function handle()
    {
        $limit = $this->option('limit');
        
        $this->info('Sending scheduled emails...');

        $notifications = EmailNotification::scheduled()
            ->limit($limit)
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No emails to send at this time.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        foreach ($notifications as $notification) {
            try {
                // Get user from FrontendUserLogin table
                $user = DB::table('FrontendUserLogin')->where('UserId', $notification->user_id)->first();
                
                if (!$user) {
                    $notification->markAsFailed('User not found');
                    $failed++;
                    continue;
                }

                // Check email preferences
                $preferences = DB::table('user_email_preferences')
                    ->where('user_id', $user->UserId)
                    ->first();
                
                $preferenceType = $notification->notification_type === 'hotel_deals' 
                    ? 'hotel_deals' 
                    : 'location_recommendations';
                
                // Check if user can receive this type of email
                if (!$preferences || !$preferences->$preferenceType) {
                    $notification->update(['status' => 'pending', 'scheduled_at' => now()->addDays(7)]);
                    continue;
                }

                $emailData = $notification->email_data;
                
                // Create a simple user object for the mailable
                $userObject = (object)[
                    'id' => $user->UserId,
                    'email' => $user->Email,
                    'name' => trim(($user->FirstName ?? '') . ' ' . ($user->LastName ?? '')),
                    'first_name' => $user->FirstName ?? '',
                    'last_name' => $user->LastName ?? '',
                ];
                
                if ($notification->notification_type === 'hotel_deals') {
                    Mail::to($notification->email)->send(
                        new HotelDealsMail(
                            $userObject,
                            $emailData['location'] ?? [],
                            $emailData['hotels'] ?? []
                        )
                    );
                } else {
                    Mail::to($notification->email)->send(
                        new LocationRecommendationMail(
                            $userObject,
                            $emailData['location'] ?? [],
                            $emailData['hotels'] ?? [],
                            $emailData['attractions'] ?? [],
                            $emailData['restaurants'] ?? []
                        )
                    );
                }

                $notification->markAsSent();
                
                // Update last email sent timestamp
                DB::table('user_email_preferences')
                    ->where('user_id', $user->UserId)
                    ->update(['last_email_sent_at' => now()]);
                
                $sent++;
                $this->info("Sent email to {$user->Email}");

            } catch (\Exception $e) {
                $notification->markAsFailed($e->getMessage());
                $failed++;
                $this->error("Failed to send email to {$notification->email}: " . $e->getMessage());
                Log::error('Email sending failed: ' . $e->getMessage(), [
                    'notification_id' => $notification->id,
                    'user_id' => $notification->user_id,
                ]);
            }
        }

        $this->info("Email sending complete. Sent: {$sent}, Failed: {$failed}");
        
        return Command::SUCCESS;
    }
}
