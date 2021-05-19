<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content'];

    // この投稿を所有するユーザ。（ Userモデルとの関係を定義）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // あるユーザが追加したお気に入りの一覧を取得。（Userモデルとの関係を定義）
    public function favorite_users()
    {
        // お気に入りに追加された投稿を取得するため、クラス名はMicropostを指定
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
}
