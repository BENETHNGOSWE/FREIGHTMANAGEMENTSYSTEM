<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffType extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['staff_type_name', 'staff_type_description','is_system','is_admin'];

    protected $searchableFields = ['*'];

    protected $table = 'staff_types';

    public function allStaff()
    {
        return $this->hasMany(Staff::class);
    }

    public function kodapackage()
    {
        return $this->belongsTo(Kodapackage::class);
    }
}
