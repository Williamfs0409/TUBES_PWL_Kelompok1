<h1>Daftar Places</h1>

@if (session('status'))
    <p>{{ session('status') }}</p>
@endif

<a href="{{ url('/places/create') }}">Tambah Place</a>

@foreach ($places as $place)
    <div>
        <h2>{{ $place->name }}</h2>
        <p>{{ $place->city }}</p>

        <a href="{{ url('/places/'.$place->id.'/edit') }}">Edit</a>

        <form method="POST" action="{{ url('/places/'.$place->id) }}">
            @csrf
            @method('DELETE')

            <button type="submit" onclick="return confirm('Yakin ingin menghapus place ini?')">
                Hapus
            </button>
        </form>
    </div>
@endforeach