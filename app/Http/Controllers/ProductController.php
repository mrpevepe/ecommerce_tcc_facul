<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductImage;
use App\Models\ProductVariationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products', compact('products'));
    }

    public function indexPublic()
    {
        $products = Product::with('variations')->get();
        return view('index', compact('products'));
    }

    public function create()
    {
        return view('admin.create-product');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'marca' => 'nullable|string|max:255',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations.*.nome_variacao' => 'required|string|max:255',
            'variations.*.quantidade_estoque' => 'required|integer|min:0',
            'variations.*.preco' => 'required|numeric|min:0',
            'variations.*.imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'marca' => $request->marca,
            'status' => 'ativo',
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
                    'quantidade_estoque' => $variationData['quantidade_estoque'],
                    'preco' => $variationData['preco'],
                ]);

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
        $product = Product::with('variations.images')->findOrFail($id);
        return view('admin.edit-product', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'marca' => 'nullable|string|max:255',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variations.*.nome_variacao' => 'required|string|max:255',
            'variations.*.quantidade_estoque' => 'required|integer|min:0',
            'variations.*.preco' => 'required|numeric|min:0',
            'variations.*.imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'marca' => $request->marca,
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
                            'quantidade_estoque' => $variationData['quantidade_estoque'],
                            'preco' => $variationData['preco'],
                        ]);
                    }
                } else {
                    $variation = ProductVariation::create([
                        'product_id' => $product->id,
                        'nome_variacao' => $variationData['nome_variacao'],
                        'quantidade_estoque' => $variationData['quantidade_estoque'],
                        'preco' => $variationData['preco'],
                    ]);
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
        }

        return redirect()->route('admin.products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function updateStock(Request $request, $variationId)
    {
        $request->validate([
            'quantidade_estoque' => 'required|integer|min:0',
        ]);

        $variation = ProductVariation::findOrFail($variationId);
        $productId = $variation->product_id;
        $variation->update([
            'quantidade_estoque' => $request->quantidade_estoque,
        ]);

        return redirect()->route('admin.products.variations', $productId)->with('success', 'Estoque atualizado com sucesso!');
    }

    public function showVariations($id)
    {
        $product = Product::with('variations.images')->findOrFail($id);
        return view('admin.variations', compact('product'));
    }

    public function storeVariation(Request $request, $id)
    {
        $request->validate([
            'nome_variacao' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'quantidade_estoque' => 'required|integer|min:0',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $variation = ProductVariation::create([
            'product_id' => $product->id,
            'nome_variacao' => $request->nome_variacao,
            'quantidade_estoque' => $request->quantidade_estoque,
            'preco' => $request->preco,
        ]);

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

    public function show($id)
    {
        $product = Product::with('variations.images')->findOrFail($id);
        return view('show', compact('product'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::with('variations.images')->findOrFail($id);
        $variationId = $request->input('variation_id');
        $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);
        $variation = $product->variations->find($variationId);

        if ($variation && $variation->quantidade_estoque >= $quantity) {
            $cart[$variationId] = [
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'name' => $product->nome,
                'variation_name' => $variation->nome_variacao,
                'price' => $variation->preco,
                'quantity' => $quantity,
                'image' => $variation->images->where('is_main', true)->first()->path ?? $product->images->where('is_main', true)->first()->path,
            ];

            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Produto adicionado ao carrinho!');
        }

        return redirect()->back()->with('error', 'Quantidade indisponível ou variação inválida.');
    }

    public function cart()
    {
        $cart = session()->get('cart');
        return view('cart', compact('cart'));
    }

    public function removeFromCart($variationId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$variationId])) {
            unset($cart[$variationId]);
            session()->put('cart', $cart);
            return redirect()->route('cart.index')->with('success', 'Item removido do carrinho!');
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }

    public function updateCartQuantity(Request $request)
    {
        $variationId = $request->input('variation_id');
        $quantity = $request->input('quantity');
        $cart = session()->get('cart', []);

        if (isset($cart[$variationId])) {
            $variation = ProductVariation::find($variationId);
            if ($variation && $variation->quantidade_estoque >= $quantity && $quantity > 0) {
                $cart[$variationId]['quantity'] = $quantity;
                session()->put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Quantidade atualizada!');
            } else {
                return redirect()->route('cart.index')->with('error', 'Quantidade inválida ou indisponível.');
            }
        }

        return redirect()->route('cart.index')->with('error', 'Item não encontrado no carrinho.');
    }
}