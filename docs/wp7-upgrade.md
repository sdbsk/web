# WordPress 7 upgrade

**Branch:** `hotfix/wp-6.8.6-cve-2026-60137` (7.0.3, merged to `main` as
[PR #20](https://github.com/sdbsk/web/pull/20) and deployed), then
`fix/wp7-editor-canvas-and-patterns` (editor fixes + 7.1.2).
**Last worked on:** 2026-10-01

## Follow-up: editor fixes and WordPress 7.1.2 (2026-10-01)

Editors reported two regressions after the 7.0.3 deploy (kanban issue #471):

- **Canvas lost its margins.** The canvas width comes from `wp-template-page` /
  `wp-template-narrow` classes that `append-template-class-to-post-title-and-post-content.js`
  adds to the post content. It looked them up in the admin `document`, but the canvas
  is an iframe (`iframe[name="editor-canvas"]`), so the class never landed. It only
  ever worked because v1 blocks kept the editor un-iframed. Fixed by querying the
  iframe's document.
- **List View flattened, accordion items could not be duplicated.** Since 6.9 a block
  with `metadata.patternName` (core adds it to anything inserted from a pattern) is a
  contentOnly section. Turned off with `disableContentOnlyForUnsyncedPatterns` in a
  `block_editor_settings_all` filter (`functions.php`). The many nested "Skupina"
  groups that now show are the real structure of `patterns/accordion.php`.

Then 7.0.3 → **7.1.2**, firebox 3.1.13, mainwp-child 6.2.1, w3-total-cache 2.10.6,
Symfony 7.4.x patches and, as dependencies, doctrine/dbal 4.5.0 and doctrine/orm 3.7.2.
7.1 has no DB schema bump (`db_version` stays 61833).

Verified with a snapshot harness (headless Chrome over CDP, scripts in the session
scratchpad, not in the repo): 327 front-end URLs (all pages, campaigns, newsletters,
60 latest posts, categories, search, 404, feed), 23 admin screens, 66 posts/pages in
the block editor (every page with a pattern or a `saleziani/*` block). Two runs on
7.0.3 established the noise floor: structural checks were identical between them.
Against 7.1.2:

- Front end: same HTTP status, title, script count and console output on all 327
  URLs. HTML differs only in core CSS (`:root` presets, button and post-template grid
  rules) and FireBox splitting `animate.min.css` into per-animation files. Screenshot
  differences were the newsletter popup / cookie dialog caught mid-fade; after a 6 s
  settle the pages match.
- Editor: no invalid blocks, no `core/missing`, template class present on every post,
  accordion items duplicable. The 15 posts with v1 blocks (`darujme-form`,
  `newsletter-form`), not iframed on 7.0, are iframed on 7.1 and render correctly. The
  36px-size deprecation warnings are gone (7.1 forces 40px). New warning on every post:
  `global-styles-css-custom-properties-inline-css was added to the iframe incorrectly`,
  emitted by core itself; FireBox's `fpframework-admin-css` gives the same warning.
- Admin: all screens load, no new notices. The admin toolbar now stays visible in the
  editor, and the media grid scrolls infinitely (both 7.1 changes).
- REST media upload as a logged-in user works (sizes generated), and saving a page
  round-trips its content unchanged.
- `/a/update_campaigns` still returns the same pre-existing 500 (no Darujme credentials
  locally).
- `doctrine:schema:validate` now runs on DBAL 4.5 (known issue 6 below is resolved) and
  reports that `app_darujme_campaign.created_at` / `updated_at` still carry DBAL 3's
  `(DC2Type:datetime_immutable)` column comment. Harmless; a migration dropping the
  comment would silence it.

Local server is now the Symfony CLI (`symfony server:start --port=8765 --no-tls -d`
with `WP_HOME`/`WP_SITEURL` exported, see below). It does not resolve directory
indexes, so `/wp/wp-admin/` redirects to the homepage; use `/wp/wp-admin/index.php`.

## Goal

Update WordPress and plugins, verified against a complete local copy of production
before deploying. The branch originally carried only the WP 6.8.6 CVE-2026-60137
bump; WP 7.0.3 supersedes it, so that fix is absorbed here.

## What changed

| Package | Production today | This branch |
|---|---|---|
| roots/wordpress | 6.8.3 | **7.0.3** |
| wpackagist-plugin/firebox | 3.1.1-free (via `dev-trunk`) | **3.1.9** (pinned `^3.1`) |
| wpackagist-plugin/w3-total-cache | 2.8.14 | 2.10.4 |
| wpackagist-theme/twentytwentyfive | 1.3 | 1.5 |
| symfony/* | 7.3.x | 7.4.x |
| doctrine/dbal | 3.10.3 | **4.4.4** (major) |
| doctrine/orm | 3.5.3 | 3.6.8 |
| roots/bedrock-autoloader | 1.0.4 | 1.1.0 |
| roots/wp-password-bcrypt | 1.2.0 | **removed** |
| deployer/deployer | 7.5.12 | unchanged (8.0.5 is a major; separate task) |

`firebox` was moved off `dev-trunk` on purpose. wpackagist serves `dev-trunk` as a
rolling snapshot, so `composer install` from an unchanged `composer.lock` produced
3.1.9 locally while production had been built with 3.1.1 — every deploy was silently
shipping whatever trunk happened to be. Pinning also picks up the 3.1.8 fix for
*unauthenticated access to the form submissions CSV export endpoint*, and the WP 7
compatibility added in 3.1.6/3.1.7 (3.1.1 predates WP 7 entirely).

`roots/wp-password-bcrypt` was removed. It is abandoned, 1.3.0 declares a `conflict`
with WordPress ≥ 6.8 because core hashes with bcrypt natively, and on WP 7 it was
still shadowing core's `wp_hash_password()` / `wp_check_password()` through
`vendor/autoload.php`. Verified after removal: both functions resolve to
`wp-includes/pluggable.php`, `$wp_hasher` is empty, all 10 users in the production
DB have plain `$2y$` hashes which core's `password_verify()` branch accepts, and core
still covers the `md5`, `$P$` phpass and `$wp$2y$` forms
(`web/wp/wp-includes/pluggable.php:2839`). New hashes are written as `$wp$2y$`.
Logging in through the browser was confirmed to work.

## Code fixes included

### `blocks.js` enqueued on the front end

`blocks.js` was enqueued on `enqueue_block_assets`, which fires on the front end as
well as in the editor, while declaring `wp-edit-post` as a dependency.
`wp-edit-post` depends on `postbox`, which core registers on admin screens only
(`web/wp/wp-includes/script-loader.php:1439`, inside the `is_admin()` branch; the
dependency is added at line 304).

WordPress 6.9.1 started raising a `_doing_it_wrong` notice for unregistered
dependencies. With `WP_ENV=development` Symfony's debug error handler turns that
notice into a thrown exception in the middle of `wp_print_footer_scripts`, so **every
footer script vanished from the page and the response became a 500**. With
`WP_ENV=production` the notice is not thrown, so production would have rendered
fine; the bug was latent there.

The fix moves the script (not the stylesheet) to `enqueue_block_editor_assets`. All
nine block scripts under `assets/blocks/scripts/blocks/` are pure `registerBlockType`
editor registrations, so the front end never needed the JS. Side benefit: the
homepage dropped from 119 KB to 99 KB of HTML in production mode.

### `admin.js` enqueued twice, and outside the editor

Found during the browser pass. `admin.js` was enqueued **twice**: once on
`admin_enqueue_scripts` for every admin screen with no dependencies at all, and once
on `enqueue_block_editor_assets` with proper ones. All four of its modules target the
block editor, so the first enqueue only ran the file where the pieces it needs are
absent. Two exceptions fired on the dashboard, the plugins screen and the FireBox
admin page:

- `append-template-class-to-post-title-and-post-content.js` guarded `wp.data` but not
  the store, and `wp.data.select('core/editor')` is `undefined` outside the post
  editor. Because the call sits inside `wp.data.subscribe`, it threw on *every* store
  change, and a throwing subscriber can stop the subscribers registered after it.
- `blockembed.js` called `.forEach` on `getBlockVariations('core/embed')`, which
  returns `undefined` where `core/embed` is not registered.

The double enqueue also made the editor run the file twice, so `registerPlugin`
produced `Plugin "domicil" is already registered.`

The fix drops the `admin_enqueue_scripts` enqueue (nothing depended on the `admin`
handle, and there is no `admin.css`) and adds the two guards, which are still needed
because the site editor registers no `core/editor` store.

## Verified locally

Against a full production copy (DB + 11 GB uploads + 1.2 GB images):

- WP 7.0.3, DB upgraded 60421 → 61833
- Homepage, article pages, `wp-login.php` → 200; unknown URL → 404
- Uploaded media serves correctly
- `bin/console` boots on Symfony 7.4.15 + DBAL 4
- `doctrine:migrations:status` → 4/4 executed, 0 new
- `doctrine:migrations:migrate -n --dry-run` → OK (this is what `deploy.php`'s `migrate:db` runs)
- w3-total-cache 2.10.4 activated under WP 7 → homepage 200, no fatals (then deactivated again locally)
- No `_doing_it_wrong` notices, no fatals, `web/app/debug.log` never created

### Browser pass (19/19)

Driven by a headless-Chrome CDP harness rather than by hand, so it can be re-run.
The scripts are **not** in the repo — they live in the session scratchpad
(`cdp.mjs` + `test-wp7.mjs`, zero-dependency, Node 22's built-in `WebSocket`). Worth
re-creating or keeping somewhere if this needs repeating.

- Front end: `blocks.js` absent, `public.js` / `consent.js` / `firebox.js` present,
  `blocks.css` / `public.css` still present
- Login as an administrator succeeds against a core-written `$wp$2y$` hash
- Dashboard renders (footer reports "Verzia 7.0.3"), no error notices
- Block editor boots, `blocks.js` loads, 10 `saleziani/*` block types register
- Post 27918: 18 blocks, none invalid, no `core/missing`
- All 10 `saleziani/*` blocks instantiate via `createBlock` and render with no
  validation errors (84 blocks on the canvas)
- The custom `DOMICIL` sidebar field renders, and registers only once
- Site editor loads
- FireBox 3.1.9 admin loads with no fatal, "Newsletter formulár" campaign listed
- Plugins screen loads
- **Zero** console errors, zero uncaught exceptions, zero failed requests

`saleziani/posts` and `saleziani/post-columns` are `core/query` **variations**
selected through a `namespace` attribute (see the `render_block_core/query` filter in
`functions.php`), not block types — which is why only 10 types register.

## Known issues, none blocking

1. **Three `saleziani/*` blocks are still Block API version 1** — `darujme-form`,
   `link-to-page`, `newsletter-form`. WP 6.9 deprecated API ≤ 2, and the warning says
   the post editor may fall back to a non-iframe editor while all editors are planned
   to become iframes. Pre-existing; needs doing before it becomes forced.
2. **`wp.editPost.PluginPostStatusInfo` is deprecated since 6.6** — use
   `wp.editor.PluginPostStatusInfo`. Used by `domicil.jsx` and `page-perex.jsx`.
3. **`36px default size` deprecations** for `wp.components.TextControl` and
   `SelectControl`, *to be removed in 7.1* — so this one has a real deadline. Set
   `__next40pxDefaultSize`.
4. **Two DB `wp_template` records reference blocks that no longer exist in the
   codebase** — `page-podporte-nas` (12084) and `page-sme-tu-100-rokov` (12089) both
   contain self-closing `saleziani/top-level-page-title`,
   `saleziani/top-level-page-perex` and `saleziani/navigation`. Those blocks were
   added in `fcc6010` and removed long before this branch, and both templates belong
   to the active `saleziani` theme, so they render nothing and show as unknown blocks
   in the site editor. Pre-existing, unrelated to WP 7 — the homepage (page 5 uses the
   second template) renders its title and navigation from `core/*` blocks and is fine.
5. **DBAL 4 logs `Support for MySQL < 8 is deprecated and will be removed in DBAL 5`**
   on every request, because production runs MariaDB 10.5. Informational; matters only
   when DBAL 5 comes up.
6. **`doctrine:schema:validate` fails** on the *Database* section:
   `GenerateSchemaEventArgs::setSchema()` wants the DBAL `Schema::edit()` API from
   `doctrine/dbal ^4.5`, which does not exist yet (4.4.4 is the newest release). An
   upstream `doctrine-bundle` 2.19.0 feature gate, not our bug. Mapping validation
   passes and migrations work. Pinning `doctrine-bundle` to 2.18.x would avoid it.
7. **One unexplained password event.** The first `wp user update --user_pass=…` after
   removing `wp-password-bcrypt` wrote a `$wp$2y$` hash that then failed
   `wp_check_password` and login. The identical command run again produced a hash that
   verifies, and `wp_set_password` → read back from the DB → `wp_check_password` round
   trips cleanly with and without plugins loaded. Not reproducible and not a
   systematic fault in the WP 7 auth path, but recorded rather than hand-waved.
8. **Still not covered by local testing:**
   - `/a/update_campaigns` returns 500 with `Undefined array key "response"` because
     the Darujme API credentials are commented out in `.env.local`. Not a WP 7 or DBAL
     regression, but the Symfony form/Darujme paths are therefore untested.
   - w3-total-cache was tested with default (disk) settings. Production runs a
     Redis-backed configuration, which we deliberately did not pull
     (`shared/web/app/w3tc-config`).
9. **Production Redis is down.** `wp-cli` on production emitted hundreds of
   `W3TC\Cache_Redis::_get_accessor: Connection refused`. Unrelated to this upgrade,
   but worth chasing separately.

## Local environment

Production copy of the database lives in a Docker container, pinned to MariaDB 10.5
to match production (`db-05.nameserver.sk` runs 10.5.29-MariaDB; the database there is
`saleziani_prod`). It publishes a password-less root on `127.0.0.1:3306`, so the
`DATABASE_URL` already in `.env.local` needs no change.

```bash
docker compose up -d                 # start the database
```

If the container was created before a Docker restart it can come back attached to a
network that no longer exists (`failed to set up container networking: network … not
found`). `docker compose up -d --force-recreate` fixes it; the `db-data` volume is
separate, so the imported copy survives.

The *system* MariaDB service is deliberately left stopped — it holds ~51 unrelated
project databases plus a stale `saleziani`, and it would fight the container for port
3306. Nothing there was modified.

Serve the site (no local vhost exists; `saleziani.wip` does not resolve):

```bash
PHP_CLI_SERVER_WORKERS=10 WP_HOME=http://127.0.0.1:8765 WP_SITEURL=http://127.0.0.1:8765/wp \
  php8.3 ~/.local/bin/wp server --host=127.0.0.1 --port=8765 --docroot=web
```

`PHP_CLI_SERVER_WORKERS` matters: the block editor fires many parallel REST requests
and PHP's built-in server is single-threaded without it.

Then `http://127.0.0.1:8765` — admin at `/wp/wp-admin/`. Set yourself a local
password first:

```bash
php8.3 ~/.local/bin/wp user update 4 --user_pass='…' --skip-plugins --skip-themes
```

`wp` needs `php8.3` explicitly (system default is PHP 8.5). Composer too —
`composer.json` platform is 8.3.

Assets: `npm run dev` (watch) or `npm run prod`. The 53 Sass `@import` deprecation
warnings are pre-existing. Note that `npm run prod` adds content hashes to filenames
(`public.fee7b407.js`) while `npm run dev` does not.

`/wp-json/` returning 404 for an anonymous request is **intentional**, not a
regression — `src/legacy/functions/DisableFeatures.php:52` closes the REST API to
everyone who is not logged in.

### Rebuilding the local copy from scratch

The production dump and a post-import snapshot are in `~/Downloads/`:

- `saleziani-db.sql.gz` — raw production dump (production URLs intact)
- `saleziani-local-baseline-6.8.3.sql.gz` — after import, URL rewrite and plugin
  deactivation, still on WP 6.8.3. Restore this to get back to the pre-upgrade
  baseline.

The import is scripted; the working copy lives in the session scratchpad, and the
steps it performs are: recreate the `saleziani` database, import, detect the table
prefix (production uses the default `wp_`), `search-replace` both
`https://saleziani.sk` and `http://saleziani.sk` → `http://127.0.0.1:8765`
(`--all-tables --precise --skip-columns=guid`), deactivate `mainwp-child` and
`w3-total-cache`, and delete the `advanced-cache.php` / `object-cache.php` / `db.php`
drop-ins.

Do **not** replace `https://saleziani.sk` blindly everywhere — `100.saleziani.sk`,
`web@saleziani.sk` and the Facebook/Instagram `saleziani.sk` handles must survive.

Refreshing the copy from production is done by hand (see `hosts.yml` for the host;
uploads and images live under `shared/web/app/uploads` and `shared/web/images` in the
Deployer layout at `/home/html/multi_175876/saleziani.sk/web`).

## Next steps

Review and deploy are with a colleague as of 2026-08-10; the work below is theirs
unless it comes back.

1. Deploy to the `develop` stage first — `hosts.yml` points it at
   `~/saleziani.sk/_sub/stage` — and only then to `main`. Deploys trigger only on
   pushes to `develop` / `main`, and `develop` is currently 14 commits behind `main`
   and 0 ahead, so merging this brings those along too.
2. After the `main` deploy, confirm on production that the front end still loads
   `public.js` / `consent.js` / `firebox.js` and that `blocks.js` is gone, and that
   the block editor opens an existing article without invalid blocks.
3. Separately: the Block API v1 migration and the `__next40pxDefaultSize` deadline
   (known issues 1 and 3), `deployer/deployer` 8.0.5, and production Redis.

PR #20 was originally opened as the WP 6.8.6 CVE bump, so its first description
argued that 6.9/7.0 should be a separate tested upgrade. The description has been
replaced; if an old copy resurfaces anywhere, that reservation is answered by the
browser pass above, not still open.
