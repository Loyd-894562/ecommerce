<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $products = Product::query()
            ->with(['category', 'seller'])
            ->where('status', 'active')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($filters['category'] ?? null, function ($query, int $categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()->orderBy('name')->get();

        return view('products.index', compact('categories', 'products'));
    }

    public function show(Request $request, Product $product): View
    {
        $user = $request->user();
        $canManage = $user && ($user->hasRole('admin') || $product->seller_id === $user->id);

        abort_if($product->status !== 'active' && ! $canManage, 404);

        $product->load(['category', 'seller', 'reviews.user']);
        $canReview = $user?->hasRole('customer')
            && $user->orders()
                ->where('status', 'delivered')
                ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
                ->exists();

        return view('products.show', compact('canManage', 'canReview', 'product'));
    }

    public function manage(Request $request): View
    {
        $user = $request->user();
        $products = Product::query()
            ->with('category')
            ->when(! $user->hasRole('admin'), fn ($query) => $query->where('seller_id', $user->id))
            ->orderByDesc('id')
            ->paginate(15);

        return view('products.manage.index', compact('products'));
    }

    public function create(Request $request): View
    {
        $categories = Category::query()->orderBy('name')->get();
        $sellers = $request->user()->hasRole('admin')
            ? User::query()->where('role', 'seller')->orderBy('name')->get()
            : collect();

        return view('products.manage.create', compact('categories', 'sellers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->productRules($request));
        $validated['seller_id'] = $request->user()->hasRole('admin')
            ? (int) $validated['seller_id']
            : $request->user()->id;

        Product::create($validated);

        return redirect()->route('manage.products.index')->with('status', 'Product created.');
    }

    public function edit(Request $request, Product $product): View
    {
        $this->authorizeProductManagement($request, $product);

        $categories = Category::query()->orderBy('name')->get();
        $sellers = $request->user()->hasRole('admin')
            ? User::query()->where('role', 'seller')->orderBy('name')->get()
            : collect();

        return view('products.manage.edit', compact('categories', 'product', 'sellers'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProductManagement($request, $product);

        $validated = $request->validate($this->productRules($request));
        if ($request->user()->hasRole('admin')) {
            $validated['seller_id'] = (int) $validated['seller_id'];
        }

        $product->update($validated);

        return redirect()->route('manage.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProductManagement($request, $product);
        $product->delete();

        return redirect()->route('manage.products.index')->with('status', 'Product deleted.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function productRules(Request $request): array
    {
        return [
            'seller_id' => $request->user()->hasRole('admin')
                ? ['required', 'integer', Rule::exists('users', 'id')->where('role', 'seller')]
                : ['prohibited'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'image_path' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    private function authorizeProductManagement(Request $request, Product $product): void
    {
        abort_unless(
            $request->user()->hasRole('admin') || $product->seller_id === $request->user()->id,
            403,
        );
    }
}
