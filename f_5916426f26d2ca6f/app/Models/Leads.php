<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    use HasFactory;
    protected $fillable = ["name", "amocrm_id", "price", "is_deleted", "closed_at", "contact_name", "contact_phone", "contact_email"];
}
