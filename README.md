# Time Tracker

A small employee time tracker: add employees, start and stop a timer with a task description, and view each employee's records and total time for a chosen period. Laravel, SQLite, Blade, Alpine.js and Pest; all data is fictional.

## Requirements

- Docker Desktop. The project directory must sit under a path Docker Desktop shares with containers (`/Users` by default) and must not be configured as a Synchronized File Share, because the SQLite file is live.
- Optional, only to run the tests on the host: PHP 8.5 with the `pdo_sqlite` extension, and Composer. The host PHP must not serve the app; only the container does.

## Run

```bash
docker compose up --build
```

Open http://localhost:8000.

The database and the application key are stored in the host folder `database/data` (`database.sqlite` and `app.key`), which is bind-mounted into the container at `/app/database/data`. They survive `docker compose down`, `docker compose down -v` and rebuilds, and the key is generated only once, so sessions survive restarts. Deleting the `database/data` folder deletes the data and the key. On Linux the files are owned by root, so removing or editing them needs `sudo`.

Limits: `php artisan serve` is demo-grade, not production hardening. The app has no authentication, so port 8000 is published only on the host's loopback address `127.0.0.1` and other machines cannot reach it. `docker compose exec` commands run without `APP_KEY`; `db:seed --force` and `pest` do not need it.

## Demo data

```bash
docker compose exec app php artisan db:seed --force
```

This adds a few fictional employees (Alex Demo, Jordan Sample, Taylor Example) with finished entries from the last week. Nothing is seeded automatically. The container runs with `APP_ENV=production`, so `--force` is needed to skip Laravel's confirmation prompt.

## Tests

On the host:

```bash
composer install
vendor/bin/pest
```

In the container:

```bash
docker compose exec app vendor/bin/pest
```

Tests run on an in-memory SQLite database with a fake clock (`freezeClock()`), and the test case refuses to boot against any other database, so they cannot touch the data folder.

## PHP version pin

The image is `php:8.5.12-cli-alpine`. 8.5.12 is the first 8.5 release with the fix for the "php2shell" remote code execution pair CVE-2026-109618 and CVE-2026-109619, disclosed 2026-10-21; the same fix is in 8.4.27, 8.3.36 and 8.2.35. These facts come from php.net as reported by a research step and were not independently re-fetched. The host PHP may be older than 8.5.12 and must only run tests.

## Report semantics

- All times are UTC, in storage and in display.
- `from` and `to` are inclusive dates: the period runs from `from` 00:00:00 up to, but not including, the day after `to`.
- An entry belongs to the day it started, and its full duration counts, even if it ends after the period.
- A running entry is counted up to the time the report is generated and is labelled as running.
- Stopping and starting a timer again creates a new entry; the totals sum both.

## Project structure

```
app/
  Domain/          Employees, Timers, Reports, Shared (models, actions, exceptions, report query)
  Http/            Controllers and Requests, grouped by Employees, Timers, Reports
  Providers/
  Support/Clock/   Clock interface and system clock
database/          migrations, factories, seeders; data/ holds the SQLite file and app key
docker/            entrypoint.sh
resources/views/
  layouts/         page layout
  employees/       employees page
  reports/         report page
  components/      ui, employees, timers, reports
routes/web.php
tests/
  Feature/         Employees, Timers, Reports, Persistence
  Unit/            Reports, Timers
  Support/         fake clock
```

## Alpine.js

Alpine.js is vendored at `public/vendor/alpinejs/alpine.min.js`, version 3.17.5, taken from npm `dist/cdn.min.js`. There is no CDN and no JavaScript build step.

## AI tools and models used

Built with Claude Code (Anthropic's CLI) as a multi-agent workflow: a read-only dispatcher plans and delegates, specialised subagents do the work, and no implementer verified its own output.

| Role | Agent | Model | What it did here |
|---|---|---|---|
| Dispatcher | main session | claude-sonnet-5-5 | triage, contract approval, delegation, README sections below |
| Researcher | qf-researcher | not recorded | PHP patch level, php2shell CVEs, Laravel 13 / Pest 5 versions |
| Explorer | qf-explorer | not recorded | host tooling probes (Docker, PHP, ports) |
| Architect | qf-architect | not recorded | contract, plan-review adjudication, bind-mount replan |
| Plan verifier | qf-plan-verifier | not recorded | reviewed the contract before any code existed |
| Implementer | qf-implementer | claude-opus-5-5 | scaffold, Docker, domain, HTTP/UI, tests, defect fixes |
| Mechanic | qf-mechanic | claude-sonnet-5-5 | composer script cleanup, compose delta, README |
| Verifier | qf-verifier | claude-opus-5-5 | two independent verification passes, including real Docker runs |

Models marked "not recorded" were pinned by the agent definitions and not reported back during the session.

## Example prompts

Prompts actually used, quoted as sent (typos in the user's own text are kept).

1. The original request:

```text
Create a simple web application for employees time tracking in a company. Use Lravel + SQLite + Blade (Alpine.js for the live timer) + Pest, with a small Docker setup so it runs in one command. Write clean code. Group compnents in directories/sbudirectories. Follow SOLID and DRY principles. Ensure using a php version that is patched agains the last php2shell vulnerabilities.
```

2. A change request after the contract was approved:

```text
need to make it to not wipe data when bringing down docker container
```

3. To the researcher, to resolve the unfamiliar name:

```text
Identify what that refers to. Search for a vulnerability/exploit/tool named "php2shell" or similar, and for the most recent PHP RCE/shell-injection class vulnerabilities (e.g. PHP-CGI argument injection CVE-2024-4577 and any newer 2025-2026 ones). If nothing matches the name "php2shell", say so explicitly and list the closest candidates; do not guess silently.
```

4. To a fresh architect for the persistence change:

```text
Dispatcher's proposal (decide, do not just accept): replace the named volume with a host bind mount `./database/data:/app/database/data` in compose.yaml, so the data lives in the project directory and no `docker compose` command deletes it.
```

5. To the verifier, so it could not lean on the implementers' claims:

```text
Form your own view from the contract and the actual files; you are given no implementer summary on purpose.
```

6. To a test-writing implementer, so defects are reported instead of hidden:

```text
If a test reveals a defect in production code (S1-S4 files), DO NOT fix it and do not weaken the test: report it as a defect with the failing assertion and `path:line` of the production cause; the dispatcher routes fixes.
```

## Errors found in the code and fixed

Only defects that a reviewer or verifier actually reported are listed. Final state: 57 Pest tests, 214 assertions, passing on the host and inside the container.

Found in the plan review, before code was written:

1. Running Pest inside the container would have emptied the real database. Laravel reads `$_SERVER` before `$_ENV`, and PHPUnit's `<env force>` only sets `$_ENV`, so a `DB_DATABASE` set by compose beat the forced `:memory:`; `RefreshDatabase` then ran `migrate:fresh` on the data file. Fixed with `<server>` entries in `phpunit.xml` plus a guard in `tests/TestCase.php::createApplication()` that refuses to run on a real database file.
2. The live timer would have failed silently. Alpine's build starts in a microtask, so `app.js` loaded after it misses `alpine:init`. Fixed by loading `app.js` before `alpine.min.js`, both `defer`.
3. A report with only `from` or `to`, or `to=9999-12-31`, would have been silently empty (dates are compared as text). Fixed with `required_with` and `before:9999-12-31` validation.
4. A `.env` copied into the image would make `artisan serve` drop `DB_DATABASE` for the child process and write to the image layer. Fixed with `serve --no-reload` and `.env` in `.dockerignore`.
5. A named Docker volume is deleted by `docker compose down -v`, which conflicted with the requirement that data must not be wiped. Replaced by the host bind mount `./database/data`.

Found during implementation and review:

6. The skeleton's `composer dev` script would start `artisan serve` on the host PHP, which is older than the security fix. Removed it with the npm steps of `setup`.

Found by the independent verifier and fixed:

7. `POST name[]=x` followed by `GET /` returned HTTP 500 (`trim()` received an array in `resources/views/components/employees/create-form.blade.php`). Fixed; regression test `tests/Feature/Employees/NameSentAsListTest.php`.
8. A `DB_URL` in the environment bypassed the in-memory test guard and tests wrote to that database. Fixed: `DB_URL` is forced empty in `phpunit.xml` and the guard refuses a non-empty sqlite `url`.
9. `docker compose down` and `restart` waited about 10 s because `artisan serve` ran as PID 1 and ignored SIGTERM. Fixed with `init: true`; `down` now takes under half a second.
10. The README's `php artisan db:seed` was cancelled in production mode without `--force`. Fixed.
11. Pest inside the container printed about 44 `file_get_contents(/app/.env)` warnings. Cause: phpdotenv's deliberately suppressed read of a missing `.env` is still printed by the Pest/Collision reporter. Fixed in `tests/Pest.php` by excluding only `vendor/vlucas/phpdotenv` from PHPUnit's source scope; the verifier confirmed real warnings and deprecations from application and test code are still reported.
12. The compose file published port 8000 on every host interface, and the app has no authentication. Now bound to `127.0.0.1:8000`.

Known gaps: the live ticking of the timer was checked by reading the code and rendered markup, not in a browser; file ownership of `database/data` on a Linux host was not checked (Docker Desktop on macOS was used).
