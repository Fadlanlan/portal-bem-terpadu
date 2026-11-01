@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-bold mb-4">Percakapan #{{ $conversation->id }}</h1>
<div class="space-y-2 mb-4">
@foreach($messages as $m)
<div class="rounded border p-2"><strong>{{ $m->sender_type }}:</strong> {{ $m->body }}</div>
@endforeach
</div>
<form method="POST" action="{{ route('admin.chat.reply', $conversation) }}" class="mb-4">
@csrf
<textarea name="body" class="w-full border rounded p-2" required></textarea>
<button class="mt-2 rounded bg-blue-600 px-3 py-2 text-white">Balas</button>
</form>
<form method="POST" action="{{ route('admin.chat.assign', $conversation) }}" class="mb-4">
@csrf @method('PUT')
<select name="admin_id" class="border rounded p-2">
<option value="">-- lepas --</option>
@foreach($admins as $a)
<option value="{{ $a->id }}">{{ $a->name }}</option>
@endforeach
</select>
<button class="mt-2 rounded bg-slate-600 px-3 py-2 text-white">Assign</button>
</form>
<form method="POST" action="{{ route('admin.chat.resolve', $conversation) }}">
@csrf
<button class="rounded bg-green-600 px-3 py-2 text-white">Selesai</button>
</form>
@endsection