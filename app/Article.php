<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'title',
        'body',
    ];

    public function user(): BelongsTo // BelongsToは戻り値の型
    {
        // belongsToメソッドは、関係するモデルとのリレーションを返す。
        // belongsToメソッドの場合は、BelongsToクラス
        return $this->belongsTo('App\User');

        // 1対1のときは、return $this->hasOne('App\User');
        // 1対多のときは、return $this->hasMany('App\User');
        // 多対多ときは、return $this->belongsToMany('App\User');
    }
}
