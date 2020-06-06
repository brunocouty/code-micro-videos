<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Genre
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Genre onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Genre query()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Genre withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Genre withoutTrashed()
 * @mixin \Eloquent
 */
class Genre extends Model
{
    use SoftDeletes, Uuid;

    protected $fillable = [
        'name',
        'is_active'
    ];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];
    protected $dates = [
        'deleted_at'
    ];

    public $incrementing = false;
}
