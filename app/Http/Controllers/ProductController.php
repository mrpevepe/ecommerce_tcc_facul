<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use App\Models\ProductVariationImage;
use App\Models\Size;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Paginação com 5 produtos por página
        $products = $query->paginate(5)->appends($request->only(['search', 'status']));

        return view('admin.products', compact('products'));
    }

    public function indexPublic(Request $request)
    {
        $query = Product::with(['variations' => function ($q) {
            $q->where('status', 'ativo');
        }])->where('status', 'ativo')->whereHas('variations', function ($q) {
            $q->where('status', 'ativo');
        });

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12)->appends($request->only(['search', 'category_id']));

        $categories = Category::all();
        return view('index', compact('products', 'categories'));
    }

    public function create()
    {
        $sizes = Size::all();
        $categories = Category::all();
        return view('admin.create-product', compact('sizes', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:60',
            'descricao' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:64',
            'category_id' => 'nullable|exists:categories,id',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations.*.nome_variacao' => 'required|string|max:60',
            'variations.*.preco' => 'required|numeric|min:0|max:99999999.99',
            'variations.*.quantidade_estoque.*.size_id' => 'required|exists:sizes,id',
            'variations.*.quantidade_estoque.*.quantity' => 'required|integer|min:0|max:9999999999',
            'variations.*.imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'marca' => $request->marca,
            'status' => 'ativo',
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        if (isset($request->variations) && is_array($request->variations)) {
            foreach ($request->variations as $variationData) {
                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'nome_variacao' => $variationData['nome_variacao'],
                    'preco' => $variationData['preco'],
                ]);

                if (isset($variationData['quantidade_estoque']) && is_array($variationData['quantidade_estoque'])) {
                    foreach ($variationData['quantidade_estoque'] as $stock) {
                        $variation->sizes()->attach($stock['size_id'], ['quantity' => $stock['quantity']]);
                    }
                }

                if (isset($variationData['imagem']) && $variationData['imagem']) {
                    $path = $variationData['imagem']->store('product_variations', 'public');
                    ProductVariationImage::create([
                        'variation_id' => $variation->id,
                        'path' => $path,
                        'is_main' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produto criado com sucesso!');
    }

    public function edit($id)
    {
        $product = Product::with('variations.images', 'variations.sizes', 'category')->findOrFail($id);
        $sizes = Size::all();
        $categories = Category::all();
        return view('admin.edit-product', compact('product', 'sizes', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:60',
            'descricao' => 'nullable|string|max:255',
            'marca' => 'nullable|string|max:64',
            'category_id' => 'nullable|exists:categories,id',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations.*.nome_variacao' => 'required|string|max:60',
            'variations.*.preco' => 'required|numeric|min:0|max:99999999.99',
            'variations.*.quantidade_estoque.*.size_id' => 'required|exists:sizes,id',
            'variations.*.quantidade_estoque.*.quantity' => 'required|integer|min:0|max:9999999999',
            'variations.*.imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'marca' => $request->marca,
            'category_id' => $request->category_id,
        ]);

        if ($request->hasFile('imagem')) {
            $oldImage = $product->images()->where('is_main', true)->first();
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }
            $path = $request->file('imagem')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        if (isset($request->variations) && is_array($request->variations)) {
            foreach ($request->variations as $index => $variationData) {
                if (isset($variationData['id'])) {
                    $variation = ProductVariation::find($variationData['id']);
                    if ($variation) {
                        $variation->update([
                            'nome_variacao' => $variationData['nome_variacao'],
                            'preco' => $variationData['preco'],
                        ]);

                        if (isset($variationData['quantidade_estoque']) && is_array($variationData['quantidade_estoque'])) {
                            $variation->sizes()->sync(
                                collect($variationData['quantidade_estoque'])->mapWithKeys(function ($stock) {
                                    return [$stock['size_id'] => ['quantity' => $stock['quantity']]];
                                })->toArray()
                            );
                        }

                        if (isset($variationData['imagem']) && $variationData['imagem']) {
                            $oldImage = $variation->images()->where('is_main', true)->first();
                            if ($oldImage) {
                                Storage::disk('public')->delete($oldImage->path);
                                $oldImage->delete();
                            }
                            $path = $variationData['imagem']->store('product_variations', 'public');
                            ProductVariationImage::create([
                                'variation_id' => $variation->id,
                                'path' => $path,
                                'is_main' => true,
                            ]);
                        }
                    }
                } else {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'nome_variacao' => $variationData['nome_variacao'],
                        'preco' => $variationData['preco'],
                        'status' => 'ativo',
                    ]);

                    if (isset($variationData['quantidade_estoque']) && is_array($variationData['quantidade_estoque'])) {
                        foreach ($variationData['quantidade_estoque'] as $stock) {
                            $variation->sizes()->attach($stock['size_id'], ['quantity' => $stock['quantity']]);
                        }
                    }

                    if (isset($variationData['imagem']) && $variationData['imagem']) {
                        $path = $variationData['imagem']->store('product_variations', 'public');
                        ProductVariationImage::create([
                            'variation_id' => $variation->id,
                            'path' => $path,
                            'is_main' => true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produto atualizado com sucesso!');
    }


    public function showVariations($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $variations = $product->variations()->with('images', 'sizes')->paginate(5); // 5 variações por página
        $sizes = Size::all();
        return view('admin.variations', compact('product', 'variations', 'sizes'));
    }

    public function storeVariation(Request $request, $id)
    {
        $request->validate([
            'nome_variacao' => 'required|string|max:60',
            'preco' => 'required|numeric|min:0|max:99999999.99',
            'quantidade_estoque.*.size_id' => 'required|exists:sizes,id',
            'quantidade_estoque.*.quantity' => 'required|integer|min:0|max:9999999999',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $variation = ProductVariation::create([
            'product_id' => $product->id,
            'nome_variacao' => $request->nome_variacao,
            'preco' => $request->preco,
        ]);

        if (isset($request->quantidade_estoque) && is_array($request->quantidade_estoque)) {
            $variation->sizes()->sync(
                collect($request->quantidade_estoque)->mapWithKeys(function ($stock) {
                    return [$stock['size_id'] => ['quantity' => $stock['quantity']]];
                })->toArray()
            );
        }

        if ($request->hasFile('imagem')) {
            $path = $request->file('imagem')->store('product_variations', 'public');
            ProductVariationImage::create([
                'variation_id' => $variation->id,
                'path' => $path,
                'is_main' => true,
            ]);
        }

        return redirect()->route('admin.products.variations', $product->id)->with('success', 'Variação adicionada com sucesso!');
    }

    public function editStock($variationId)
    {
        $variation = ProductVariation::with('sizes', 'product.category')->findOrFail($variationId);
        $sizes = Size::all();
        return view('admin.edit-stock', compact('variation', 'sizes'));
    }

    public function saveStock(Request $request, $variationId)
    {
        // Se for uma requisição JSON, usar os dados do JSON
        if ($request->wantsJson() || $request->isJson()) {
            $stockData = $request->json('quantidade_estoque');
        } else {
            $stockData = $request->quantidade_estoque;
        }

        $request->validate([
            'quantidade_estoque.*.size_id' => 'required|exists:sizes,id',
            'quantidade_estoque.*.quantity' => 'required|integer|min:0|max:99999999',
        ]);

        $variation = ProductVariation::findOrFail($variationId);
        $variation->sizes()->sync(
            collect($stockData)->mapWithKeys(function ($stock) {
                return [$stock['size_id'] => ['quantity' => $stock['quantity']]];
            })->toArray()
        );

        // Retornar JSON para requisições AJAX
        if ($request->wantsJson() || $request->isJson()) {
            return response()->json(['success' => true, 'message' => 'Estoque atualizado com sucesso!']);
        }

        return redirect()->route('admin.products.variations', $variation->product_id)->with('success', 'Estoque atualizado com sucesso!');
    }

    public function show($id)
    {
        $product = Product::with(['variations' => function ($q) {
            $q->where('status', 'ativo')->with('sizes', 'images');
        }, 'images', 'category', 'comments.user'])->findOrFail($id);
        return view('show', compact('product'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::with('variations.images', 'variations.sizes', 'category')->findOrFail($id);
        $variationId = $request->input('variation_id');
        $quantity = $request->input('quantity', 1);
        $sizeId = $request->input('size_id');

        $request->validate([
            'variation_id' => 'required|exists:product_variations,id',
            'size_id' => 'required|exists:sizes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        $variation = $product->variations->find($variationId);

        if ($variation && $sizeId) {
            $stock = $variation->sizes()->where('size_id', $sizeId)->first()->pivot->quantity ?? 0;
            if ($stock >= $quantity) {
                $cartKey = $variationId . '_' . $sizeId;
                $size = Size::find($sizeId);
                $cart[$cartKey] = [
                    'product_id' => $product->id,
                    'variation_id' => $variationId,
                    'name' => $product->nome,
                    'variation_name' => $variation->nome_variacao,
                    'price' => $variation->preco,
                    'quantity' => $quantity,
                    'size_id' => $sizeId,
                    'size_name' => $size->name,
                    'image' => $variation->images->where('is_main', true)->first()->path ?? $product->images->where('is_main', true)->first()->path,
                    'stock' => $stock,
                    'category' => $product->category->name ?? 'Sem categoria',
                ];

                session()->put('cart', $cart);
                return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
            }
        }

        return redirect()->back()->with('error', 'Quantidade indisponível ou variação/tamanho inválido.');
    }

    public function cart()
    {
        $cart = session()->get('cart');
        return view('cart', compact('cart'));
    }

    public function removeFromCart($cartKey)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item removido do carrinho!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function updateCartQuantity(Request $request)
    {
        $cartKey = $request->input('cart_key');
        $quantity = $request->input('quantity');
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            $parts = explode('_', $cartKey);
            $variationId = $parts[0];
            $sizeId = $parts[1];

            $variation = ProductVariation::find($variationId);
            if ($variation) {
                $stock = $variation->sizes()->where('size_id', $sizeId)->first()->pivot->quantity ?? 0;
                if ($stock >= $quantity && $quantity > 0) {
                    $cart[$cartKey]['quantity'] = $quantity;
                    $cart[$cartKey]['stock'] = $stock;
                    session()->put('cart', $cart);
                    return redirect()->route('cart.index')->with('success', 'Quantidade atualizada!');
                } else {
                    return redirect()->route('cart.index')->with('error', 'Quantidade inválida ou indisponível.');
                }
            }
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function createCategory(Request $request)
    {
        $query = Category::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $categories = $query->get();
        return view('admin.create-category', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60|unique:categories',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories.create', [
            'search' => $request->search,
            'status' => $request->status
        ])->with('success', 'Categoria criada com sucesso!');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::all();
        return view('admin.edit-category', compact('category', 'categories'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:60|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.categories.create', [
            'search' => $request->search,
            'status' => $request->status
        ])->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.create')->with('success', 'Categoria excluída com sucesso!');
    }

    public function updateVariationStatus($variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        $newStatus = $variation->status === 'ativo' ? 'inativo' : 'ativo';
        $variation->update(['status' => $newStatus]);
        return redirect()->back()->with('success', 'Status da variação atualizado com sucesso!');
    }

    public function updateStatus($productId)
    {
        $product = Product::findOrFail($productId);
        $newStatus = $product->status === 'ativo' ? 'inativo' : 'ativo';
        $product->update(['status' => $newStatus]);
        return redirect()->back()->with('success', 'Status do produto atualizado com sucesso!');
    }

    public function updateCategoryStatus($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $newStatus = $category->status === 'ativo' ? 'inativo' : 'ativo';
        $category->update(['status' => $newStatus]);

        return redirect()->route('admin.categories.create', [
            'search' => request()->search,
            'status' => request()->status
        ])->with('success', 'Status da categoria atualizado com sucesso!');
    }
}