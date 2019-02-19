@extends('themes.bulma.layout')
@section('title', '- Song Upload')

@section('content')
    @if(auth()->user()->isVerified())
        <div class="content">
            <form class="form-signin" method="post" enctype="multipart/form-data" action="{{ route('upload.submit') }}">
                {{ csrf_field() }}
                <h2 class="form-signin-heading" style="margin-top: -1rem;">Upload Beat Track</h2>

                <input type="text" name="name" id="inputTitle" class="input" placeholder="Title (160 Letters Max)" maxlength="160" required autofocus/>

                <div class="field" style="margin: 10px 0;">
                    <label class="label">File Input</label>
                    <div class="file has-name">
                        <label class="file-label">
                            <input
                                style="display: none;"
                                class="file-input"
                                type="file"
                                id="InputFile"
                                accept="application/zip"
                                name="fileupload"
                            >
                            <span class="file-cta">
                                <span class="file-icon">
                                    <i class="fas fa-upload"></i>
                                </span>
                                <span class="file-label">
                                    Choose a file...
                                </span>
                            </span>
                            
                            <span class="file-name" id="file-name">
                                No file selected.
                            </span>
                        </label>
                    </div>
                </div>

                <script>
                    var inp = document.getElementById('InputFile')
                    var label = document.getElementById('file-name')

                    inp.addEventListener('change', e => {
                        label.innerHTML = e.target.files[0].name
                    })
                </script>

                <h5 class="has-text-grey">Must meet the following upload rules</h5>
                <ul>
                    <li>Must be under 15MB</li>
                    <li>Must be a ZIP file with the songs subfolder in the root (EG: SongName/SongInfo.json)</li>
                    <li>Must contain valid metadata <strong>(UTF-8 encoded)</strong> in SongInfo.json</li>
                    <li>Must contain an ".ogg" audio file</li>
                    <li>Must contain at least one beatmap file.  Beatmap files should have "beatmap" in the file name and the extension must be ".json" or ".txt" (EG: SongName/song_beatmap_hard.json)
                    <li>Optionally include album art with a file named "cover.jpg" or "cover.png"</li>
                    <li>Make sure you have permission to use any content involved in your beatmap. This includes songs, videos, hit sounds, graphics,
                        and any other content that isn't your own creation
                    </li>
                    <li>Do not plagiarise or attempt to steal the work of others. Do not also upload or use other people's work without their explicit permission
                        (including, but not limited to, skins and guest beatmaps)
                    </li>
                </ul>

                <h5 class="has-text-grey">Useful tips for avoiding problems</h5>
                <ul>
                    <li>Avoid using UNICODE characters in folder or file names</li>
                    <li>Remove unnecessary content from the zip file. (Anything that isn't SongInfo.json, beatmap files, an audio file, or a cover image)</li>
                </ul>

                <div class="field">
                    <label class="label">Beatmap Description</label>
                    <textarea name="description" id="TextFile" class="textarea" rows="3"></textarea>
                    <h5 class="has-text-grey" style="margin: 10px 0;">Plain Text Only</h5>
                </div>

                <button class="button is-link is-fullwidth" type="submit">Submit</button>
            </form>

        </div>
    @else
        <article class="message is-danger">
            <div class="message-body">
                Your account is not verified. You won't be able to upload songs until you verify your account!
                Please visit your <a href="{{ route('profile') }}">profile</a> page if you need re-request the verification mail.
            </div>
        </article>
    @endif
@endsection
