<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-md border border-gray-100">
    <div class="text-center mb-8">
        <h3 class="text-2xl font-bold text-gray-800">Unggah Berkas Legalitas</h3>
        <p class="text-gray-500 text-sm">Pastikan dokumen (NIB/Surat Keterangan) terbaca jelas (Format PDF/JPG, Max 2MB)</p>
    </div>

    <form action="{{ route('docs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
            <select name="document_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="NIB">NIB (Nomor Induk Berusaha)</option>
                <option value="Surat Keterangan">Surat Keterangan Warga</option>
            </select>
        </div>

        <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-blue-400 transition-colors">
            <input type="file" name="file" id="fileInput" class="hidden" required>
            <label for="fileInput" class="cursor-pointer">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-1 text-blue-600 font-semibold">Klik untuk pilih file atau drag and drop</p>
            </label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition duration-300">
            Kirim Dokumen untuk Validasi
        </button>
    </form>
</div>