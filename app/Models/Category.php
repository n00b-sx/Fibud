<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'icon'];

    public function getIconOrDefaultAttribute(): string
    {
        if (!empty($this->icon)) {
            return $this->icon;
        }

        return $this->type === 'income' ? '💵' : '💸';
    }

    public function getOpenmojiUrlAttribute(): string
    {
        return \App\Helpers\OpenMojiHelper::getUrl($this->icon_or_default);
    }

    public function getOpenmojiImgAttribute(): string
    {
        return \App\Helpers\OpenMojiHelper::render($this->icon_or_default, 'size-6 inline-block');
    }

    public function getFormattedNameAttribute(): string
    {
        return $this->icon_or_default . ' ' . $this->name;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
