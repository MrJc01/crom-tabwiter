# TabWiter — Ecossistema Dev Efêmero 🚀

Plataforma moderna para desenvolvedores, unindo **Yii2** (backend) com **Alpine.js** + **Tailwind CSS** (frontend). Identidade via Hash e validação pela Bio do TabNews.

## Stack
- **Backend:** Yii 2.0 · PHP 8.1+ · SQLite
- **Frontend:** Alpine.js 3 · Tailwind CSS 3.4
- **Ícones:** Inline SVG · **Fontes:** Inter · JetBrains Mono

## Setup Rápido

```bash
# 1. Dependências
composer install

# 2. Banco de dados
mkdir -p data
chmod -R 775 data runtime
php yii migrate --interactive=0

# 3. Iniciar servidor
php yii serve --port=8080
```

Acesse: [http://localhost:8080](http://localhost:8080)

## Regras de Negócio
| Regra | Detalhe |
|-------|---------|
| **Acesso** | Imediato via Hash (guest auto-criado) |
| **Validação** | Bio do TabNews contém o hash |
| **Decay** | Posts perdem 1 ponto/dia |
| **Morte** | Post deletado ao atingir -10 pontos |
| **Mana** | Semanal, baseada no saldo TabCoins |
| **Self-vote** | Bloqueado |
| **Char Limit** | 500 por post |

## Configuração do Cron (Reaper)

As tarefas automáticas de manutenção devem ser agendadas via cron:

```cron
# Decay diário (3h da manhã)
0 3 * * * cd /path/to/tabwiter && php yii reaper/decay

# Purge de guests inativos (a cada 6h)
0 */6 * * * cd /path/to/tabwiter && php yii reaper/purge-inactives

# Sync de mana semanal (Segunda 00:00)
0 0 * * 1 cd /path/to/tabwiter && php yii reaper/sync-mana
```

Os logs de execução ficam em `runtime/reaper.log`.

## Estrutura de Diretórios
```
controllers/     PostController, AuthController, SiteController
models/          User, Post, PostTag, Vote
commands/        ReaperController (decay, purge, mana sync)
views/           Alpine.js templates (3-column layout)
web/js/          tracker.js (interest tracking local-first)
data/            SQLite database
config/          web.php, db.php, params.php
migrations/      3 migrations (user, post, tags+votes)
```

## Dark Mode
Ativado automaticamente pela preferência do sistema (`prefers-color-scheme: dark`).

## Solução de Problemas

| Erro | Causa/Solução |
|------|---------------|
| `unable to open database` | `mkdir -p data && chmod 775 data` |
| Mana insuficiente | Aguarde reset semanal ou valide conta TabNews |
| CSRF mismatch em AJAX | Endpoints API têm CSRF desabilitado |

---

&copy; TabWiter — Código efêmero para a comunidade DEV.
