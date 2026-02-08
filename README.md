# OreWire

Mining news and drill results ingestion site. Ingests normalized mining articles from the north-cloud pipeline via HTTP API and displays them with commodity, company, and jurisdiction filters.

**Domain**: [orewire.ca](https://orewire.ca)

## Development

### Local (DDEV)

```bash
ddev start
ddev artisan migrate
ddev artisan db:seed
```

### Commands

- `ddev artisan migrate` - Run migrations
- `ddev artisan db:seed` - Seed categories, commodities, jurisdictions
- `ddev artisan test --compact` - Run tests
- `ddev artisan wayfinder:generate` - Regenerate route types after route changes

## Ingestion API

Articles are ingested via HTTP POST from north-cloud. Configure `OREWIRE_INGEST_TOKEN` in `.env`.

### Endpoint

```
POST /api/ingest/mining-article
Authorization: Bearer {OREWIRE_INGEST_TOKEN}
Content-Type: application/json
```

### Payload (minimal)

```json
{
  "id": "unique-external-id",
  "title": "Article Title",
  "body": "Full content...",
  "intro": "Excerpt...",
  "canonical_url": "https://example.com/article",
  "source": "https://example.com",
  "published_date": "2025-02-04T10:00:00Z",
  "publisher": {
    "route_id": "uuid",
    "published_at": "2025-02-04T12:00:00Z",
    "channel": "articles:mining"
  },
  "commodities": ["gold", "silver"],
  "companies": ["Company X Inc"],
  "jurisdictions": ["Canada", "Ontario"],
  "categories": ["exploration", "drill-results"],
  "drill_results": [
    {
      "hole_id": "DDH-001",
      "commodity": "gold",
      "intercept_m": 5.2,
      "grade": 12.5,
      "unit": "g/t"
    }
  ]
}
```

Only `id` and `title` are required. Re-ingestion with the same `(news_source_id, external_id)` updates the article.

### Test ingest

```bash
curl -X POST https://orewire.ca/api/ingest/mining-article \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"id":"test-1","title":"Test Article","body":"Content","source":"https://miningnews.com","published_date":"2025-02-04T10:00:00Z"}'
```

## API Routes

| Method | Route | Auth |
|--------|-------|------|
| POST | /api/ingest/mining-article | Bearer token |
| GET | /api/articles | Public |
| GET | /api/articles/{id} | Public |
| GET | /api/commodities | Public |
| GET | /api/companies | Public |
| GET | /api/categories | Public |

## Deploy

Deploy via GitHub Actions (triggers after tests pass on main) or manually:

```bash
dep deploy orewire.ca
```

### Server setup

1. Create `deployer` user with SSH key access
2. Clone to `~/orewire-laravel`
3. Configure Caddy: copy `Caddyfile` to `/etc/caddy/Caddyfile` and reload Caddy. Adjust `php_fastcgi` socket if needed (e.g. `php8.4-fpm.sock`)
4. Install systemd user services (prefixed with `orewire-` to avoid conflicts with other projects sharing `~/.config/systemd/user/`):

```bash
cp deploy/systemd-user/orewire-*.service ~/.config/systemd/user/
sudo loginctl enable-linger deployer
systemctl --user daemon-reload
systemctl --user enable orewire-inertia-ssr orewire-horizon orewire-mining-consumer orewire-schedule-work
systemctl --user start orewire-inertia-ssr orewire-horizon orewire-mining-consumer orewire-schedule-work
```

5. Set `OREWIRE_INGEST_TOKEN` and other env vars in production `.env`

Queue processing uses Laravel Horizon (Redis). Horizon dashboard is at `/horizon` (authenticated users only; configure in `HorizonServiceProvider::gate()`).

### Auth note

The single shared `OREWIRE_INGEST_TOKEN` is suitable for MVP. For production at scale, consider per-source tokens or Laravel Sanctum API tokens.
