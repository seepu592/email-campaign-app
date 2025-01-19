<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email','campaign_id'];

    public static function storeContactsFromCsv($data,$campaignId)
    {
        foreach ($data as $row) {
            self::create([
                'name' => $row['name'], 
                'email' => $row['email'], 
                'campaign_id' => $campaignId,
            ]);
        }
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

}
