<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessCampaignJob;

class CampaignController extends Controller
{
    public function create()
    {
        return Inertia::render('Campaign/CreateCampaign');
    }

    // Store campaign and process CSV file
    public function store(Request $request)
    {
        // Validate file input
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',  // Ensure CSV file
        ]);

        $campaign = Campaign::create([
            'name' => $request->input('name'),
            'user_id' => auth()->id(), // Assuming user is authenticated
            'status' => 'processing',
        ]);
        // Check if the file is valid
        $file = $request->file('csv_file');

        if (!$file->isValid()) {
            return redirect()->back()->withErrors(['csv_file' => 'Invalid file uploaded.']);
        }

        // Parse CSV file using helper method
        $result = $this->parseCsv($file);

        // If there are validation errors, redirect back with errors
        if (!empty($result['errors'])) {
            return redirect()->back()->withErrors($result['errors'])->withInput();
        }

        // Store valid contacts in the database
        // if (!empty($result['valid'])) {
        //     Contact::storeContactsFromCsv($result['valid'], $campaign->id);
        // }

        if (!empty($result['valid'])) {
            $this->storeContacts($result['valid'], $campaign);
        }

        //ProcessCampaignJob::dispatch($campaign, $contacts);
        if (!empty($result['valid'])) {
            collect($result['valid'])
                ->unique('email') 
                ->chunk(10)
                ->each(function ($chunk) use ($campaign) {
                    //dd($chunk);
                    dispatch(new ProcessCampaignJob($campaign, $chunk))->delay(now()->addSeconds(1));
                });
        }
        
        // Notify user about the campaign processing
        session()->flash('success', 'Campaign has been initiated and is processing.');
        return redirect()->route('campaign-progress', ['campaign' => $campaign->id]);
    }

    // Helper method to parse CSV file
    private function parseCsv($file)
    {
        $data = [];
        $errors = [];
        $rowNumber = 1;

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            $isHeader = true;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                // Skip header row
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                // Validate the data in each row
                if (empty($row[0]) || empty($row[1])) {
                    $errors[] = "Row {$rowNumber}: Name and email are required.";
                    continue;
                }

                if (!filter_var($row[1], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format for '{$row[1]}'";
                    continue;
                }

                // Add valid data to the array
                $data[] = ['name' => $row[0], 'email' => $row[1]];
            }
            fclose($handle);
        } else {
            $errors[] = 'Unable to open the file.';
        }

        return [
            'valid' => $data,
            'errors' => $errors,
        ];
    }


    private function storeContacts(array $contacts, Campaign $campaign)
    {
        // Insert contacts in bulk
        Contact::insert(array_map(function ($contact) use ($campaign) {
            return [
                'name' => $contact['name'],
                'email' => $contact['email'],
                'campaign_id' => $campaign->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $contacts));

        // Update total_contacts after adding new contacts
        $campaign->update(['total_contacts' => $campaign->contacts()->count()]);
    }

    public function overview()
    {
        $campaigns = Campaign::where('user_id', auth()->id())->get();

        return Inertia::render('Campaign/Overview', [
            'campaigns' => $campaigns,
        ]);
    }
    // Show campaign progress
    public function progressUpdate(Campaign $campaign)
    {
        return Inertia::render('Campaign/Progress', [
            'campaign' => $campaign,
            'processedContacts' => $campaign->processed_contacts,
            'remainingContacts' => $campaign->total_contacts - $campaign->processed_contacts,
        ]);
    }
    
   }
