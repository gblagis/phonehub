<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = ['listing_id','path','is_primary','ordering'];

    protected $casts = ['is_primary'=>'boolean'];

    public function listing() 
    { 
        return $this->belongsTo(Listing::class); 
    }
}
