<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Exibe a página principal do usuário com endereço e lista de pedidos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->latest()->get();

        return view('user.index', compact('user', 'orders'));
    }

    /**
     * Exibe o formulário para adicionar ou editar endereço.
     *
     * @return \Illuminate\View\View
     */
    public function showAddressForm()
    {
        $user = Auth::user();
        $endereco = $user->endereco ?? null;

        return view('user.address', compact('endereco'));
    }

    /**
     * Salva ou atualiza o endereço do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
public function saveAddress(Request $request)
    {
        try {
            Log::info('Tentando salvar endereço para user ID: ' . Auth::id());

            $request->validate([
                'logradouro' => 'required|string|max:40',
                'numero' => 'required|string|max:20',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'required|string|max:40',
                'cep' => 'required|string|size:9',
                'nome_cidade' => 'required|string|max:30',
                'estado' => 'required|string|size:2',
            ]);

            $user = Auth::user();

            if ($user->endereco_id) {
                $endereco = Endereco::findOrFail($user->endereco_id);
                $endereco->update($request->all());
            } else {
                $endereco = Endereco::create($request->all());
                $user->endereco_id = $endereco->id;
                $user->save();
            }

            Log::info('Endereço salvo com sucesso para user ID: ' . Auth::id());

            return redirect()->route('user.index')->with('success', 'Endereço salvo com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao salvar endereço: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Erro geral ao salvar endereço: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao salvar endereço: ' . $e->getMessage())->withInput();
        }
    }
}