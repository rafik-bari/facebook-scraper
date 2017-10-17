<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{


    protected $fillable = ['id', 'about', 'app_links', 'can_checkin', 'category',
        'checkins', 'company_overview', 'cover', 'description', 'description_html',
        'emails', 'engagement', 'fan_count', 'featured_video', 'founded', 'global_brand_page_name',
        'global_brand_root_id', 'has_whatsapp_number', 'impressum', 'is_owned', 'is_published', 'is_unclaimed',
        'link', 'location', 'mission', 'name', 'parking', 'pharma_safety_info', 'phone', 'products',
        'rating_count', 'schedule', 'single_line_address', 'start_info', 'store_number', 'talking_about_count',
        'username','verification_status', 'voip_info', 'website', 'were_here_count', 'whatsapp_number'];
}
