<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\City;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'city_id',
        'district',
        'street',
        'building_number',
        'latitude',
        'longitude',
        'additional_info'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('district', 'like', "%{$search}%")
            ->orWhere('street', 'like', "%{$search}%")
            ->orWhere('building_number', 'like', "%{$search}%")
            ->orWhere('additional_info', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeFilterByDistrict($query, $district)
    {
        return $query->where('district', $district);
    }

    public function scopeSortBy($query, $field, $direction = 'asc')
    {
        return $query->orderBy($field, $direction);
    }
}
