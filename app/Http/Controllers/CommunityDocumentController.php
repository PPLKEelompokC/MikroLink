<?php

namespace App\Http\Controllers;

use App\Models\CommunityDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityDocumentController extends Controller
{
    /**
     * Show the legal document upload form for members.
     */
    public function create(): View
    {
        $documents = CommunityDocument::query()
            ->where('user_id', auth()->id())
            ->whereIn('document_name', ['NIB', 'Surat Keterangan'])
            ->latest()
            ->get();

        return view('community.upload', compact('documents'));
    }

    /**
     * Store a legal document submission for admin validation.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_name' => ['required', Rule::in(['NIB', 'Surat Keterangan'])],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $path = $request->file('file')->store('community-documents', 'local');

        CommunityDocument::create([
            'user_id' => $request->user()->id,
            'document_name' => $validated['document_name'],
            'file_path' => $path,
            'status' => 'pending',
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()
            ->route('docs.upload.form')
            ->with('success', 'Dokumen berhasil dikirim dan menunggu validasi admin.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $documents = CommunityDocument::with('user')->latest()->get();
        $stats = [
            'pending' => $documents->where('status', 'pending')->count(),
            'verified' => $documents->where('status', 'approved')->count(),
            'waiting_approval' => $documents->where('status', 'pending')->count(),
            'approved_today' => $documents
                ->where('status', 'approved')
                ->filter(fn (CommunityDocument $document): bool => $document->updated_at?->isToday() ?? false)
                ->count(),
        ];

        return view('admin.docs.index', compact('documents', 'stats'));
    }

    /**
     * Update the status of a document.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'note' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ]);

        $document = CommunityDocument::findOrFail($id);
        $document->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
