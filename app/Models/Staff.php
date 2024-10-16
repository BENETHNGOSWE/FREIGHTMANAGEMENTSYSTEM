<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'staff_no',
        'staff_firstname',
        'staff_middlename',
        'staff_lastname',
        'staff_address',
        'staff_photo',
        'staff_email',
        'phone',
        'staff_document',
        'staff_bio',
        'staff_gender',
        'client_id',
        'staff_type_id',
        'user_id',
        'branch_id',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'staffs';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function staffType()
    {
        return $this->belongsTo(StaffType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
