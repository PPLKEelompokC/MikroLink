<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('ticketing.index', compact('tickets'));
    }

    public function create()
    {
        return view('ticketing.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|in:umum,pinjaman,pembayaran,teknis,lainnya',
            'priority'    => 'required|in:low,medium,high',
            'description' => 'required|string|min:20',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'subject.required'     => 'Subjek tiket wajib diisi.',
            'category.required'    => 'Kategori wajib dipilih.',
            'priority.required'    => 'Prioritas wajib dipilih.',
            'description.required' => 'Deskripsi masalah wajib diisi.',
            'description.min'      => 'Deskripsi minimal 20 karakter.',
            'attachment.mimes'     => 'File hanya boleh berformat JPG, PNG, atau PDF.',
            'attachment.max'       => 'Ukuran file maksimal 2MB.',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id'       => Auth::id(),
            'subject'       => $validated['subject'],
            'category'      => $validated['category'],
            'priority'      => $validated['priority'],
            'description'   => $validated['description'],
            'status'        => 'open',
            'attachment'    => $attachmentPath,
        ]);

        return redirect()
            ->route('ticketing.show', $ticket)
            ->with('success', 'Tiket berhasil dibuat! Nomor tiket Anda: ' . $ticket->ticket_number);
    }

    public function show(Ticket $ticket)
    {
        // Pastikan user hanya bisa lihat tiket miliknya sendiri
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('ticketing.show', compact('ticket'));
    }
}