<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Aspiration;

class AspirationController extends Controller
{
    public function indexUser()
    {
        $aspirations = Aspiration::where('user_id', auth()->id())->latest()->get();
        return view('aspirationPortal', compact('aspirations'));
    }

    public function indexAdmin()
    {
        // Load aspirations along with the user data
        $aspirations = Aspiration::with('user')->latest()->get();
        return view('admin-dashboard', compact('aspirations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Aspiration::create([
            'user_id' => auth()->id() ?? 1, // Jika belum login, otomatis pakai ID 1
            'subject' => $request->subject,
            'message' => $request->message,
            'status'  => 'pending',
        ]);

        return redirect()->back()->with('success', 'Aspirasi berhasil diajukan!');
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $aspiration = Aspiration::findOrFail($id);
        $aspiration->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Status aspirasi berhasil diperbarui!');
    }
}
