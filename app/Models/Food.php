<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Support\Facades\Storage;

class Food extends Model
{
  use HasFactory, SoftDeletes;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'description', 'ingredients', 'price', 'rate', 'types', 'picturePath'
  ];

  public function getCreatedAtAttribute($val)
  {
    return Carbon::parse($val)->timestamp;
  }

  public function getUpdatedAtAttribute($val)
  {
    return Carbon::parse($val)->timestamp;
  }

  public function toArray()
  {
    $toArray = parent::toArray();
    $toArray['picturePath'] = $this->picturePath;
    return $toArray;
  }

  public function getPicturePathAttribute()
  {
    return url() . Storage::url($this->attributes['picturePath']);
  }
}
