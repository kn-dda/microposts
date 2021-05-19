{{-- ユーザーが特定の投稿をお気に入り登録しているかどうかの確認 --}}
@if (Auth::user()->is_favoring($micropost->id))
    {{-- お気に入り解除ボタンのフォーム --}}
    {!! Form::open(['route' => ['favorites.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => "btn btn-danger btn-block"]) !!}
    {!! Form::close() !!}
@else
    {{-- お気に入り登録ボタンのフォーム --}}
    {!! Form::open(['route' => ['favorites.favorite', $micropost->id]]) !!}
        {!! Form::submit('Favorite', ['class' => "btn btn-primary btn-block"]) !!}
    {!! Form::close() !!}
@endif