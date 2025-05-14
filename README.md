# Montink ERP

Sistema ERP desenvolvido em PHP com MySQL, utilizando arquitetura MVC.

## Requisitos

- Docker
- Docker Compose

## Configuração do Ambiente

1. Clone o repositório:

```bash
git clone <repository-url>
cd montink
```

2. Configure as variáveis de ambiente:

```bash
cp .env.example .env
```

Edite o arquivo `.env` com suas configurações.

3. Inicie o ambiente de desenvolvimento:

```bash
./start.sh
```

O script `start.sh` irá:

- Parar e remover containers existentes
- Limpar dados antigos do banco de dados
- Iniciar os containers
- Aguardar o MySQL estar pronto
- Mostrar o status dos containers

## Acessando a Aplicação

- Frontend: http://localhost:8080
- MySQL: localhost:3306
  - Database: montink_erp
  - Username: montink_user
  - Password: montink_password

## Estrutura do Projeto

```
.
├── app/
│   ├── Controllers/
│   ├── Core/
│   └── Models/
├── docker/
│   ├── mysql/
│   ├── nginx/
│   └── php/
├── public/
│   ├── css/
│   └── js/
└── views/
```

## Recursos

- Gerenciamento de Produtos
- Gerenciamento de Pedidos
- Gerenciamento de Cupons
- Carrinho de Compras
- Validação de CEP
- Cálculo de Frete
- Notificações por Email

## Desenvolvimento

Para reiniciar o ambiente com um banco de dados limpo:

```bash
./start.sh
```

Para ver os logs dos containers:

```bash
docker-compose logs -f
```
