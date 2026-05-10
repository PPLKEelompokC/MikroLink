<?php

namespace App\Http\Controllers;

use App\Models\CommunityDocument;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommunityDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = CommunityDocument::with('user')->latest()->get();
        return view('admin.docs.index', compact('documents'));
    }

    /**
     * Update the status of a document.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'note' => 'nullable|string',
        ]);

        $document = CommunityDocument::findOrFail($id);
        $document->update([
            'status' => $request->status,
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui.');
    }
}
