<h1>Edit Place</h1>

@if ($errors->any())
    <div>
        <strong>Data belum valid:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('/places/'.$place->id) }}">
    @csrf
    @method('PUT')

    <div>
        <label>Nama Place</label>
        <input type="text" name="name" value="{{ old('name', $place->name) }}">
    </div>

    <div>
        <label>Kategori</label>
        <select name="category_id">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label>Deskripsi Singkat</label>
        <input type="text" name="short_description" value="{{ old('short_description', $place->short_description) }}">
    </div>

    <div>
        <label>Deskripsi</label>
        <textarea name="description">{{ old('description', $place->description) }}</textarea>
    </div>

    <div>
        <label>Alamat</label>
        <input type="text" name="address" value="{{ old('address', $place->address) }}">
    </div>

    <div>
        <label>Kota</label>
        <input type="text" name="city" value="{{ old('city', $place->city) }}">
    </div>

    <div>
        <label>Provinsi</label>
        <input type="text" name="province" value="{{ old('province', $place->province) }}">
    </div>

    <div>
        <label>Google Maps URL</label>
        <input type="text" name="google_maps_url" value="{{ old('google_maps_url', $place->google_maps_url) }}">
    </div>

    <button type="submit">Update</button>
</form>

<a href="{{ url('/places') }}">Kembali</a>