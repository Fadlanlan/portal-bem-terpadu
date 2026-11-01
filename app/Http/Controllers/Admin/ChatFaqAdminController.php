<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatFaq;
use Illuminate\Http\Request;

class ChatFaqAdminController extends Controller
{
    public function index()
    {
        $faqs = ChatFaq::orderBy('sort_order')->paginate(20);
        return view('admin.chat.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.chat.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['slug'] = \Illuminate\Support\Str::slug($data['question']);
        $data['is_published'] = $request->boolean('is_published');
        ChatFaq::create($data);
        return redirect()->route('admin.chat.faqs.index');
    }

    public function edit(ChatFaq $faq)
    {
        return view('admin.chat.faqs.edit', compact('faq'));
    }

    public function update(Request $request, ChatFaq $faq)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $faq->update($data);
        return redirect()->route('admin.chat.faqs.index');
    }

    public function destroy(ChatFaq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.chat.faqs.index');
    }
}
