# TabWiter - Ecossistema Dev 🚀

**TabWiter** é uma plataforma moderna para desenvolvedores, unindo o poder do backend **Yii2** com a agilidade e estética de um frontend baseado em **React** e **Tailwind CSS**. 

Inspirado no TabNews e no ecossistema do Twitter, o projeto oferece uma interface premium para troca de conhecimento técnico e visualização de conteúdo híbrido (local e externo).

---

## 🛠️ Stack Tecnológica

- **Backend:** [Yii Framework 2.0](https://www.yiiframework.com/) (PHP 8.3+)
- **Frontend:** [React 18](https://react.dev/) + [Babel Standalone](https://babeljs.io/)
- **Estilização:** [Tailwind CSS 3.4](https://tailwindcss.com/)
- **Ícones:** [Lucide React](https://lucide.dev/)
- **Banco de Dados:** SQLite (Leve e Eficiente)

---

## 📂 Estrutura de Diretórios 

- `assets/`: Definições de bundles de assets do Yii2.
- `config/`: Configurações da aplicação (DB, Web, Console).
- `controllers/`: Lógica de roteamento e processamento (Post, Auth, etc).
- `data/`: Armazenamento do banco de dados SQLite.
- `models/`: Modelos de Active Record (User, Post).
- `runtime/`: Arquivos temporários gerados pelo Yii (logs, cache).
- `views/`: Templates PHP que injetam os componentes React.
- `web/`: Entry point público e recursos estáticos (CSS/JS).

---

## 🚀 Instalação e Configuração

### 1. Requisitos
- PHP 8.1 ou superior.
- Composer.
- SQLite habilitado no PHP.

### 2. Preparação do Ambiente
Após clonar o repositório, instale as dependências:
```bash
composer install
```

### 3. Banco de Dados e Permissões
É fundamental que os diretórios de escrita existam e tenham as permissões corretas para o SQLite e logs.

```bash
# Criar diretório do banco se não existir
mkdir -p data

# Corrigir permissões (Linux/macOS)
chmod -R 775 data runtime
```

### 4. Migrações
Inicialize a estrutura do banco de dados:
```bash
php yii migrate --interactive=0
```

---

## 🏃 Executando o Projeto

Inicie o servidor de desenvolvimento do Yii:
```bash
php yii serve --port=8080
```
Acesse: [http://localhost:8080](http://localhost:8080)

---

## 🔍 Solução de Problemas Comuns

### Erro: `SQLSTATE[HY000] [14] unable to open database file`
Este erro ocorre quando o processo do servidor PHP não consegue escrever no diretório `data/` ou no arquivo `tabwiter.db`.
- **Causa:** Diretório `data/` inexistente ou falta de permissões de escrita.
- **Solução:** Execute `mkdir -p data` e `chmod -R 775 data runtime`. Certifique-se de que o usuário que roda o servidor tem permissão de escrita.

---

## 🎨 Novo Design (Release v1.1)
O design foi atualizado para uma experiência SPA (Single Page Application) dentro do Yii2, trazendo:
- **Feed Híbrido:** Integração suave entre posts locais e conteúdos do ecossistema dev.
- **Sistema de Votação (Tabcoins):** Interface inspirada no TabNews.
- **Design System:** Paleta de cores customizada, animações de entrada e tipografia moderna (Inter/JetBrains Mono).
- **Responsividade Total:** Sidebar retrátil e layout otimizado para mobile.

---

&copy; <?= date('Y') ?> TabWiter - Criado para a comunidade DEV.
