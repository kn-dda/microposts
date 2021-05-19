<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    //このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    //このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    /**
     * $userIdで指定されたユーザをフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }

    /**
     * $userIdで指定されたユーザをアンフォローする。
     *
     * @param  int  $userId
     * @return bool
     */
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }

    /**
     * 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
     *
     * @param  int  $userId
     * @return bool
     */
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    //このユーザとフォロー中ユーザの投稿に絞り込む。
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    //このユーザに関係するモデルの件数をロードする。
    public function loadRelationshipCounts()
    {
        // タイムライン、フォロー、フォロワー、お気に入りの件数を取得
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    // ユーザーが$postIdで指定された投稿内容をお気に入りに追加する
    public function favorite($postId)
    {
        // すでにお気に入りに追加しているかの確認
        $exist = $this->is_favoring($postId);
        // 対象が自分の投稿かどうかの確認
        $its_me = $this->id == $postId;

        if ($exist || $its_me) {
            // すでにお気に入り登録していれば何もしない
            return false;
        } else {
            // 未登録であればお気に入りする
            $this->favorites()->attach($postId);
            return true;
        }
    }

    // ユーザが$postIdで指定された投稿内容をお気に入りから削除する
    public function unfavorite($postId)
    {
        // すでにお気に入り登録しているかの確認
        $exist = $this->is_favoring($postId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $postId;

        if ($exist && !$its_me) {
            // すでにお気に入り登録していれば登録を外す
            $this->favorites()->detach($postId);
            return true;
        } else {
            // 未登録であれば何もしない
            return false;
        }
    }
    
    // 指定された $postIdの投稿内容をこのユーザがお気に入りに追加済みか調べる。追加済みならtrueを返す。
    public function is_favoring($postId)
    {
        // お気に入り登録済みの投稿の中 に$postIdのものが存在するか
        return $this->favorites()->where('micropost_id', $postId)->exists();
    }
    
    // ユーザが追加したお気に入り一覧を取得する。
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
}
