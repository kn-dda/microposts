<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    //ユーザが特定の投稿内容をお気に入りに追加
    public function store($id)
    {
        // 認証済みユーザ（閲覧者）が、 特定の投稿内容をお気に入り追加する
        \Auth::user()->favorite($id);
        // 前のURLへリダイレクトさせる
        return back();
    }
    //ユーザがお気に入りを削除
    public function destroy($id)
    {
        // 認証済みユーザ（閲覧者）が、 特定の投稿内容をお気に入り解除する
        \Auth::user()->unfavorite($id);
        // 前のURLへリダイレクトさせる
        return back();
    }
}
