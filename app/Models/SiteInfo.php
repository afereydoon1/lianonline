<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteInfo extends Model
{
    protected $table = 'site_info';

    protected $casts = [
        'licenses' => 'array',
    ];

    protected $fillable = [
        'site_name',
        'site_description',
        'logo_url',
        'favicon_url',
        'licenses',
        'gateway',
        'about_us',
        'contact_us',
        'site_percent',
        'terms_and_conditions',
    ];

    protected $appends = [
        'slides'
    ];
    public function getSlidesAttribute() {
        return SliderImage::get();
    }
}
