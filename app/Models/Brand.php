<?php

namespace App\Models;

use App\Enum\status;
use App\Helpers\ImageHelper;
use App\Helpers\KeywordHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brand';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'status',
        'icon',
        'image',
    ];

    protected $casts = [
        KeywordHelper::STATUS => KeywordHelper::BOOLEAN,
    ];

    public function scopeActive($query){
        return $query->where(KeywordHelper::STATUS,status::ACTIVE);
    }

    public function scopeInactive($query){
        return $query->where(KeywordHelper::STATUS,status::INACTIVE);
    }

    public function getImageUrlAttribute(){
        return $this->image ? asset($this->image) : null;
    }

    public function getIconUrlAttribute(){
        return $this->icon ? asset($this->icon) : null;
    }
}
