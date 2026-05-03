<?php

namespace App\Http\Controllers;

use App\Models\LiterasiArtikel;
use Illuminate\Http\Request;

class LiterasiController extends Controller
{
    public function index(Request $request)
    {
        $kategoriAktif  = $request->get('kategori');

        $artikelQuery = LiterasiArtikel::published()->latest();
        if ($kategoriAktif) {
            $artikelQuery->kategori($kategoriAktif);
        }

        $artikels       = $artikelQuery->paginate(9)->withQueryString();
        $daftarKategori = LiterasiArtikel::daftarKategori();

        return view('literasi.index', compact(
            'artikels', 'daftarKategori', 'kategoriAktif'
        ));
    }

    public function show(string $slug)
    {
        $artikel = LiterasiArtikel::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $artikel->increment('views');

        $artikelTerkait = LiterasiArtikel::published()
            ->where('kategori', $artikel->kategori)
            ->where('id', '!=', $artikel->id)
            ->take(3)
            ->get();

        return view('literasi.show', compact('artikel', 'artikelTerkait'));
    }
}