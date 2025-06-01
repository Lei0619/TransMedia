@extends('layouts.convert')

@section('title', 'Facebook Conversion')

@section('content')
    <h1>Convert Facebook Videos</h1>

    <form method="POST" action="{{ route('conversions.store') }}" enctype="multipart/form-data">
        @csrf

        <label for="file">Upload File:</label>
        <input type="file" name="file" id="file">

        <label for="url">Or Enter Facebook Video URL:</label>
        <input type="text" name="url" id="url" placeholder="https://facebook.com/video">

        <label for="target_format">Convert To:</label>
        <select name="target_format" id="target_format">
            <option value="mp3">MP3</option>
            <option value="mp4">MP4</option>
            <option value="wav">WAV</option>
        </select>

        <button type="submit">Convert</button>
    </form>

    <hr>

    <h2>Your Facebook Conversions</h2>
    @if($conversions->isEmpty())
        <p>No conversions yet.</p>
    @else
        <table>
            <thead>
                <tr><th>Filename</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($conversions as $conversion)
                    <tr>
                        <td>{{ $conversion->original_filename }}</td>
                        <td>{{ $conversion->status }}</td>
                        <td>
                            @if($conversion->status === 'completed')
                                <a href="{{ route('conversions.download', $conversion) }}">Download</a>
                            @else
                                <button disabled>Processing...</button>
                            @endif

                            <form method="POST" action="{{ route('conversions.destroy', $conversion) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
