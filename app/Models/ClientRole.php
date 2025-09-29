<?php

// namespace App\Models;

// use Spatie\Permission\Models\Role as SpatieRole;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Support\Facades\Auth;

// class ClientRole extends SpatieRole
// {
//     protected $fillable = [
//         'name', 
//         'guard_name', 
//         'client_id' // <--- Essential to allow saving the client_id
//     ];

 
//     protected static function booted()
//     {
//         static::addGlobalScope('client_restriction', function (Builder $builder) {
            
//             if (Auth::check() && property_exists(Auth::user(), 'client_id')) {

               
//                 $isSuperAdmin = method_exists(Auth::user(), 'is_super_admin') && Auth::user()->isSuperAdmin();
                
//                 if (!$isSuperAdmin) {
//                     $builder->where('client_id', Auth::user()->client_id);
//                 }
//             }
//         });
//     }


//     public static function withoutClientScope(): Builder
//     {
//         return static::withoutGlobalScope('client_restriction');
//     }
// }