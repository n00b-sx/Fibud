<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['transactions', 'budgets'])->orderBy('type')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'type' => 'required|in:income,expense',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'type' => 'required|in:income,expense',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
