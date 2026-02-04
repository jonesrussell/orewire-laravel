# Drillfeed

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

Articles are ingested via HTTP POST from north-cloud. Configure `DRILLFEED_INGEST_TOKEN` in `.env`.

### Endpoint

```
POST /api/ingest/mining-article
Authorization: Bearer {DRILLFEED_INGEST_TOKEN}
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
3. Configure web server (nginx/Caddy) for `orewire.ca`
4. Install systemd user service for Inertia SSR:

```bash
cp deploy/systemd-user/inertia-ssr.service ~/.config/systemd/user/
systemctl --user daemon-reload
systemctl --user enable inertia-ssr
systemctl --user start inertia-ssr
```

5. Set `DRILLFEED_INGEST_TOKEN` and other env vars in production `.env`

### Auth note

The single shared `DRILLFEED_INGEST_TOKEN` is suitable for MVP. For production at scale, consider per-source tokens or Laravel Sanctum API tokens.
