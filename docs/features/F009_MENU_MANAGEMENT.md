# F009 — Menu Management

**Feature Status:** `Complete — Implemented, Installed, Verified and Accepted`
**Implementation:** Complete — installed, technically verified and accepted, including final UI refinements.
**User acceptance:** Approved — final browser refinements accepted; screenshots confirmed status and visibility feedback.
**Final user approval:** Approved.
**Active development database migration / initialization:** Passed — confirmed by user; permanent runtime configuration retained.

## Final UI refinement and public visibility feedback — accepted

F009 is Complete — Implemented, Installed, Verified and Accepted. Final browser
acceptance and closure were explicitly approved by the user. Do not repeat the
historical installation commands below on this deployment.

### Final accepted verification evidence

Implementation, installation, automated verification and final browser acceptance were
approved by the user. The ordinary Windows results below are accepted user-supplied
evidence, not new measurements by Codex during this documentation-only closure.

| Verification | Accepted result |
| --- | --- |
| Test database isolation | Passed — SQLite, :memory: |
| PHP / PHPUnit | 8.5.12 / 11.5.56 |
| Original focused F009 suite | Passed — 63 tests, 301 assertions |
| Previous full regression | Passed — 183 tests, 1323 assertions |
| Final focused F009 suite | Passed — 63 tests, 301 assertions |
| Final full regression | Passed — 191 tests, 1421 assertions |
| Final regression execution time | 15.237 seconds |
| Original scoped Pint | Passed — 30 files |
| Refinement scoped Pint | Passed — 4 files |
| PHP syntax verification | Passed |
| Production frontend build | Passed — 59 modules |
| Standalone refinement visibility checks | Passed — 38/38 |
| Manual UI review | Passed — confirmed by user with screenshots |
| Final application acceptance | Approved |
| Final user approval / feature closure | Approved |

#### Focused-filter and refinement coverage distinction

Read-only source inspection confirms that tests/Feature/Admin/MenuVisibilityTest.php
declares Tests\Feature\Admin\MenuVisibilityTest with eight test methods. It resides
under the configured Feature-suite discovery directory. The original focused filter
selects MenuManagementTest, MenuItemsTest, MenuRenderingTest, DefaultMenusTest and
SafeMenuUrlTest; it does not select MenuVisibilityTest.

The accepted final focused result is therefore 63 tests / 301 assertions. Refinement
coverage comes from the accepted final full regression: 191 tests / 1421 assertions,
an increase of eight tests and 98 assertions over 183 / 1323. Source inventory confirms
the eight-method class; assertion counts come from accepted execution evidence, not
an estimate from source. No expanded 71-test focused execution is claimed.
No tests were rerun during closure.

#### Accepted database installation

- MariaDB 11.4.13; host 127.0.0.1; port 3312; database dss_enterprise_cms.
- F009 migration successfully applied in 494.72 ms.
- MenuPermissionsSeeder and DefaultMenusSeeder successfully executed.
- Schema and foreign-key verification passed.
- Existing business records and role mappings preserved.
- Only approved menu permissions added.
- Two default menus and eleven default menu items initialized.
- Persistent default initialization marker verified.
- Permanent runtime database configuration retained.

These live installation results were confirmed by the user. No live database was
accessed by Codex during this documentation closure. No reinitialization is required.

### Implemented refinement

- Active menus/items have green configured-status badges near their title/label.
- Inactive badges are neutral gray; inactive cards have a subtle slate background
  and border. Text contrast and every existing authorized management action remain.
- Styling reads the persisted model status on each response; it is not a client-only toggle.
- MenuResolver::inspect() returns effective eligibility and blocking reasons for all
  items, using the same bounded branch/destination evaluation that produces public
  nodes. Hidden ancestor/menu state is propagated parent-first.
- Feedback distinguishes inactive parent menu, inactive item, hidden ancestor,
  unpublished/missing Page, unavailable public Page routing, invalid destination and
  groups without publicly eligible children. Unavailable hierarchies fail closed.
- Unassigned locations are explained without treating them as inactive: an active,
  eligible menu can render explicitly by its identifier, but is not automatically
  shown in the header/footer.
- The item editor shows the currently saved configuration and assessment, explicitly
  distinguishing it from unsaved changes.
- Page help states exactly: Published CMS Pages can be assigned to this menu, but their
  links will not appear on the website until public Page rendering is implemented.
- No outbound availability probes, fabricated Page routes, schema/permission changes,
  default resets or new dependencies were introduced.

Public eligible URL links, Home links and populated groups retain the existing rendering
contract. Inactive menus/items, hidden branches, invalid destinations and Page references
without a public contract remain excluded. Public groups have no fabricated URL.
The eligibility assessment describes whether the resolver can include an item; it does
not claim that an unassigned menu has been mounted in a website template or that an
external destination was contacted.

### Refinement execution history and final disposition

| Check | Result |
| --- | --- |
| PHP syntax | 4/4 affected PHP files passed |
| Scoped Pint --test | Passed on MenuResolver, MenuController, MenuItemController and MenuVisibilityTest |
| Production frontend build | Passed — Vite 7.3.6, 59 modules, empty temporary envDir; real .env not loaded |
| Standalone in-memory eligibility | 38/38 checks passed; no PDO opened, no SQL, no application bootstrap |
| Historical Codex focused attempt | Blocked before bootstrap; no tests ran in that attempt. Final ordinary-shell focused result: 63 passed / 301 assertions |
| Refinement feature coverage | Eight MenuVisibilityTest methods covered by the accepted final full regression |
| Final full regression after refinement | Passed — 191 tests, 1421 assertions, 15.237 seconds; confirmed by user |
| Manual UI after refinement | Passed — confirmed by user with supplied screenshots |

The first standalone harness rejected Eloquent's query-builder metadata construction.
It was adjusted to allow a grammar-only connection object while a throwing PDO factory
still prevented opening any database or issuing SQL. The final 38 checks passed.
This does not substitute for feature tests, Blade rendering checks or live acceptance.

The new feature tests cover persisted menu/item badge styling and enabled actions,
published Page feedback/help, all visibility restrictions, unassigned identifier use,
header/footer URL/Home and nested-group rendering, hidden branches, shared eligibility/
node agreement, bounded query counts and a missing preloaded reference without disabling FKs.

Changed for this refinement:
- app/Support/MenuResolver.php — shared rendering/diagnostic evaluation.
- app/Http/Controllers/Admin/MenuController.php — per-item assessment for administration.
- app/Http/Controllers/Admin/MenuItemController.php — saved-state assessment and eager loading.
- resources/views/admin/menus/index.blade.php — persisted badges and inactive-card styling.
- resources/views/admin/menus/show.blade.php — status/eligibility feedback and explanations.
- resources/views/admin/menus/item-form.blade.php — routing clarification and saved-state panel.
- resources/views/components/admin/menu-status-badge.blade.php — new reusable badge.
- resources/views/components/admin/menu-item-visibility.blade.php — new escaped feedback component.
- tests/Feature/Admin/MenuVisibilityTest.php — eight new focused tests.
- docs/features/F009_MENU_MANAGEMENT.md — accepted baseline and this refinement record.
Ignored public/build artifacts were regenerated. No migrations, seeders, models,
authorization routes, ACLs, credentials, live records or preserved evidence changed.

### Historical proposed verification commands — retained for reference

Verification and acceptance are complete; no command below is a pending closure task.
This earlier proposed expanded filter includes MenuVisibilityTest and is not the filter
that produced the accepted 63-test focused result. Refinement coverage is established
by the accepted final full regression. All feature tests use SQLite :memory:.

~~~powershell
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath 'C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms'
$preflight = @'
require 'tests/bootstrap.php';
$case = new Tests\Feature\Admin\MenuVisibilityTest('test_menu_badges_and_muted_cards_reflect_saved_status_without_disabling_actions');
$app = $case->createApplication();
$db = $app['db']->connection();
if (!$app->environment('testing') || $db->getDriverName() !== 'sqlite' || $db->getDatabaseName() !== ':memory:') {
    throw new RuntimeException('STOP: unsafe test database');
}
echo 'Preflight Passed: testing / sqlite / :memory:', PHP_EOL;
'@
& php -r $preflight
if ($LASTEXITCODE -ne 0) { throw 'STOP: isolated test preflight failed' }

& php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result --filter '(MenuManagementTest|MenuItemsTest|MenuVisibilityTest|MenuRenderingTest|DefaultMenusTest|SafeMenuUrlTest)'
if ($LASTEXITCODE -ne 0) { throw 'STOP: focused F009 tests failed' }

& php vendor/bin/pint --test app/Support/MenuResolver.php app/Http/Controllers/Admin/MenuController.php app/Http/Controllers/Admin/MenuItemController.php tests/Feature/Admin/MenuVisibilityTest.php
if ($LASTEXITCODE -ne 0) { throw 'STOP: scoped Pint failed' }
~~~

The build already passed for the refined views; it need not be repeated without asset
changes. The existing environment-isolated build recipe below remains available if needed.
No sandbox/ACL changes or alternative execution context were attempted by Codex.

### Browser review checklist — Passed, confirmed by user

The user accepted the following behavior and supplied screenshots confirming the
expected status and visibility feedback, including the responsive administration layout.

- Hard-refresh /admin/menus: active green badges; inactive gray badges and muted cards.
- Deactivate/save/reload a menu or item: persisted appearance changes; authorized actions work.
- Active About Us linked to a Published Page: Active badge plus Hidden/public-routing reason.
- Create/Edit Page item: exact public-rendering limitation and saved-state explanation.
- Inactive menu, inactive item, hidden parent, Draft Page, invalid URL and empty group:
  correct restriction shown; no hidden descendants promoted.
- Unassigned active menu: identifier eligibility is explained, without promising header/footer placement.
- Existing header/footer Home/custom links and eligible nested groups still render.
- Narrow/mobile layout, long labels, badge wrapping, keyboard actions and delete confirmation.
- No default menu reinitialization, database reset or public Page renderer is needed.


## Objective and authorization

Implement reusable, permission-protected menus, locations, hierarchical menu items,
safe destinations, deterministic ordering and public navigation components.
The approved complete F009 assignment and F009.11 default initialization supplement
authorize implementation, focused tests and one applicable full regression run.
They release F009 from its previous database-related hold; they do not authorize
active-database migrations, credential access, ACL changes or F010 work.

Accepted environment status supplied by the user: CMS-DB-04 fresh initialization
Complete; CMS-DB-03 existing-data migration Discontinued. MariaDB 11.4.13 is the
development database on 127.0.0.1:3312, schema dss_enterprise_cms. These are accepted
user observations, not new database measurements in this assignment.
F001–F008 remain Complete. Existing unrelated Pint findings and Composer advisories
are outside scope. No governance rules or accepted feature records were rewritten.

## Review findings and implementation decisions

- Initial local working tree was clean; no pre-existing modifications were replaced.
- Laravel 12, Blade/Tailwind/Alpine and Spatie RBAC conventions are retained.
- Page identity uses title, slug and Draft/Published status. F007/F008 defer public
  Page routing. The only existing public content route is named `home`, at `/`.
- The header/footer contain presentation text but no hardcoded navigation links.
  Their text is preserved, with location-based navigation components added.
- Maximum hierarchy depth is three, including the root. Parent IDs are scoped to
  the owning menu, and the complete affected tree is checked after a proposed move.
- Accessible Move Up/Down controls are provided. Drag and drop is deferred; no
  additional dependency or frontend framework is introduced.
- Existing F008 owner-row locking patterns are reused. All menu-item operations
  lock the menu before its items; Page reference acquisition locks the Page first,
  matching Page deletion/reference coordination. No live concurrency claim is made.
- A menu `lock_version` rejects stale item mutations and reorder requests rather
  than silently overwriting a concurrently changed menu.
- Public Page destinations currently resolve to null. Page items, including their
  descendants, are omitted publicly until an approved Page routing contract exists.
  Page IDs remain authoritative; no copied slug, invented route or admin URL is used.

## Approved functional scope

Menu create/view/edit/delete, search, pagination, activation and location assignment;
Page/URL/non-clickable-group items; parent changes; three-level validation; sibling
ordering; link target; deletion safeguards; public visibility filtering; one-time
default navigation initialization. Menus and items remain ordinary editable records.

Permissions: `menus.view`, `menus.create`, `menus.update`, `menus.delete`.
All admin routes require authentication and `admin.access`. All menu routes require
`menus.view`; creation additionally requires `menus.create`, menu editing and all
item mutations require `menus.update`, and menu deletion requires `menus.delete`.
The existing centralized Super Admin bypass is unchanged. No new permission is
automatically assigned to Admin. The menu-only permission seeder does not mutate roles.

## Additive database design

New migration: `database/migrations/2026_11_26_000001_create_menu_tables.php`.
Previously executed migrations are unchanged. The additive migration design yields:
10 migration records and 21 tables by the migration design. The user confirms migration success; Codex did not re-inventory the active database.

| Table | Columns / constraints |
| --- | --- |
| menus | id; name varchar(200); unique slug varchar(150); nullable description text; nullable unique location varchar(30); is_active boolean; lock_version unsigned integer; timestamps |
| menu_items | id; menu_id FK; nullable parent_id; nullable page_id FK; label varchar(200); type varchar(20); nullable url varchar(2048); target varchar(10); sort_order unsigned integer; is_active boolean; timestamps |
| menu_initializations | key varchar(100) primary key; nullable completed_at timestamp |

The unique `(id, menu_id)` key supports a restrictive composite parent foreign key
`(parent_id, menu_id) -> menu_items(id, menu_id)`, enforcing same-menu parenting.
Menu and Page FKs are restrictive. The sibling-order index is
`(menu_id, parent_id, sort_order, id)`. Contiguous order is maintained transactionally;
no unreliable nullable-root unique ordering constraint is used.
Down migration removes these new tables in dependency order; it is destructive to
menu data and is not an authorized deployment or rollback command in this assignment.

## Mutation and deletion policies

- Parent validation rejects foreign menus, self-parenting, cycles and depth overflow,
  including descendants of a moved item. Traversal depth is bounded.
- Changing parent appends the item to the new sibling group; old/new groups normalize.
- Full sibling reorder requires each current sibling exactly once and a current version.
- Failed related writes roll back together. Stale versions produce a reload message.
- Type changes clear incompatible Page/URL fields. Groups have no fabricated URL and
  always use the same-tab target value internally.
- Nonempty menus and parent items with children cannot be deleted. Page references
  protect deletion even when their menus/items are inactive. No content cascades.

## Public URL and visibility policies

Custom URLs accept a single-leading-slash site-relative path or absolute HTTP/HTTPS
URL, up to 2048 characters. Query strings and fragments are supported.
Validation rejects unsupported/encoded schemes, protocol-relative forms, embedded
credentials, malformed absolute URLs, controls and backslashes, including repeated
percent-encoding bypasses. No outbound validation request is performed.
Labels and attributes use escaped Blade output. New-tab links receive
`rel="noopener noreferrer"`. No arbitrary HTML attributes are persisted.

`MenuResolver::forLocation()` and `forSlug()` eager-load items and Pages. They omit
inactive menus/items, unavailable Page destinations, unsafe URLs and entire hidden
ancestor branches; children are never promoted. Empty groups and missing/empty menus
produce no navigation. Pre-migration rendering returns no navigation when the menu
table does not exist; it never seeds or falls back to hardcoded links.

Reusable component: `<x-navigation-menu location="header" />`,
`<x-navigation-menu location="footer" />`, or `<x-navigation-menu slug="identifier" />`.
Frontend theme rendering and the Content API remain deferred.

## F009.11 — One-time default initialization

`DefaultMenusSeeder` initializes ordinary active menus:

| Identifier / location | Ordered initial labels |
| --- | --- |
| main-navigation / header | Home; About Us; Services; Projects; Contact Us |
| footer-navigation / footer | Home; About Us; Services; Contact Us; Privacy Policy; Terms & Conditions |

Only Home has a verified destination (`route('home', absolute: false)`, `/`) and is
active. All unavailable defaults are inactive navigation groups without URLs. An
administrator may change their types/destinations and activate them through normal UI.
No sample Pages, Media or Users are created. No Dewatering-specific content is seeded.

A transaction inserts/locks the persistent `default-menus-v1` initialization marker,
checks completion, creates missing defaults and marks completion. Reruns do not restore
deleted items or menus, reset labels/destinations/order, reassign locations or fill
intentionally empty menus. Pre-existing default identifiers are left entirely alone.
Occupied locations remain assigned to their existing menu; a new default is unassigned.
Concurrent initialization still requires separately approved MariaDB verification.

Fresh-install `DatabaseSeeder` calls the existing RBAC seeder and this initializer;
its previous demonstration User insertion is removed. Existing installations have
explicit, separately reviewed initialization commands, never request-time initialization.

## Earlier correction history — superseded by the accepted baseline above

The fixture was registered in the correct test application. Laravel adds the route
to its collection before the fluent ->name() call assigns its name; the name lookup
therefore remained stale in this already booted test. The test now calls
Route::getRoutes()->refreshNameLookups() immediately after registration.
Its original publication/current-slug assertions are unchanged. No production route,
renderer, exception suppression or skipped assertion was introduced.

The completeness review also reproduced acceptance of malformed original percent
escapes (/bad%ZZ and https://example.com/bad%ZZ). SafeMenuUrl now rejects these, with
two regression datasets. All original 61 cases remain; the focused suite now defines
63 cases. The user subsequently confirmed 63 passed / 301 assertions; current refinement tests are recorded separately above.

| Verification | Actual result |
| --- | --- |
| Prior ordinary Windows focused run | User-reported: 61 tests, 297 assertions, 1 error; isolated SQLite preflight passed |
| Corrected single PHPUnit test, Codex | Blocked before bootstrap: is_readable() check; zero tests executed |
| Later ordinary-shell verification | Accepted: final focused 63/301; final full regression 191/1421. Earlier execution limits are preserved as history |
| Standalone Laravel routing reproduction | 3/3 checks passed: stale lookup, successful refresh, current-slug relative URL |
| Standalone URL-rule datasets | 33/33 passed; not a PHPUnit run |
| PHP syntax | 30/30 F009 PHP files passed php -l |
| Historical scoped Pint --test | Earlier SafeMenuUrl formatting findings were subsequently resolved; accepted original scoped Pint: 30 files passed |
| Historical Codex formatting attempt | Blocked by writable-file check at that stage; later accepted ordinary-shell Pint results supersede it |
| Production frontend build | Passed: Vite 7.3.6, 59 modules, existing dependencies |
| Manual command syntax | Six PowerShell blocks parsed under Windows PowerShell 5.1; five embedded PHP snippets passed php -l; none executed against an application/database |
| Subsequent installation / UI | Migration, seeders, schema/FK checks and manual UI accepted; MariaDB concurrent-write verification intentionally remains unperformed |

The build used Vite build() with a newly created empty temporary envDir, also respected
by laravel-vite-plugin. It did not load the real .env. The temporary directory was
removed; ignored public/build assets were generated. No dependency/config changes.

The source review covered all required hierarchy, binding, ordering, Page reference,
publication, URL, permission, initialization/customization and frontend controls.
No additional concrete F009 defect was identified. Source inspection and SQLite
functional tests do not establish live MariaDB concurrency behavior.

PHPUnit continues to use tests/bootstrap.php, synthetic configuration, temporary
environment/cache/storage and SQLite :memory:. Tests\TestCase checks configuration
and PRAGMA database_list before destructive setup; no active database is permitted.
No sandbox permissions, execution identity or vendor source were altered or investigated.
After the single-test bootstrap failure, the focused/full suites were not repeatedly retried.

### Historical pre-install verification commands — already completed before refinement

Run only in the authorized project. Stop at the first failure. This does not use
Artisan to launch tests and cannot intentionally select the active MariaDB connection.

~~~powershell
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath 'C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms'
$preflight = @'
require 'tests/bootstrap.php';
$case = new Tests\Feature\Admin\MenuManagementTest('test_guest_and_unauthorized_direct_routes_are_rejected');
$app = $case->createApplication();
$db = $app['db']->connection();
if (!$app->environment('testing') || $db->getDriverName() !== 'sqlite' || $db->getDatabaseName() !== ':memory:') {
    throw new RuntimeException('STOP: unsafe test database');
}
echo 'Preflight Passed: testing / sqlite / :memory:', PHP_EOL;
'@
& php -r $preflight
if ($LASTEXITCODE -ne 0) { throw 'STOP: test isolation preflight failed' }

& php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result --filter test_publication_and_current_slug_contract_can_be_supplied_by_a_later_renderer
if ($LASTEXITCODE -ne 0) { throw 'STOP: corrected fixture test failed' }

& php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result --filter '(MenuManagementTest|MenuItemsTest|MenuRenderingTest|DefaultMenusTest|SafeMenuUrlTest)'
if ($LASTEXITCODE -ne 0) { throw 'STOP: focused suite failed' }

& php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result
if ($LASTEXITCODE -ne 0) { throw 'STOP: full regression failed' }

# Format only the reported F009 file, then review its diff.
& php vendor/bin/pint app/Rules/SafeMenuUrl.php
if ($LASTEXITCODE -ne 0) { throw 'STOP: formatting failed' }
$changed = @(git -c core.safecrlf=false diff --name-only -- '*.php')
$added = @(git ls-files --others --exclude-standard -- '*.php')
$scopedPhp = @($changed + $added | Where-Object { $_ -notlike '*.blade.php' } | Sort-Object -Unique)
# At this handoff these are the 30 F009 files. Review/narrow this list if unrelated
# work has since been added. Both Git commands above are read-only.
& php vendor/bin/pint --test @scopedPhp
if ($LASTEXITCODE -ne 0) { throw 'STOP: scoped Pint failed' }
~~~

The build already passed and need not be repeated for PHP formatting. If later assets
change, use the same environment-isolated build command below (no package installation):

~~~powershell
@'
import { mkdtemp, rm } from 'node:fs/promises';
import { tmpdir } from 'node:os';
import path from 'node:path';
const envDirectory = await mkdtemp(path.join(tmpdir(), 'cms-f009-vite-env-'));
try {
    const { build } = await import('vite');
    await build({ envDir: envDirectory });
} finally {
    const target = path.resolve(envDirectory);
    if (path.dirname(target) !== path.resolve(tmpdir()) || !path.basename(target).startsWith('cms-f009-vite-env-')) {
        throw new Error('Unexpected temporary path; cleanup refused');
    }
    await rm(target, { recursive: true, force: true });
}
'@ | node --input-type=module
if ($LASTEXITCODE -ne 0) { throw 'STOP: frontend build failed' }
~~~

Existing unrelated Pint findings in app/View/Components/FrontendLayout.php and
routes/frontend.php, and the documented Composer advisories, remain outside scope.

## Implementation file inventory

New:
- app/Enums/MenuLocation.php, MenuItemType.php
- app/Models/Menu.php, MenuItem.php
- app/Rules/SafeMenuUrl.php
- app/Support/MenuTree.php, MenuResolver.php
- app/Actions/Menus/MutateMenuItems.php
- app/Http/Requests/Admin/StoreMenuRequest.php, StoreMenuItemRequest.php, MutateMenuItemRequest.php
- app/Http/Controllers/Admin/MenuController.php, MenuItemController.php
- app/View/Components/NavigationMenu.php
- database/migrations/2026_11_26_000001_create_menu_tables.php
- database/seeders/DefaultMenusSeeder.php, MenuPermissionsSeeder.php
- resources/views/admin/menus/index.blade.php, form.blade.php, show.blade.php, item-form.blade.php
- resources/views/components/admin/menu-errors.blade.php, menu-delete-dialog.blade.php
- resources/views/components/navigation-menu.blade.php
- resources/views/frontend/partials/menu-branch.blade.php
- tests/bootstrap.php, tests/Concerns/WithMenuFixtures.php
- tests/Feature/Admin/MenuManagementTest.php, MenuItemsTest.php
- tests/Feature/MenuRenderingTest.php, DefaultMenusTest.php
- tests/Unit/SafeMenuUrlTest.php
- docs/features/F009_MENU_MANAGEMENT.md

Modified:
- app/Support/Rbac.php — four permission definitions, existing Admin defaults unchanged.
- app/Models/Page.php — menuItems reference relationship.
- app/Http/Controllers/Admin/PageController.php — referenced-Page deletion protection.
- database/seeders/DatabaseSeeder.php — essential initialization without sample User.
- routes/admin.php — named, permission-protected scoped Menu/Item routes.
- resources/views/admin/layouts/partials/sidebar.blade.php — permission-aware Menus link.
- resources/views/frontend/layouts/partials/header.blade.php, footer.blade.php — navigation components, presentation text preserved.
- phpunit.xml, tests/TestCase.php — safe test bootstrap and pre-migration database guard.

No files removed; no dependency changes; no ACL or configuration changes.

## Routes prepared

All names begin `admin.menus.`:
- GET /admin/menus — index; GET /admin/menus/create — create; POST /admin/menus — store.
- GET /admin/menus/{menu} — show; GET /admin/menus/{menu}/edit — edit;
  PATCH /admin/menus/{menu} — update; DELETE /admin/menus/{menu} — destroy.
- GET /admin/menus/{menu}/items/create — items.create; POST /admin/menus/{menu}/items — items.store.
- GET /admin/menus/{menu}/items/{item}/edit — items.edit;
  PATCH /admin/menus/{menu}/items/{item} — items.update;
  DELETE /admin/menus/{menu}/items/{item} — items.destroy.
- PATCH /admin/menus/{menu}/items/{item}/move — items.move;
  PATCH /admin/menus/{menu}/items/reorder — items.reorder.

## Historical first-install procedure — completed by user; do not repeat for this refinement

The user has confirmed completion of the original installation below. Retain this as
first-install reference only; do not execute it again on the current database.
For an independently approved new installation, run only after its tests pass,
implementation review is approved and installation is separately authorized. Arrange
a quiet CMS-only write window and an approved recovery backup. Do not stop shared
services. Keep permanent .env DB_USERNAME=dss_cms_runtime; no command below edits it.
Do not use migrate:fresh, refresh, wipe, general DatabaseSeeder or a root account.

This first-install procedure requires the pending F009 migration, nine existing
migration records and no existing F009 tables. If the baseline differs, STOP for review;
never remove/recreate an existing table to make the checks pass. Existing business
tables are not assumed empty. Passwords use secure prompts and process environment,
never command-line arguments, chat, files or output.

### 1. Define process-only credentials, SQL identity checks and preservation snapshot

Paste this setup once into ordinary PowerShell at the project root. These are
reviewable shell variables/functions, not an installed deployment framework.

~~~powershell
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath 'C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms'
function Invoke-F009Php([string]$Code) {
    $output = ('<?php' + [Environment]::NewLine + $Code) | & php
    if ($LASTEXITCODE -ne 0) { throw 'STOP: PHP command failed; do not continue' }
    return ($output -join [Environment]::NewLine)
}
function Use-F009Account([string]$Account, [Security.SecureString]$Password, [scriptblock]$Operation) {
    if ($Account -notin @('dss_cms_runtime','dss_cms_migrator')) { throw 'Unexpected account' }
    $keys = @('DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD','DB_URL','DB_SOCKET','APP_CONFIG_CACHE')
    $saved = @{}
    foreach ($key in $keys) { $saved[$key] = [Environment]::GetEnvironmentVariable($key, 'Process') }
    $configPath = Join-Path ([IO.Path]::GetTempPath()) ('cms-f009-no-config-' + [guid]::NewGuid().ToString('N') + '.php')
    if (Test-Path -LiteralPath $configPath) { throw 'Temporary configuration path collision' }
    try {
        $env:DB_CONNECTION='mysql'; $env:DB_HOST='127.0.0.1'; $env:DB_PORT='3312'
        $env:DB_DATABASE='dss_enterprise_cms'; $env:DB_URL=''; $env:DB_SOCKET=''; $env:DB_USERNAME=$Account
        $env:APP_CONFIG_CACHE=$configPath
        $pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($Password)
        try { $env:DB_PASSWORD=[Runtime.InteropServices.Marshal]::PtrToStringBSTR($pointer) }
        finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($pointer) }
        & $Operation
    } finally {
        foreach ($key in $keys) { [Environment]::SetEnvironmentVariable($key, $saved[$key], 'Process') }
    }
}
$identityPhp = @'
$account = getenv('DB_USERNAME');
if (!in_array($account, ['dss_cms_runtime','dss_cms_migrator'], true)) {
    throw new RuntimeException('STOP: unexpected requested account');
}
$pdo = new PDO('mysql:host=127.0.0.1;port=3312;dbname=dss_enterprise_cms;charset=utf8mb4',
    $account, getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES=>false]);
$identity = $pdo->query('SELECT @@version AS version, @@port AS port, @@datadir AS datadir, DATABASE() AS dbname, CURRENT_USER() AS account')->fetch(PDO::FETCH_ASSOC);
$directory = strtolower(rtrim(str_replace('\\','/',$identity['datadir']),'/'));
if (!str_starts_with($identity['version'],'11.4.13') || (int)$identity['port']!==3312
    || $directory!=='c:/mariadbdata/cmsdev11413' || $identity['dbname']!=='dss_enterprise_cms'
    || !in_array($identity['account'],[$account.'@127.0.0.1',$account.'@localhost'],true)) {
    throw new RuntimeException('STOP: authenticated database identity mismatch');
}
$q = fn ($name) => chr(96).str_replace(chr(96),chr(96).chr(96),$name).chr(96);
'@
$pendingPhp = @'
if ((int)$pdo->query('SELECT COUNT(*) FROM migrations')->fetchColumn()!==9
    || (int)$pdo->query("SELECT COUNT(*) FROM migrations WHERE migration='2026_11_26_000001_create_menu_tables'")->fetchColumn()!==0
    || (int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('menus','menu_items','menu_initializations')")->fetchColumn()!==0) {
    throw new RuntimeException('STOP: F009 is not pending against the reviewed baseline');
}
'@
$snapshotPhp = @'
$tables=['users','roles','model_has_roles','model_has_permissions','role_has_permissions','website_settings','pages','page_sections','media','permissions','migrations'];
$state=['records'=>[],'permissions'=>[]];
foreach ($tables as $table) {
    $columns=[];
    foreach ($pdo->query('SHOW COLUMNS FROM '.$q($table))->fetchAll(PDO::FETCH_ASSOC) as $column) {
        if (!preg_match('/password|token|secret|payload|credential|api_key/i',$column['Field'])) {
            $columns[]=$column['Field'];
        }
    }
    $state['records'][$table]=[];
    $statement=$pdo->query('SELECT '.implode(',',array_map($q,$columns)).' FROM '.$q($table));
    while ($row=$statement->fetch(PDO::FETCH_ASSOC)) {
        if (count($state['records'][$table])>=100000) { throw new RuntimeException('STOP: snapshot exceeds reviewed bound'); }
        $digest=hash('sha256',json_encode($row,JSON_THROW_ON_ERROR|JSON_INVALID_UTF8_SUBSTITUTE));
        $key=isset($row['id'])?(string)$row['id']:$digest;
        $state['records'][$table][$key]=$digest;
        if ($table==='permissions') { $state['permissions'][$key]=['name'=>$row['name'],'guard'=>$row['guard_name']]; }
    }
}
foreach ($state['records'] as $table => $records) { $state['records'][$table] = (object)$records; }
$state['permissions'] = (object)$state['permissions'];
echo json_encode($state,JSON_THROW_ON_ERROR);
'@
$runtimePassword=Read-Host 'Runtime database password (not recorded)' -AsSecureString
$before=Use-F009Account 'dss_cms_runtime' $runtimePassword {
    Invoke-F009Php ($identityPhp+[Environment]::NewLine+$pendingPhp+[Environment]::NewLine+$snapshotPhp) | ConvertFrom-Json
}
'Runtime identity and preservation snapshot: Passed'
~~~

The native PDO identity check does not bootstrap Laravel. The expected persistent data
directory above comes from accepted environment evidence; if CMS-DB-04 uses a different
accepted directory/account host, stop for review rather than weakening the check.
Snapshots remain in PowerShell memory and expose only hashes of non-secret business
columns. Password/token/session/job contents are not retrieved or printed. This is a
preservation check, not a replacement for an approved recovery backup.

### 2. Apply only F009, using the migration account

~~~powershell
$migrationPassword=Read-Host 'Migration database password (not recorded)' -AsSecureString
try {
    Use-F009Account 'dss_cms_migrator' $migrationPassword {
        $null=Invoke-F009Php ($identityPhp+[Environment]::NewLine+$pendingPhp)
        & php artisan migrate --database=mysql --path=database/migrations/2026_11_26_000001_create_menu_tables.php --force --no-interaction
        if ($LASTEXITCODE -ne 0) { throw 'STOP: migration failed; do not seed or automatically roll back' }
    }
} finally { $migrationPassword.Dispose() }
~~~

Only this additive migration is selected. Process-only APP_CONFIG_CACHE points to a
nonexistent temporary filename so stale cached runtime configuration cannot override
the migration account. No permanent migration credentials/configuration cache is created.

### 3. Initialize using the runtime account

~~~powershell
Use-F009Account 'dss_cms_runtime' $runtimePassword {
    $null=Invoke-F009Php $identityPhp
    & php artisan db:seed --database=mysql --class=MenuPermissionsSeeder --force --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'STOP: permission initialization failed' }
    & php artisan db:seed --database=mysql --class=DefaultMenusSeeder --force --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'STOP: default initialization failed' }
}
~~~

MenuPermissionsSeeder initializes only missing definitions and assigns nothing to roles.
Do not substitute RolesAndPermissionsSeeder on an existing installation when customized
role assignments must remain unchanged. DatabaseSeeder is for approved fresh installations.

### 4. Verify new schema/defaults and preserved existing data

~~~powershell
$verifyPhp = @'
if ((int)$pdo->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('menus','menu_items','menu_initializations')")->fetchColumn()!==3
    || (int)$pdo->query('SELECT COUNT(*) FROM migrations')->fetchColumn()!==10
    || (int)$pdo->query("SELECT COUNT(*) FROM migrations WHERE migration='2026_11_26_000001_create_menu_tables'")->fetchColumn()!==1) {
    throw new RuntimeException('STOP: schema/migration coverage mismatch');
}
$rules=$pdo->query("SELECT DELETE_RULE FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='menu_items'")->fetchAll(PDO::FETCH_COLUMN);
if (count($rules)!==3 || count(array_filter($rules,fn($rule)=>!in_array(strtoupper($rule),['RESTRICT','NO ACTION'],true)))!==0) {
    throw new RuntimeException('STOP: expected restrictive foreign keys missing');
}
$bindings=$pdo->query("SELECT k.CONSTRAINT_NAME AS constraint_name,k.COLUMN_NAME AS column_name,k.REFERENCED_TABLE_NAME AS table_name,k.REFERENCED_COLUMN_NAME AS foreign_column FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS k WHERE k.CONSTRAINT_SCHEMA=DATABASE() AND k.TABLE_NAME='menu_items' AND k.REFERENCED_TABLE_NAME IS NOT NULL ORDER BY k.CONSTRAINT_NAME,k.ORDINAL_POSITION")->fetchAll(PDO::FETCH_ASSOC);
$groups=[];
foreach ($bindings as $binding) {
    $groups[$binding['constraint_name']][]=$binding['column_name'].'>'.$binding['table_name'].'.'.$binding['foreign_column'];
}
$actual=array_map(fn($parts)=>implode('|',$parts),array_values($groups));
$expected=['menu_id>menus.id','page_id>pages.id','parent_id>menu_items.id|menu_id>menu_items.menu_id'];
sort($actual);sort($expected);
if ($actual!==$expected) { throw new RuntimeException('STOP: foreign-key target or composite parent binding mismatch'); }
if ((int)$pdo->query('SELECT COUNT(*) FROM menus')->fetchColumn()!==2
    || (int)$pdo->query('SELECT COUNT(*) FROM menu_items')->fetchColumn()!==11) {
    throw new RuntimeException('STOP: unexpected defaults; do not reset data');
}
$marker=$pdo->prepare('SELECT completed_at FROM menu_initializations WHERE '.$q('key').'=?');
$marker->execute(['default-menus-v1']);
if (!$marker->fetchColumn()) { throw new RuntimeException('STOP: initialization marker incomplete'); }
$definitions=[
 ['header','main-navigation',['Home','About Us','Services','Projects','Contact Us']],
 ['footer','footer-navigation',['Home','About Us','Services','Contact Us','Privacy Policy','Terms & Conditions']]
];
foreach ($definitions as [$location,$slug,$labels]) {
    $find=$pdo->prepare('SELECT id,is_active FROM menus WHERE location=? AND slug=?');
    $find->execute([$location,$slug]);$menu=$find->fetch(PDO::FETCH_ASSOC);
    if (!$menu || !(bool)$menu['is_active']) { throw new RuntimeException('STOP: default menu/location mismatch'); }
    $find=$pdo->prepare('SELECT label,type,url,page_id,parent_id,target,is_active,sort_order FROM menu_items WHERE menu_id=? ORDER BY sort_order,id');
    $find->execute([$menu['id']]);$items=$find->fetchAll(PDO::FETCH_ASSOC);
    if (count($items)!==count($labels)) { throw new RuntimeException('STOP: default item count mismatch'); }
    foreach ($items as $index=>$item) {
        $home=$index===0;
        if ($item['label']!==$labels[$index] || (int)$item['sort_order']!==$index+1 || $item['parent_id']!==null
            || $item['page_id']!==null || $item['target']!=='_self' || (bool)$item['is_active']!==$home
            || $item['type']!==($home?'url':'group') || $item['url']!==($home?'/':null)) {
            throw new RuntimeException('STOP: default destination, ordering or visibility mismatch');
        }
    }
}
'@
try {
    $after=Use-F009Account 'dss_cms_runtime' $runtimePassword {
        Invoke-F009Php ($identityPhp+[Environment]::NewLine+$verifyPhp+[Environment]::NewLine+$snapshotPhp) | ConvertFrom-Json
    }
    foreach ($table in $before.records.PSObject.Properties.Name) {
        foreach ($id in $before.records.$table.PSObject.Properties.Name) {
            if (-not $after.records.$table.PSObject.Properties[$id] -or $before.records.$table.$id -cne $after.records.$table.$id) {
                throw "STOP: existing record changed in $table"
            }
        }
        if ($table -notin @('permissions','migrations') -and
            @($before.records.$table.PSObject.Properties).Count -ne @($after.records.$table.PSObject.Properties).Count) {
            throw "STOP: unexpected business record count change in $table"
        }
    }
    $allowed=@('menus.view','menus.create','menus.update','menus.delete')
    foreach ($id in $after.permissions.PSObject.Properties.Name) {
        if (-not $before.permissions.PSObject.Properties[$id] -and
            ($after.permissions.$id.guard -ne 'web' -or $after.permissions.$id.name -notin $allowed)) {
            throw 'STOP: unexpected new permission'
        }
    }
    foreach ($name in $allowed) {
        if (@($after.permissions.PSObject.Properties.Value|Where-Object{$_.guard -eq 'web' -and $_.name -eq $name}).Count -ne 1) {
            throw 'STOP: missing/duplicate menu permission'
        }
    }
    'Installation verification: Passed; existing business records and role mappings preserved; only approved permissions added'
} finally { $runtimePassword.Dispose() }
~~~

Every account block restores its original process environment even on failure. If an
earlier step fails, stop and dispose any retained SecureString before closing the shell.
Do not continue, automatically drop tables, rerun a partial installation blindly or
restore credentials into .env. Preserve the error for review without posting secrets.
The comparison intentionally excludes password/token fields; reviewed migration/seeders
do not write those tables. No password-content verification claim should be made.

### 5. Historical browser checklist — application acceptance completed

Using the ordinary runtime-configured application, verify:
- Menus sidebar/permission boundaries and unauthorized direct URLs.
- Header/footer Home links; inactive unavailable defaults; no hardcoded fallback.
- Menu CRUD/search/pagination, unique locations, status and location removal.
- Page/URL/group forms, Draft/Published labels, type changes and link targets.
- Three-level hierarchy, self/cycle/cross-menu/depth errors and subtree parent moves.
- Move Up/Down, sibling continuity and stale-version recovery.
- Themed deletion confirmation/Cancel/Escape and restrictive deletion messages.
- Reference protection for Pages used by inactive items/menus.
- Default customization, deletion and repeated initialization preservation.
- Escaping, long labels, mobile layout, keyboard controls and existing F001–F008 behavior.

Browser test writes occur only in the approved manual acceptance window after preservation
comparisons. MariaDB concurrency tests require a separately authorized disposable database.
Verification, installation and final application acceptance have passed; F009 is Complete
with the intentional limitations recorded below.


## Known limitations and stop record

Intentional limitations and deferred capabilities:

- Public CMS Page rendering remains deferred. Published Page menu items cannot resolve
  publicly until an approved public routing contract is implemented.
- Drag-and-drop ordering remains deferred; accessible Move Up/Down controls are provided.
- The approved menu hierarchy limit remains three levels.
- MariaDB-specific concurrent-write verification has not been performed. SQLite tests,
  schema/FK installation checks and source review are not substitutes for that evidence.
- Content API and headless integration remain deferred.
- The separate Codex sandbox-access discrepancy remains unresolved.
- No new caching infrastructure, multilingual/mega-menu support or Reset to Defaults.
  Only header/footer locations and the three approved item types are implemented.

Final refinement verification and browser acceptance were confirmed by the user.
No application/source, test, database, service, ACL, credential or Git state changes
were made during this documentation-only closure. User-executed installation is recorded
separately above; F010 and Public Page Rendering work have not begun.

## Proposed next development sequence — separate authorization required

1. Public Page Rendering.
2. F010 — SEO Management.

This is a proposed sequence only. No existing feature identifier is changed and no
new identifier is assigned here. Neither assignment begins as part of F009 closure.

AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
