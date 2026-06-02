<h1>Daftar Places</h1>

<a href="{{ url('/places/create') }}">Tambah Place</a>

@foreach ($places as $place)
    <div>
        <h2>{{ $place->name }}</h2>
        <p>{{ $place->city }}</p>
        <a href="{{ url('/places/'.$place->id.'/edit') }}">Edit</a>
    </div>
@endforeach