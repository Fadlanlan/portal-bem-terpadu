@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-bold mb-4">FAQ Chat</h1>
<a href="{{ route('admin.chat_faqs.create') }}" class="mb-3 inline-block rounded bg-blue-600 px-3 py-2 text-white">Tambah</a>
<table class="w-full text-sm">
<thead><tr><th>Pertanyaan</th><th>Aktif</th><th>Aksi</th></tr></thead>
<tbody>
@foreach($faqs as $f)
<tr>
<td>{{ $f->question }}</td>
<td>{{ $f->is_published ? 'Ya' : 'Tidak' }}</td>
<td>
<a href="{{ route('admin.chat_faqs.edit', $f) }}" class="text-blue-600">Edit</a>
<form method="POST" action="{{ route('admin.chat_faqs.destroy', $f) }}" class="inline">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
</td>
</tr>
@endforeach
</tbody>
</table>
{{ $faqs->links() }}
@endsection