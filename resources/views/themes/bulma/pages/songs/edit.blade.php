@extends('themes.bulma.layout')
@section('title', '- Song Edit')

@section('content')
    @component('themes.bulma.components.song-edit',['uploaderId' => $song['uploaderId']])
        @slot('id', $song['id'])
        @slot('key', $song['key'])
        @slot('name', $song['name'])
        @slot('description', $song['description'])
        @slot('uploader', $song['uploader'])
        @slot('authorName', $song['version'][$song['key']]['authorName'])
        @slot('songName', $song['version'][$song['key']]['songName'])
        @slot('artistName', $song['version'][$song['key']]['artistName'])
        @slot('beatmaps')
            @foreach($song['version'][$song['key']]['beatmaps'] as $beatmap => $data)
                {{ $beatmap }}@if(!$loop->last), @endif
            @endforeach
        @endslot
        @slot('downloadCount', $song['version'][$song['key']]['downloadCount'])
        @slot('playedCount', $song['version'][$song['key']]['playedCount'])
        @slot('upVotes', $song['version'][$song['key']]['upVotes'])
        @slot('downVotes', $song['version'][$song['key']]['downVotes'])
        @slot('version', $song['key'])
        @slot('createdAt', $song['version'][$song['key']]['createdAt'])
        @slot('linkUrl', $song['version'][$song['key']]['linkUrl'])
        @slot('downloadUrl', $song['version'][$song['key']]['downloadUrl'])
        @slot('coverUrl', $song['version'][$song['key']]['coverUrl'])
    @endcomponent
@endsection
