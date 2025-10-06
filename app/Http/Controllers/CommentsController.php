<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    /**
     * Exibe a lista de avaliações de um produto para o administrador.
     *
     * @param  int  $productId
     * @return \Illuminate\View\View
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $comments = $product->comments()
            ->with('user')
            ->when(request('search'), function($query) {
                $search = request('search');
                $query->where('id', 'LIKE', "%{$search}%")
                    ->orWhere('titulo', 'LIKE', "%{$search}%")
                    ->orWhere('descricao', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereDate('created_at', 'LIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('admin.comments', compact('product', 'comments'));
    }

    /**
     * Armazena um novo comentário para o produto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'titulo' => 'required|string|max:40',
            'descricao' => 'required|string|max:140',
        ]);

        // Verificar se o usuário comprou e recebeu o produto
        $canComment = Order::where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->whereHas('items', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->exists();

        if (!$canComment) {
            return redirect()->back()->with('error', 'Você precisa ter comprado e recebido este produto para adicionar uma avaliação.');
        }

        Comment::create([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'product_id' => $productId,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Avaliação adicionada com sucesso!');
    }

    /**
     * Atualiza um comentário existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @param  int  $commentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $productId, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Verificar se o comentário pertence ao usuário
        if ($comment->user_id !== Auth::id() || $comment->product_id !== (int) $productId) {
            abort(403);
        }

        $request->validate([
            'titulo' => 'required|string|max:40',
            'descricao' => 'required|string|max:140',
        ]);

        $comment->update([
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
        ]);

        return redirect()->back()->with('success', 'Avaliação atualizada com sucesso!');
    }

    /**
     * Remove um comentário existente.
     *
     * @param  int  $productId
     * @param  int  $commentId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($productId, $commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Verificar se o comentário pertence ao produto e se o usuário é administrador
        if ($comment->product_id !== (int) $productId && Auth::user()->cargo !== 'administrador') {
            abort(403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Avaliação removida com sucesso!');
    }
}