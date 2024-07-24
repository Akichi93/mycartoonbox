<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partenaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_partenaire',
        'logo'
    ];


      // Cast credential JSON to array
      protected $casts = [
        'credential' => 'array',
    ];
    public function service() {
        return $this->hasMany(Service::class);
    }

}
