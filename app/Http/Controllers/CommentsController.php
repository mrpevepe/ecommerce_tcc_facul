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
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
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
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
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

        // Verificar se o comentário pertence ao usuário
        if ($comment->user_id !== Auth::id() || $comment->product_id !== (int) $productId) {
            abort(403);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Avaliação removida com sucesso!');
    }
}