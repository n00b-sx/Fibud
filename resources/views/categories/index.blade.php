@extends('layouts.app')

@section('title', 'Kelola Kategori - Fibud')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Kelola Kategori Transaksi</h1>
            <p class="text-xs sm:text-sm text-gray-500">Ubah atau hapus jenis kategori pemasukan dan pengeluaran</p>
        </div>
        <div>
            <button type="button" class="py-2 px-3.5 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-orange-500 text-white hover:bg-orange-600 shadow-sm transition" data-hs-overlay="#hs-add-category-modal">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Kategori Baru
            </button>
        </div>
    </div>

    <!-- CATEGORIES TABLE CARD (PURE WHITE CARDS + SHADOW-SM ON CREME BG) -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-xs font-semibold text-gray-500 uppercase">
                        <th class="py-3 px-4 text-start">Nama Kategori</th>
                        <th class="py-3 px-4 text-start">Tipe</th>
                        <th class="py-3 px-4 text-center">Jumlah Transaksi</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="py-3 px-4 font-bold text-gray-900">
                            {{ $cat->name }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-x-1.5 py-1 px-2.5 rounded-lg text-xs font-medium {{ $cat->type === 'income' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                {{ $cat->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center text-xs font-medium text-gray-600">
                            {{ $cat->transactions_count }} transaksi
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-x-2">
                                <!-- Button Edit Modal -->
                                <button type="button" class="text-xs font-semibold text-orange-600 hover:text-orange-800 py-1 px-2 rounded-lg hover:bg-orange-50 transition" data-hs-overlay="#hs-edit-category-modal-{{ $cat->id }}">
                                    Ubah
                                </button>

                                <!-- Button Hapus -->
                                <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Menghapus kategori juga akan menghapus transaksi terkait. Lanjutkan?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 py-1 px-2 rounded-lg hover:bg-rose-50 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500 text-sm">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL TAMBAH KATEGORI BARU -->
<div id="hs-add-category-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-900">Tambah Kategori Baru</h3>
                <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-add-category-modal">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Nama Kategori</label>
                        <input type="text" name="name" required placeholder="Contoh: Investasi, Tagihan Listrik" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Tipe Kategori</label>
                        <select name="type" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="expense" selected>Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                    <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-add-category-modal">Batal</button>
                    <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT KATEGORI -->
@foreach($categories as $cat)
<div id="hs-edit-category-modal-{{ $cat->id }}" class="hs-overlay hidden size-full fixed top-0 start-0 z-80 overflow-y-auto overflow-x-hidden pointer-events-none" tabindex="-1" role="dialog">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 opacity-0 transition-all sm:max-w-md sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
        <div class="w-full flex flex-col bg-white border border-gray-200 rounded-xl shadow-sm pointer-events-auto">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-900">Edit Kategori: {{ $cat->name }}</h3>
                <button type="button" class="size-8 inline-flex justify-center items-center rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" data-hs-overlay="#hs-edit-category-modal-{{ $cat->id }}">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('categories.update', $cat->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Nama Kategori</label>
                        <input type="text" name="name" value="{{ $cat->name }}" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-1">Tipe Kategori</label>
                        <select name="type" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="expense" {{ $cat->type === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="income" {{ $cat->type === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-x-2 py-3 px-4 border-t border-gray-200">
                    <button type="button" class="py-2 px-3 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50" data-hs-overlay="#hs-edit-category-modal-{{ $cat->id }}">Batal</button>
                    <button type="submit" class="py-2 px-3 text-sm font-medium rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
