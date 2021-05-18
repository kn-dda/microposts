@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-sm-4">
            {{-- ユーザ情報 --}}
            @include('users.card')
        </aside>
        <div class="col-sm-8">
            {{-- タブ --}}
            @include('users.navtabs')
                @if (Auth::id() == $user->id)
                    {{-- 投稿フォーム --}}
                    @include('microposts.form')
            @endif
                {{-- 投稿一覧 --}}
                @include('microposts.microposts')
                {{-- お気に入り一覧 --}}
                @include('users.favorites')
                {{-- お気に入り登録／解除ボタン --}}
                @include('user_favorite.favorite_button')
        </div>
    </div>
@endsection