# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-02-16

### Added

- Initial Laravel application for OreWire mining news aggregation
- HTTP article ingestion via northcloud-laravel package integration
- North Cloud Redis configuration for mining article pub/sub consumption
- Mining article consumer command with deduplication by normalized title
- Subscription to all Layer 5 mining channels
- Live metal prices widget via Metals.Dev API (Gold, Silver, Copper)
- Admin user management command
- Ore-themed frontend design system
- Laravel Horizon integration for queue management
- Systemd user services for SSR and queue workers
- Mining article ingestion statistics command
- Deployment pipeline via PHP Deployer with Horizon and consumer restarts

### Changed

- Rebranded from Drillfeed to OreWire across project configuration and references
- Adopted northcloud-laravel admin module for article management
- Switched to northcloud-laravel composables and auto-registered Redis connection
- Aligned Caddy configuration with standard pattern (TLS, encode, static handles, log, handle_errors)
- Refactored Caddyfile for improved asset handling

### Fixed

- Article deduplication logic in MiningArticleProcessor
- Keyword matching using REGEXP word boundaries for mining classification
- Non-mining article detection using keyword matching instead of relation check
- Redis prefix disabled on northcloud connection for pub/sub compatibility
- SSR port conflict resolved with dedicated port (13715)
- Dashboard rendering fixed to use page component instead of missing route redirect
- Inertia SSR configuration to use environment variable for SSR URL
- Deploy compatibility with SSH alias for repository URL
- PHP-FPM restart after deploy to clear realpath cache
