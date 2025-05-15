# Montink ERP

Sistema ERP desenvolvido em PHP com MySQL, utilizando arquitetura MVC, princípios SOLID e Clean Architecture.

## Pré-requisitos

- Docker (versão 20.10.0 ou superior)
- Docker Compose (versão 2.0.0 ou superior)
- Git
- PHP 8.1 ou superior (para desenvolvimento local)
- Composer (para desenvolvimento local)

## Como Rodar o Projeto

### 1. Clone o Repositório

```bash
git clone <repository-url>
cd montink
```

### 2. Configuração do Ambiente

1. Copie o arquivo de exemplo das variáveis de ambiente:

```bash
cp .env.example .env
```

2. Configure as variáveis no arquivo `.env`:

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=montink_erp
DB_USERNAME=montink_user
DB_PASSWORD=montink_password

MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS=noreply@montink.com
MAIL_FROM_NAME="Montink ERP"
```

### 3. Iniciar o Ambiente

Execute o script de inicialização:

```bash
./start.sh
```

Este script irá:

- Parar e remover containers existentes
- Limpar dados antigos do banco de dados
- Iniciar os containers
- Aguardar o MySQL estar pronto
- Mostrar o status dos containers

### 4. Acessar a Aplicação

- Frontend: http://localhost:8080
- MySQL: localhost:3306
  - Database: montink_erp
  - Username: montink_user
  - Password: montink_password

### 5. Comandos Úteis

Para ver os logs dos containers:

```bash
docker-compose logs -f
```

Para reiniciar o ambiente com banco limpo:

```bash
./start.sh
```

Para parar os containers:

```bash
docker-compose down
```

Para acessar o shell do container PHP:

```bash
docker-compose exec php bash
```

### 6. Desenvolvimento

Para instalar dependências PHP (caso esteja desenvolvendo localmente):

```bash
composer install
```

Para atualizar dependências:

```bash
composer update
```

## Estrutura do Projeto

```
.
├── app/
│   ├── Controllers/    # Controladores da aplicação
│   ├── Core/          # Classes core do sistema
│   ├── Models/        # Modelos de dados
│   ├── Services/      # Lógica de negócios
│   ├── Repositories/  # Acesso a dados
│   └── Interfaces/    # Contratos e interfaces
├── docker/
│   ├── mysql/         # Configuração do MySQL
│   ├── nginx/         # Configuração do Nginx
│   └── php/           # Configuração do PHP
├── public/
│   ├── css/          # Arquivos CSS
│   └── js/           # Arquivos JavaScript
└── views/            # Templates e views
```

## Recursos

- Gerenciamento de Produtos
- Gerenciamento de Pedidos
- Gerenciamento de Cupons
- Carrinho de Compras
- Validação de CEP
- Cálculo de Frete
- Notificações por Email

## Solução de Problemas

### Problemas Comuns

1. **Erro de conexão com o banco de dados**

   - Verifique se as variáveis no `.env` estão corretas
   - Confirme se o container MySQL está rodando
   - Execute `docker-compose logs mysql` para ver logs detalhados

2. **Erro 502 Bad Gateway**

   - Verifique se o container PHP está rodando
   - Execute `docker-compose logs php` para ver logs detalhados

3. **Permissões de arquivo**
   - Se encontrar problemas de permissão, execute:
   ```bash
   chmod +x start.sh
   ```

### Logs e Debug

Para ver logs específicos de um serviço:

```bash
docker-compose logs -f [serviço]
# Exemplo: docker-compose logs -f php
```

## Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.
