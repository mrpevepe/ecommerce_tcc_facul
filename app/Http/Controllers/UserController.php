<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash; // Adicione esta linha

class UserController extends Controller
{
    /**
     * Exibe a página principal do usuário com endereço e lista de pedidos.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        $user = auth()->user();
        $search = request('search');
        
        $orders = $user->orders()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('id', 'LIKE', "%{$search}%")
                    ->orWhereHas('items.product', function($q) use ($search) {
                        $q->where('nome', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5); // 5 pedidos por página

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
                'numero' => 'required|string|max:20|regex:/^[0-9]+$/',
                'complemento' => 'nullable|string|max:100',
                'bairro' => 'required|string|max:40',
                'cep' => 'required|string|size:9',
                'nome_cidade' => 'required|string|max:30',
                'estado' => 'required|string|size:2',
            ], [
                'numero.regex' => 'O campo número deve conter apenas números.',
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

    public function orders()
    {
        return $this->index();
    }

    public static function translateStatus($status)
    {
        $statusMap = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];
        return $statusMap[$status] ?? $status;
    }

    public function reviews()
    {
        $search = request('search');
        $user = Auth::user();
        
        $reviews = $user->comments()
            ->with('product.images', 'product.category')
            ->when($search, function($query) use ($search) {
                $query->whereHas('product', function($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('user.reviews', compact('reviews'));
    }
 
    /**
     * Exibe o formulário para editar dados do usuário.
     *
     * @return \Illuminate\View\View
     */
    public function showEditForm()
    {
        $user = Auth::user();
        return view('user.edit', compact('user'));
    }

    /**
     * Atualiza os dados do usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'name' => ['required', 'string', 'max:60'],
                'telefone' => ['required', 'string', 'max:20', 'regex:/^\(\d{2}\) \d \d{4}-\d{4}$/'],
                'current_password' => ['required', 'string'],
                'password' => ['nullable', 'string', 'min:8', 'max:60', 'confirmed'],
            ], [
                // Mensagens personalizadas em português
                'name.required' => 'O campo nome é obrigatório.',
                'name.max' => 'O nome não pode ter mais de 60 caracteres.',
                'telefone.required' => 'O campo telefone é obrigatório.',
                'telefone.regex' => 'Formato de telefone inválido. Use (XX) X XXXX-XXXX.',
                'current_password.required' => 'A senha atual é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
                'password.max' => 'A senha não pode ter mais de 60 caracteres.',
                'password.confirmed' => 'A confirmação da senha não corresponde.',
            ]);

            // Verificar senha atual
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Senha atual incorreta.'])->withInput();
            }

            // Atualizar dados
            $user->name = $request->name;
            $user->telefone = $request->telefone;
            
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->route('user.index')->with('success', 'Dados atualizados com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar dados: ' . $e->getMessage())->withInput();
        }
    }

}