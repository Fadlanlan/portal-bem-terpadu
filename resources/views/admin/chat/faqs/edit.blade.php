@extends('layouts.admin')
@section('content')
<h1 class="text-xl font-bold mb-4">Edit FAQ</h1>
<form method="POST" action="{{ route('admin.chat_faqs.update', $faq) }}">
@csrf @method('PUT')
<input name="question" value="{{ $faq->question }}" class="w-full border rounded p-2 mb-2" required>
<textarea name="answer" class="w-full border rounded p-2 mb-2" required>{{ $faq->answer }}</textarea>
<label><input type="checkbox" name="is_published" value="1" @checked($faq->is_published)> Aktif</label>
<input name="sort_order" type="number" class="border rounded p-2 ml-2" value="{{ $faq->sort_order }}">
<button class="mt-2 rounded bg-blue-600 px-3 py-2 text-white">Simpan</button>
</form>
@endsection