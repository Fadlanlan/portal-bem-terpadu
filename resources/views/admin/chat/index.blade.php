@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-bold mb-4">Inbox Live Chat</h1>
<table class="w-full text-sm">
<thead><tr><th>ID</th><th>Status</th><th>Terakhir</th><th>Aksi</th></tr></thead>
<tbody>
@foreach($conversations as $c)
<tr>
<td>{{ $c->id }}</td>
<td>{{ $c->status }}</td>
<td>{{ optional($c->last_message_at)->format('d/m/Y H:i') }}</td>
<td><a href="{{ route('admin.chat.show', $c) }}" class="text-blue-600">Detail</a></td>
</tr>
@endforeach
</tbody>
</table>
{{ $conversations->links() }}
@endsection