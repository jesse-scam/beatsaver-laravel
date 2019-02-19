@extends('admin.layout.app')

@section('content')
    <section class="content-header">
        <h1>
            {{ $song->name }}
            <small>Song</small>
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Song info</h3>

                    </div>
                    <div class="box-body">
                        <form method="post" action="{{ route('admin.songs.update', ['song' => $song]) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group {{ !$errors->has('name') ?: 'has-error' }}">
                                @foreach($errors->get('name') as $message)
                                    <label class="control-label" for="name">{{ $message }}</label><br>
                                @endforeach
                                <label for="name">Name</label>
                                <input name="name" id="name" value="{{ old('name') ? old('name') : $song->name }}" type="text" placeholder="Name" class="form-control">
                            </div>
                            <div class="form-group {{ !$errors->has('description') ?: 'has-error' }}">
                                @foreach($errors->get('description') as $message)
                                    <label class="control-label" for="description">{{ $message }}</label><br>
                                @endforeach
                                <label for="description">Description</label>
                                    <textarea name="description" id="description" type="text" placeholder="Description" class="form-control" rows="4">{{ old('description') ? old('description') : $song->description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label id="hidden">
                                    <input name="hidden" id="hidden" value="1" type="checkbox" {{ !$song->deleted_at ?: 'checked' }}>
                                    Hidden
                                </label>
                            </div>
                            <button id="deleteSong" type="button" class="btn btn-danger pull-left">Delete</button>
                            <button type="submit" class="btn btn-success pull-right">Save</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Song details</h3>

                    </div>
                    <div class="box-body">
                        <dl class="dl-horizontal">
                            <dt>Name:</dt>
                            <dd>{{ $song->details->first()->song_name }}</dd>
                            <dt>Artist Name:</dt>
                            <dd>{{ $song->details->first()->artist_name }}</dd>
                            <dt>Author:</dt>
                            <dd>{{ $song->details->first()->author_name }}</dd>
                            <dt>Plays:</dt>
                            <dd>{{ $song->details->first()->play_count }}</dd>
                            <dt>Downloads:</dt>
                            <dd>{{ $song->details->first()->download_count }}</dd>
                            <dt>Beatmaps:</dt>
                            <dd>
                                @foreach($song->details->first()->beatmaps as $beatmap)
                                    {{ $beatmap }} <br>
                                @endforeach
                            </dd>
                            <dt>Upvotes:</dt>
                            <dd>{{ $song->details->first()->votes->where('direction', true)->count() }}</dd>
                            <dt>Downvotes:</dt>
                            <dd>{{ $song->details->first()->votes->where('direction', false)->count() }}</dd>
                            <dt>Uploaded:</dt>
                            <dd>{{ $song->created_at }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('admin.song.modal-delete', ['song' => $song])
@endsection
