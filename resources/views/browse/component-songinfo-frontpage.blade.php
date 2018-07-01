<h2>{{ $name }}</h2>
<table id="song-{{ $id }}" class="table" style="table-layout:fixed;">
    <tr>
        <th rowspan="5" style="width: 15%;" class="text-center">
            <div>
                <img src="{{ asset("storage/songs/$cover.$coverMime") }}" alt="{{ $name }}" style="min-width: 10em; max-width: 10em;">
            </div>
            <br/>
            <div>
                <a class="btn btn-default" href="{{ route('browse.detail', ['key' => $downloadKey]) }}" role="button">Details</a>
            </div>
        </th>
        <th colspan="2">
            <small>Uploaded by: <a href="{{ route('browse.user',['id' => $uploaderId]) }}">{{ $uploader }}</a> ({{ $createdAt }})</small>
        </th>
    </tr>
    <tr>
        <td colspan="2">Song: {{ $songName }} - {{ $songSubName }}</td>
    </tr>
    <tr>
        <td>Author: {{ $authorName }}</td>
        <td>Difficulties: {{ $difficulties }}</td>
    </tr>
    <tr>
        <td colspan="2">
            Downloads: {{ $downloadCount }} || Finished: {{ $playedCount }} || <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span> {{ $upVotes }} / <span
                    class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span> {{ $downVotes }}
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <a class="btn btn-default" href="{{ route('download', ['key' => $downloadKey]) }}" role="button">Download File</a>
        </td>
    </tr>
</table>
