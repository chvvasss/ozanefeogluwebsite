# ozanefeoglu.com — developer makefile
# Faz 1'den itibaren bu komutlar gerçek dependencies'le çalışır.
# Şu an Faz 0 (Discovery) — `make` komutları placeholder verir.

SHELL := /bin/bash
.DEFAULT_GOAL := help

# ---------------------------------------------------------------- help

.PHONY: help
help: ## Bu yardım mesajı
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# ---------------------------------------------------------------- setup

.PHONY: setup
setup: ## İlk kurulum: composer + npm install + .env + key + migrate + seed
	@if [ ! -f .env ]; then cp .env.example .env; fi
	composer install
	npm ci
	php artisan key:generate
	php artisan migrate --seed
	php artisan storage:link
	@echo ""
	@echo "✓ Kurulum tamamlandı. 'make dev' ile başlayabilirsin."

.PHONY: setup-prod
setup-prod: ## Production install: --no-dev + optimize
	composer install --no-dev --optimize-autoloader
	npm ci
	npm run build
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	php artisan event:cache

# ---------------------------------------------------------------- dev

.PHONY: dev
dev: ## Dev server: php + vite + queue worker + horizon (sadece varsa)
	@echo "Vite, php-server ve queue worker'ı paralel çalıştırıyorum..."
	@trap 'kill 0' INT; \
		php artisan serve --host=0.0.0.0 --port=8000 & \
		npm run dev & \
		php artisan queue:work --tries=3 --backoff=10 & \
		wait

.PHONY: tinker
tinker: ## REPL
	php artisan tinker

.PHONY: fresh
fresh: ## DB drop + migrate + seed (yıkıcı, sadece dev)
	php artisan migrate:fresh --seed

# ---------------------------------------------------------------- quality gates

.PHONY: test
test: ## Tüm testleri çalıştır (pest + phpstan + linter)
	$(MAKE) lint
	$(MAKE) stan
	$(MAKE) pest

.PHONY: pest
pest: ## Pest test runner
	./vendor/bin/pest --colors=always --parallel

.PHONY: pest-coverage
pest-coverage: ## Coverage raporu (xdebug gerek)
	./vendor/bin/pest --coverage --min=70

.PHONY: stan
stan: ## PHPStan / larastan (level 8)
	./vendor/bin/phpstan analyse --memory-limit=2G

.PHONY: lint
lint: ## Tüm linter'lar (php-cs-fixer + eslint + stylelint)
	./vendor/bin/pint --test
	npm run lint:js
	npm run lint:css

.PHONY: lint-fix
lint-fix: ## Linter auto-fix
	./vendor/bin/pint
	npm run lint:js -- --fix
	npm run lint:css -- --fix

# ---------------------------------------------------------------- security

.PHONY: audit
audit: ## composer + npm + secrets scan
	composer audit --abandoned=report
	npm audit --omit=dev
	@command -v gitleaks >/dev/null 2>&1 && gitleaks detect --no-banner --no-git || echo "(gitleaks not installed; skip)"

.PHONY: zap
zap: ## OWASP ZAP baseline scan (dockerized) — Faz 7'de aktif
	docker run --rm -t --network=host owasp/zap2docker-stable zap-baseline.py -t $(APP_URL) || true

# ---------------------------------------------------------------- build

.PHONY: build
build: ## Production assets
	npm run build

.PHONY: clean
clean: ## Cache temizle
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	rm -rf public/build node_modules/.vite

# ---------------------------------------------------------------- ops

.PHONY: backup
backup: ## Spatie backup
	php artisan backup:run

.PHONY: backup-list
backup-list: ## Yedek listesi
	php artisan backup:list

.PHONY: queue-restart
queue-restart: ## Queue worker'lara graceful restart sinyali
	php artisan queue:restart

.PHONY: schedule-run
schedule-run: ## Scheduler'ı bir kez çalıştır (cron alternatifi dev)
	php artisan schedule:run

.PHONY: optimize
optimize: ## Production cache (config + route + view + event)
	php artisan optimize

# ---------------------------------------------------------------- docker

.PHONY: docker-up
docker-up: ## docker compose up -d (dev servisler: mysql, redis, meili, mailpit)
	docker compose up -d
	@echo "Servisler ayağa kalktı. mailpit: http://localhost:8025"

.PHONY: docker-down
docker-down: ## docker compose down
	docker compose down

.PHONY: docker-logs
docker-logs: ## docker compose logs -f
	docker compose logs -f

# ---------------------------------------------------------------- search

.PHONY: search-reindex
search-reindex: ## Meilisearch (veya MySQL FT) re-index
	php artisan search:reindex

# ---------------------------------------------------------------- ci helpers

.PHONY: ci
ci: ## CI pipeline (lokal simulasyon)
	composer install --prefer-dist --no-interaction
	npm ci
	cp .env.example .env
	php artisan key:generate
	php artisan migrate
	$(MAKE) lint
	$(MAKE) stan
	$(MAKE) pest
	$(MAKE) audit
