@extends('themes.bulma.layout')
@section('title', '- Song Detail')

@section('og-meta')
    @component('components.og-meta')
        @slot('ogTitle', $song['version'][$song['key']]['songName'] .' '. $song['version'][$song['key']]['artistName'])
        @slot('ogImageUrl', $song['version'][$song['key']]['coverUrl'])
        @slot('ogDescription')
Beatmaps: {{implode(', ',array_keys($song['version'][$song['key']]['beatmaps'])) }}
Description: {{$song['description']}}
        @endslot
        @slot('ogUrl', $song['version'][$song['key']]['linkUrl'])
    @endcomponent
@endsection

@section('content')
    @include('themes.bulma.pages.songs.partial-detail')
@endsection
