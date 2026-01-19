<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Design extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
    ];

    public $translatable = ['name', 'description'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(DesignImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(DesignImage::class)->where('is_primary', true);
    }

    public function measurements()
    {
        return $this->belongsToMany(Measurement::class)
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function designOptions()
    {
        return $this->belongsToMany(DesignOption::class, 'design_design_option')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name->en', 'like', "%{$search}%")
              ->orWhere('name->ar', 'like', "%{$search}%")
              ->orWhere('description->en', 'like', "%{$search}%")
              ->orWhere('description->ar', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {
                  $userQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    public function scopeFilterByPrice($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    public function scopeFilterByMeasurement($query, $measurementId)
    {
        return $query->whereHas('measurements', function ($q) use ($measurementId) {
            $q->where('measurements.id', $measurementId);
        });
    }

    public function scopeFilterByDesignOption($query, $designOptionId)
    {
        return $query->whereHas('designOptions', function ($q) use ($designOptionId) {
            $q->where('design_options.id', $designOptionId);
        });
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['user:id,name', 'images', 'measurements', 'designOptions']);
    }

    // في app/Models/Design.php - ضيف هالعلاقة:

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'design_order')
            ->withPivot(['quantity', 'price', 'subtotal'])
            ->withTimestamps();
    }
}
