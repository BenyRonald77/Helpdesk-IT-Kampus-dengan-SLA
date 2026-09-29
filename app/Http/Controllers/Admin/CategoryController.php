<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()->orderBy('name')->withCount('tickets')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
        ]);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->tickets()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh tiket.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Kategori dihapus.');
    }
}
