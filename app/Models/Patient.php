<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['user_id', 'patientID', 'has_insurance', 'city'];

    protected $searchableFields = ['*'];

    protected $casts = [
        'has_insurance' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
