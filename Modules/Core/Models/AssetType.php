<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'asset_type_id');
    }

    public static function slugFromName(string $name): string
    {
        return \Illuminate\Support\Str::slug($name);
    }
}
