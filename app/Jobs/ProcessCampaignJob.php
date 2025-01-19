<?php 

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CampaignProcessedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProcessCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $contacts;

    /**
     * Create a new job instance.
     *
     * @param Campaign $campaign
     * @param Collection $contacts
     */
    public function __construct(Campaign $campaign, $contacts)
    {
        $this->campaign = $campaign;
        $this->contacts = $contacts;  // Pass contacts directly
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $total = $this->contacts->count();
        $processed = 0;

        // Debug: Log campaign and its owner details
        //Log::info('Campaign details:', ['campaign' => $this->campaign->toArray()]);
        
        $owner = $this->campaign->owner;  // Assuming the campaign has an `owner` relationship

        // Debug: Log owner information
        if ($owner) {
            //Log::info('Owner details:', ['owner' => $owner->toArray()]);
            //Log::info('Owner email:', ['email' => $owner->email]);
        } else {
            Log::error('No owner found for campaign with ID: ' . $this->campaign->id);
        }

        // Process each contact
        foreach ($this->contacts as $contact) {
            try {
                // Send the email
                Mail::send('emails.campaign', ['username' => $contact['name']], function ($message) use ($contact) {
                    $message->to($contact['email'])
                            ->subject('Campaign Email');
                });
                $this->campaign->increment('processed_contacts');

            } catch (\Exception $e) {
                // Debug: Log error in email sending
                Log::error('Failed to send email to ' . $contact['email'] . ': ' . $e->getMessage());
            }
        }

        // Debug: Check if the campaign has an owner before sending notification
        if ($owner) {
            //Log::info('Notifying owner:', ['owner_email' => $owner->email]);
            // Notify the campaign owner
            Notification::send($owner, new CampaignProcessedNotification($this->campaign));
        } else {
            Log::error('No owner found to notify for campaign with ID: ' . $this->campaign->id);
        }
        $this->updateCampaignProgress();

    }

    private function updateCampaignProgress()
    {
        // Update the campaign's processed contacts count
        $processedContacts = $this->campaign->processed_contacts;

        // Get the total contacts for the campaign
        $totalContacts = $this->campaign->total_contacts;

        // Check if all contacts are processed
        if ($processedContacts == $totalContacts) {
            // All contacts are processed, so update the campaign status
            $this->campaign->status = 'processed';
            $this->campaign->save();
        }
    }
}
