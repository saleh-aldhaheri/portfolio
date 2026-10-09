# Portfolio — Saleh Ahmed

An interactive terminal-style portfolio where visitors can explore Saleh's projects, skills, and experience, and chat with an AI assistant that knows him well.

## Features

- **Terminal-style UI** — visitors navigate commands like `about`, `projects`, `contact`, `social`, `theme set <name>`, and `help`.
- **AI assistant chat** — a Livewire chat window answers questions about Saleh using a knowledge base, with streaming-style replies pushed live over WebSockets (Laravel Reverb + queues).
- **Persistent conversations** — visitors are tracked by cookie, and chat history is per-visitor.
- **Contact form** — messages are stored and viewable in the admin panel.
- **Admin panel (Filament)** — manage contact messages, conversations, and visitors, and view activity analytics.
- **Analytics** — tracks visitor activity across the site.
- **Docker deployment** — app, MySQL, Redis, and Reverb orchestrated with Docker Compose.

## Tech stack

- PHP 8.3+ / Laravel (Skeleton)
- Livewire 4 — reactive components and real-time chat
- Filament 5 — admin panel
- Laravel AI — LLM chat (Groq by default; Anthropic, OpenAI, Ollama, etc. supported)
- Laravel Reverb + Redis — WebSocket broadcasting and queues
- Tailwind CSS 4 (via Vite)
- MySQL, Pest for testing
- Docker Compose for local and production

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+ and npm
- Docker + Docker Compose (for the Docker setup)
- A Groq API key (or another provider key supported by `config/ai.php`)

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure the database and the AI provider in `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=portfolio
DB_USERNAME=root
DB_PASSWORD=

GROQ_API_KEY=your-groq-key
```

For real-time chat, point the queues at Redis and set broadcasting to Reverb:

```
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1

REVERB_APP_ID=local
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

Run migrations and seed an admin user (email `salehaldhaheri09@gmail.com`, from `DatabaseSeeder`):

```bash
php artisan migrate --seed
```

Start Vite, the Reverb server, and the queue worker:

```bash
npm run dev
php artisan reverb:start
php artisan queue:work
```

The site is available at `http://localhost:8000` (`php artisan serve`) and the admin panel at `/admin`.

> The `.env` file references `http://127.0.0.1` fine for local dev. For a host-based setup, use `VITE_REVERB_HOST` and `VITE_REVERB_PORT` to tell the browser where to reach Reverb (these are baked into the frontend build).

## Testing

```bash
php artisan test --compact
```

Unit and feature tests cover the AI knowledge base, message flows, analytics, and services. Format code with:

```bash
vendor/bin/pint --dirty
```

Static analysis:

```bash
vendor/bin/phpstan analyse
```

## Docker

A Compose setup builds the app image and runs MySQL, Redis, and Reverb alongside it:

```bash
docker compose build
docker compose up -d
```

The `migrate` service runs migrations and seeds the database on first start. The app container is exposed on `${APP_HTTP_PORT:-80}`. A production override (`docker-compose.prod.yml`) readies the stack for deployment.

## Project structure

```
app/
  Ai/                 Chat agent and knowledge-base tool
  Actions/            Message-send and AI-reply orchestration
  Events/             Reverb broadcasts (e.g. MessageSentEvent)
  Filament/           Admin panel resources
  Jobs/               Queued background work (SendAiResponseJob)
  Livewire/
    Pages/Home.php        Terminal home page
    Components/           Terminal command components + Chat
  Services/           Analytics, chat context, visitors, contacts
resources/
  data/               knowledge.php (AI knowledge base), data.php (terminal content)
  views/              Blade templates (Livewire components, layouts)
docker/               Nginx config + Dockerfiles
tests/                Pest suite (feature + unit)
```

## How the chat works

1. A visitor opens the chat and sends a message.
2. `Chat::sendMessage()` stores it and dispatches `SendAiResponseJob` over the queue.
3. The job calls the LLM (via `AiChatContextManagerService` + `KnowledgeBaseTool`) and saves the assistant reply.
4. `MessageSentEvent` broadcasts it on the per-visitor `visitor.{uuid}` channel.
5. Livewire receives the broadcast and appends the reply with a typewriter effect. A short polling fallback shows the reply even if the broadcast is missed.