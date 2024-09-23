<form action="{{ route('apprenants.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Importer Apprenants</button>
</form>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif
