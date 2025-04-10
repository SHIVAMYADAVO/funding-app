<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Category extends Model
{
    protected $fillable = ['name', 'description', 'image'];
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($category) {
            $category->products()->delete();
        });
    }
    public function products()
{
    return $this->hasMany(Product::class, 'category_id');
}

}
