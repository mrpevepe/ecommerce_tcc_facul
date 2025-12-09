# 🛍️ E-commerce de Roupas – Laravel & MySQL

### 📖 Descrição do Projeto

Este projeto consiste no **desenvolvimento de um sistema web de e-commerce** voltado à venda de camisetas online, criado para oferecer uma plataforma **funcional, prática e segura** tanto para clientes quanto para administradores.

O sistema foi desenvolvido em **PHP com o framework Laravel**, utilizando **MySQL** como banco de dados, e integra tecnologias como **HTML, CSS e JavaScript**, seguindo o padrão arquitetural **MVC (Model-View-Controller)**.

O objetivo principal é disponibilizar uma solução acessível para **pequenos empreendedores** que desejam comercializar produtos na internet com **baixo custo, boa usabilidade e segurança.**

---

### 🚀 Funcionalidades Principais

#### 👤 Usuário (Cliente)

-   Cadastro e login de usuários com autenticação segura (bcrypt);
-   Navegação por catálogo de produtos com filtros e busca;
-   Visualização detalhada dos produtos (descrição, preço, variações e avaliações);
-   Carrinho de compras com atualização de quantidades e cálculo automático do total;
-   Finalização de pedidos e acompanhamento do status;
-   Gerenciamento de endereço de entrega e perfil;
-   Avaliação de produtos adquiridos.

#### 🧑‍💼 Administrador

-   Painel administrativo (dashboard) com acesso restrito;
-   Cadastro, edição e exclusão de produtos, variações e categorias;
-   Controle de estoque em tempo real;
-   Gerenciamento de pedidos e atualização de status (pendente, em andamento, entregue);
-   Moderação de avaliações de usuários.

---

### 🧩 Tecnologias Utilizadas

| Camada                 | Tecnologia                                                |
| ---------------------- | --------------------------------------------------------- |
| **Frontend**           | HTML5, CSS3, JavaScript                                   |
| **Backend**            | PHP 8.x com Framework Laravel                             |
| **Banco de Dados**     | MySQL                                                     |
| **Arquitetura**        | MVC (Model-View-Controller)                               |
| **Outras Ferramentas** | Blade Templates, Bootstrap, Laravel Artisan, Eloquent ORM |

---

### 🧠 Padrão Arquitetural

O projeto segue o padrão **MVC**, garantindo:

-   Separação de responsabilidades (Model, View e Controller);
-   Facilidade de manutenção e escalabilidade;
-   Organização e reuso de código;
-   Integração entre camadas de forma estruturada.

---

### 🧱 Estrutura do Sistema

```
├── app/
│   ├── Http/Controllers/       # Controladores das rotas
│   ├── Models/                 # Modelos de dados (Produto, Pedido, Usuário, etc.)
│
├── resources/
│   ├── views/                  # Templates Blade (páginas do sistema)
│
├── routes/
│   ├── web.php                 # Definição das rotas principais
│
├── public/
│   ├── css/                    # Arquivos de estilo
│   ├── js/                     # Scripts de interação
│   ├── images/                 # Imagens dos produtos
│
├── database/
│   ├── migrations/             # Estrutura das tabelas
│
└── README.md
```

---

### ⚙️ Requisitos de Instalação

#### ✅ Pré-requisitos

-   PHP 8.x+
-   Composer
-   MySQL
-   XAMPP, Laragon ou similar

#### 🔧 Passos para rodar o projeto localmente

```bash
# 1. Clonar o repositório
git clone https://github.com/mrpevepe/ecommerce_tcc_facul.git

# 2. Acessar a pasta do projeto
cd ecommerce_tcc_facul

# 3. Instalar dependências do Laravel
composer install

# 4. Criar o arquivo de ambiente
cp .env.example .env

# 5. Configurar as credenciais do banco de dados no arquivo .env
DB_DATABASE=ecommercedb
DB_USERNAME=root
DB_PASSWORD=

# 6. Gerar a chave da aplicação
php artisan key:generate

# 7. Rodar as migrações
php artisan migrate

# 8. Iniciar o servidor local
php artisan serve
```

Após isso, acesse o projeto em:  
➡️ **http://127.0.0.1:8000**

---

### 🔐 Segurança Implementada

-   Senhas criptografadas (bcrypt);
-   Proteção contra **CSRF, XSS e SQL Injection**;
-   Sessões seguras com controle de acesso por **roles (usuário e administrador)**;
-   Validação de entrada de dados no servidor.

---

### 📊 Resultados e Conclusões

O sistema desenvolvido atendeu aos objetivos propostos, oferecendo:

-   Uma **plataforma funcional e intuitiva**;
-   **Gestão integrada** de produtos, pedidos e estoque;
-   **Segurança** nas transações e autenticação de usuários;
-   Estrutura **escalável e de fácil manutenção**.

---

### 🔮 Possíveis Melhorias Futuras

-   Integração com gateways de pagamento (PIX, Mercado Pago, PayPal);
-   Relatórios gerenciais e estatísticas de vendas;
-   Versão mobile (aplicativo);
-   Implementação de sistema de cupons e promoções.

---
