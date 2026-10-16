F007 — Page Management
Status:
Complete

Final user approval — 2026-10-17
F007 is Complete following explicit final user approval. The completion record below supersedes historical pending review/status entries. Documented architectural limitations, unrelated Pint findings and Composer security advisories remain unchanged. This completion step updates only this specification; no application code, database, storage, authorization or dependencies were changed and no tests, Pint or build were rerun.

Delete-dialog action spacing — 2026-10-17
- Added explicit spacing above the action divider and padding around the Cancel/Delete Page row, retaining button wrapping and separation. Confirmation behavior is unchanged; user review remains pending.

Stacked-list spacing correction — 2026-10-17
- Added a 1rem gap and individual bordered card surfaces to stacked Page rows. Desktop retains the existing contiguous table. Presentation-only; status remains Ready for Review pending user review.

Manual-review corrections — 2026-10-17
- Pages index now uses one shared native dialog with existing Alpine/Tailwind, a dimmed backdrop, safe selected-title text binding and the selected existing DELETE action. Cancel is a non-submit button with initial focus; Escape cancels while idle, native modal focus containment/return applies, and confirmation disables duplicate submission. Existing CSRF/permission/deletion/redirect behavior remains authoritative.
- Shared create/edit form has distinct field groups, spaced labels, consistent minimum 44px controls, errors directly below fields, helper-text margins and a divided/padded primary/secondary action area. Forms and dialog wrap/fit narrow viewports without introducing a framework.
- Successful create/update now redirect to admin.pages.index with existing success messages. Validation failures return to their originating forms with old input/errors; no false success or changed slug rules.
- Focused command: php artisan test --compact tests/Feature/Admin/PageManagementTest.php — 10 passed, 236 assertions. Covers save redirects/feedback, invalid-form context, safe shared dialog selection/cancel controls, protected deletion and retained business-rule coverage.
- npm.cmd run build passed to compile updated UI styles for manual review. No new dependencies, schema changes, full regression, final validation or F008 work. Status remains Ready for Review; manual review is pending user confirmation.

Approved implementation record — 2026-10-17
- Implemented identity/lifecycle CRUD only: Page/pages with id, required title varchar(200), required unique slug varchar(150), required status varchar(20) default draft, and timestamps. No other fields, relationships or indexes beyond primary/unique slug.
- PageStatus is a string-backed enum with Draft/Published. Omitted create status defaults to draft; omitted update fields remain unchanged; explicit empty/invalid required values fail validation.
- Title is trimmed, limited to 200, rejects angle brackets and is escaped in Blade. Slugs accept human-readable letters/marks/numbers/spaces/underscores/hyphens for Str::slug normalization, reject path/URL syntax, and validate the final nonempty ASCII single segment (max 150), reserved set and uniqueness. No truncation or numeric suffixing.
- Create derives blank/omitted slug from title server-side and offers create-only Alpine suggestions until manual editing. Update never regenerates slug from title; blank update slug fails. Shared Form Request rules are inherited by UpdatePageRequest with explicit update semantics; no model events regenerate identity.
- Reserved slugs: admin, login, logout, register, storage, api, dashboard, profile, forgot-password, reset-password, verify-email, email, confirm-password, password, up, build.
- Thin PageController uses validated Eloquent writes. A confirmed unique-slug database race yields a slug validation error; unrelated unique/database errors are not disguised. No Actions/services/repositories or new packages.
- Six ID-bound admin routes (index/create/store/edit/update/destroy) require auth/admin.access/pages.manage. Added pages.manage only to the registry; existing additive seeder and Admin defaults unchanged. Super Admin uses the existing Gate bypass. Pages navigation follows Media Library and permission visibility.
- Index searches title OR slug in a grouped parameterized query, filters All/Draft/Published, orders updated_at DESC/id DESC and paginates 20 with retained filters. Responsive desktop table/mobile list, shared labeled identity forms, semantic badges, styled actions, CSRF and explicit delete confirmation follow the admin shell.
- Permanent deletion only. Later consuming features must add reference protection before deletion of referenced Pages is permitted. No restore/soft-delete system, content/body, media/picker, menus, SEO, public rendering/catch-all, homepage, hierarchy, template, default pages, scheduling or F008 implementation.
- Reviewed and applied 2026_10_17_000001_create_pages_table locally; only creates pages. Existing additive RolesAndPermissionsSeeder run to register pages.manage for review; no Page records seeded.
- Focused command: php artisan test --compact tests/Feature/Admin/PageManagementTest.php tests/Feature/Rbac — 25 passed, 256 assertions. Tests use the isolated database and cover authorization/navigation, creation/defaults, normalization/security, uniqueness/race, update stability, filtering/pagination, deletion, escaping, absence of public routing and RBAC baseline/custom grants.
- Scoped Pint formatting command passed for F007 PHP files. npm.cmd run build compiled the create-form enhancement/styles successfully for review. No full regression or final validation performed.
- Known unrelated Pint findings and Composer advisories left untouched. Manual UI review is pending user confirmation. Feature is not Complete.
1. Objective
Enable authorized administrators to create and manage reusable website page records without hard-coding pages into frontend templates.
F007 establishes the page identity, lifecycle, routing metadata, and administration foundation required by later content features.
It must not become a page builder.
2. Background
Completed foundation:
F001 — Authentication Integration
Provides CMS authentication.
F002 — Roles, Permissions and Super Admin
Provides centralized RBAC and Super Admin authorization bypass.
F003 — Admin Application Shell
Provides reusable admin navigation and responsive layout.
F004 — Admin Dashboard
Provides the CMS administration dashboard.
F005 — Website Settings
Provides site-level configuration.
F006 — Media Library
Provides reusable media storage and administration.
F007 introduces the first reusable content-structure entity:
Page
3. Purpose
Page Management should allow CMS administrators to manage the basic identity and lifecycle of website pages.
Examples of future pages may include:
- Home
- About
- Contact
- Services
- Projects
- Privacy Policy
- Terms
- business-specific information pages
However, the reusable CMS must not seed or hard-code those pages merely because they are common.
4. Core Boundary
F007 manages:
what a page is
F008 — Page Sections manages:
what content is inside a page
F009 — Menu Management manages:
where/how pages appear in navigation
F010 — SEO Management manages:
page-specific search-engine metadata
Keep these responsibilities separate.
5. In Scope
F007 should evaluate and implement the minimum reusable Page Management foundation.
Expected capabilities include:
- create page
- list pages
- edit page identity/settings
- manage page slug
- manage publication status
- delete or archive page according to approved strategy
- prevent invalid/duplicate slugs
- permission-aware admin navigation
- search/filter pages where useful
- server-side pagination where useful
- responsive admin UI
- secure authorization and validation
Exact capabilities must be finalized during Analysis.
6. Out of Scope
F007 does not automatically include:
- page sections/content builder
- rich text editor
- WYSIWYG editor
- block editor
- drag-and-drop page builder
- Elementor-like functionality
- media gallery inside pages
- featured image unless specifically justified
- menus/navigation
- SEO fields
- Open Graph fields
- schema/JSON-LD
- redirects manager
- page revisions/version history
- approval workflow
- scheduled publishing
- multilingual pages
- page translations
- multi-site pages
- reusable content blocks
- nested arbitrary page builder structures
- theme layout editor
- frontend theme design
- frontend page renderer unless explicitly approved
- activity logging
- analytics
These belong to later features or require separate approval.
7. Dependencies
Required completed features:
F001 — Authentication Integration
Status:
Complete
F002 — Roles, Permissions and Super Admin
Status:
Complete
F003 — Admin Application Shell
Status:
Complete
F004 — Admin Dashboard
Status:
Complete
F005 — Website Settings
Status:
Complete
F006 — Media Library
Status:
Complete
8. Relationship to F008
F008 — Page Sections is expected to hold actual structured page content.
Therefore F007 should not create speculative content fields such as:
- body
- content
- html
- blocks
- sections_json
unless Analysis identifies a compelling reason.
Preferred direction:
Page records contain page-level identity/lifecycle metadata only.
9. Relationship to F009
Menu membership must not be stored directly on Page merely for convenience.
Do not add fields such as:
- show_in_menu
- menu_order
- menu_label
unless F009 architecture later explicitly requires them.
Pages and menus are separate concerns.
10. Relationship to F010
Do not add SEO fields to the Page table during F007.
Examples deferred to F010:
- meta title
- meta description
- canonical URL
- robots directives
- Open Graph configuration
- structured data
- sitemap controls
Page title is content identity, not an SEO metadata subsystem.
11. Relationship to F006
Do not add Media Library relationships merely because Media now exists.
Potential page media relationships should be introduced by the consuming feature that actually needs them.
For example:
- Page Section image
- featured image
- banner image
must be explicitly designed later.
Do not add speculative:
featured_media_id
during F007 without a demonstrated requirement.
12. User Roles / Actors
Super Admin
May manage pages through the centralized Super Admin authorization bypass.
Authorized Admin/User
May manage pages when granted the approved Page Management permission.
Unauthorized Authenticated User
Must not access or mutate page administration.
Guest
Must not access page administration.
13. Permission Model
F007 Analysis must determine the minimum appropriate permission model.
Potential options:
Option A
One permission:
pages.manage
Option B
Separate permissions:
- pages.view
- pages.create
- pages.update
- pages.delete
- pages.publish
Preferred initial direction:
one:
pages.manage
unless meaningful business requirements justify granular permissions.
Do not introduce unnecessary permission complexity.
14. Admin Default Permission
Analysis must recommend whether the default Admin role should receive:
pages.manage
Consider that page management directly affects public website content structure.
Preferred conservative direction:
do not automatically grant new permissions to Admin unless explicitly approved.
Super Admin continues to receive access through centralized Gate::before.
Any change to default Admin permissions requires user approval.
15. Page Model
Preferred model:
Page
Preferred table:
pages
Analysis should verify that these names fit current project conventions.
Do not use business-specific model names.
16. Minimum Page Fields
Analysis should evaluate the minimum initial schema.
Likely candidates:
- id
- title
- slug
- status
- timestamps
Do not automatically add fields merely because other CMS platforms have them.
17. Page Title
title represents the administrative/public identity of a page.
Expected characteristics:
- required
- plain text
- validated length
- escaped when displayed
Do not allow raw HTML in page titles.
Analysis should recommend an appropriate maximum length.
18. Slug
Every page is expected to have a reusable URL-friendly slug.
Potential examples:
about
contact
privacy-policy
Analysis must define:
- generation behavior
- validation
- uniqueness
- normalization
- manual editing behavior
Do not allow arbitrary path traversal or executable URL content.
19. Slug Generation
Analysis should determine whether the initial slug should:
A. always be entered manually
B. automatically derive from title but remain editable
C. always automatically derive and never be manually edited
Preferred direction:
derive from title initially and allow intentional manual editing.
The implementation should avoid silently changing existing slugs whenever a title changes.
20. Existing Slug Stability
Once a page exists, changing its title should not automatically change the slug unless the administrator explicitly changes the slug.
Stable slugs reduce broken external/internal links.
Analysis should confirm this behavior.
21. Slug Uniqueness
Page slugs should normally be unique.
Analysis must determine:
- database uniqueness constraint
- validation behavior
- case/normalization behavior
The database should provide a final integrity guarantee where appropriate.
22. Slug Format
Preferred initial scope:
single-segment slugs such as:
about-us
rather than arbitrary paths such as:
company/about/team
unless hierarchical pages are explicitly approved.
Potential safe pattern should generally allow:
- lowercase letters
- numbers
- hyphens
Analysis should recommend exact rules.
23. Reserved Slugs
Some slugs may conflict with application routes or infrastructure.
Analysis must consider whether slugs such as:
- admin
- login
- logout
- register
- storage
- api
or other current/future system paths should be prohibited.
Do not create an enormous speculative reserved-word list.
Recommend the minimum defensible strategy.
24. Page Status
F007 should support a minimal lifecycle.
Potential statuses:
- draft
- published
Preferred initial direction:
exactly these two statuses.
Do not add:
- scheduled
- pending review
- archived
- private
unless requirements justify them.
25. Status Representation
Analysis should determine whether status should use:
- PHP backed enum
- application constants
- validated string
Follow existing project conventions and Laravel simplicity.
Avoid unnecessary abstraction.
26. Draft
A Draft page exists in CMS administration but should not be treated as publicly published content.
Even if F007 does not implement frontend rendering yet, the data model should preserve this distinction for later frontend consumption.
27. Published
Published indicates the page is eligible for public rendering once frontend/page-rendering functionality exists.
Do not assume F007 must expose it publicly immediately.
28. Public Frontend Routing
Analysis must explicitly determine whether F007 should implement dynamic frontend page rendering by slug.
Potential options:
Option A
Admin Page Management only.
Frontend rendering deferred until:
- F008 Page Sections
- and/or later theme integration
Option B
Introduce minimal dynamic page route now.
Preferred direction:
defer public rendering until content/section architecture exists.
A Page with no F008 content does not require a public rendering system merely to complete F007.
Do not create a catch-all frontend route prematurely.
29. Catch-All Route Risk
Avoid introducing routes such as:
/{slug}
without careful consideration.
Catch-all frontend routes can conflict with:
- existing frontend routes
- auth routes
- future modules
- assets/system paths
If frontend routing is deferred, no catch-all route should be introduced in F007.
30. Homepage
Analysis should determine whether F007 needs special homepage support.
Potential approaches include:
- is_homepage
- reserved home slug
- Website Settings-selected homepage
Preferred direction:
do not introduce homepage-specific architecture without an actual current requirement.
A future theme/frontend feature can define homepage selection.
31. Page Hierarchy
Analysis should determine whether F007 supports parent/child pages.
Preferred initial direction:
no hierarchy.
Do not add:
- parent_id
- nested sets
- materialized paths
- tree packages
unless demonstrated requirements justify them.
Flat pages are sufficient initially.
32. Templates
Do not introduce page-template selection automatically.
Fields such as:
template
layout
page_type
should be added only when the theme architecture provides real choices.
F020/F021 are more appropriate for theme-specific behavior.
33. Page Content
Do not put actual structured page content into F007.
F008 should establish Page Sections.
Therefore avoid:
body
content
content_html
content_json
unless separately approved.
34. Excerpt / Summary
Do not add an excerpt solely because blogs often have one.
Generic Pages do not currently require:
excerpt
or:
summary
unless analysis identifies a concrete use.
35. Featured Image
Do not add a featured-image/media relationship during F007 by default.
The Media Library exists, but a relationship should be introduced only when a real page/theme requirement exists.
36. Database Architecture
Expected initial direction:
one pages table.
Analysis must recommend exact columns, types, indexes, defaults, and constraints.
Do not create the migration until implementation approval.
Do not modify unrelated tables.
37. Database Indexes
Potential useful indexes:
- unique slug
- status if filtering justifies it
- timestamps/order if necessary
Do not over-index a small CMS table.
Analysis should recommend only useful indexes.
38. Delete Strategy
F007 Analysis must determine whether pages use:
Permanent deletion
or:
Soft deletes
Consider future relationships:
- Page Sections
- Menus
- SEO records
- internal links
Preferred direction may be soft deletes if page records will quickly become referenced by other CMS entities.
However, do not add soft deletes purely by habit.
Analysis must recommend the simplest future-safe strategy.
39. Current Delete Safety
If permanent deletion is chosen before F008/F009 relationships exist:
- explicit confirmation is required
- later consuming features must introduce reference safety
If soft delete is chosen:
- define whether restoration is part of F007
- avoid creating hidden recovery behavior that has no UI
Deletion semantics must be explicit.
40. Page Duplication
Do not implement Duplicate/Clone Page in F007 unless there is a demonstrated need.
It becomes more useful after F008 sections exist.
41. Search
Analysis should determine whether Page index search is useful.
Potential target:
title
slug
Preferred direction:
simple search by title/slug if low complexity.
No external search infrastructure.
42. Filtering
Potential status filters:
- All
- Draft
- Published
This is likely useful and low complexity.
Analysis should recommend exact initial filters.
43. Sorting
Preferred default:
newest or recently updated pages first.
Analysis should determine the most useful administrative order.
Potential:
updated_at DESC, id DESC
or:
created_at DESC, id DESC
No complex sorting interface is required initially.
44. Pagination
Use server-side pagination.
Analysis should recommend page size.
Potential:
20
25
24
Choose something suitable for a table/list administration screen.
45. Admin Index UI
Expected route concept:
GET /admin/pages
The index should show concise useful information such as:
- title
- slug
- status
- updated date
- actions
Analysis should determine table/list layout.
A dense administrative table is likely more appropriate than card grid for Page records.
46. Create UI
Expected route concept:
GET /admin/pages/create
Create form should manage only approved F007 fields.
Potential:
- Title
- Slug
- Status
Do not include F008 content fields.
47. Edit UI
Expected route concept:
GET /admin/pages/{page}/edit
Edit form should manage approved Page identity/lifecycle fields.
Do not make F007 edit form a page builder.
48. Save Behavior
Page persistence should use:
- server-side validation
- explicit authorized fields
- success feedback
- redirect consistent with project conventions
Do not use unrestricted:
$request->all()
49. Form Requests
Analysis should determine whether to use:
- StorePageRequest
- UpdatePageRequest
Preferred direction:
yes, if validation differs meaningfully around unique slug handling.
Keep authorization centralized and clear.
50. Controller
Potential:
PageController
Likely methods:
- index
- create
- store
- edit
- update
- destroy
Do not add show unless administration needs it.
Keep controller thin.
51. Actions / Services
Ordinary Page CRUD probably does not justify Actions or services initially.
Preferred direction:
controller + Form Requests + model.
Do not introduce repository/service layers without concrete need.
Slug generation may live in straightforward application code where appropriate.
Analysis should recommend the simplest location.
52. Routes
Potential resource-style routes:
GET /admin/pages
GET /admin/pages/create
POST /admin/pages
GET /admin/pages/{page}/edit
PATCH /admin/pages/{page}
DELETE /admin/pages/{page}
Potential names:
- admin.pages.index
- admin.pages.create
- admin.pages.store
- admin.pages.edit
- admin.pages.update
- admin.pages.destroy
Analysis must finalize exact routes.
53. Route Model Binding
Prefer standard Laravel route model binding.
Do not use public slug binding in admin merely because pages have slugs unless there is a clear benefit.
Admin IDs may remain implementation detail through normal model binding.
54. Navigation
After implementation, add:
Pages
to the existing Administration sidebar.
Visibility must follow the approved Page permission.
Expected position should be determined during Analysis.
Likely:
after Media Library
or in another logical position within Administration.
Do not add future module links.
55. Dashboard Integration
Do not automatically modify F004 dashboard merely because Pages exist.
Sidebar navigation is sufficient unless a Page metric provides substantial current value and is explicitly approved.
Preferred direction:
no F004 dashboard change in F007.
56. Media Library Integration
Do not add a media picker to Pages during F007.
F006 intentionally deferred the reusable picker until a consuming feature genuinely needs it.
F008 Page Sections may become the first approved consumer.
Do not force media selection into Page identity CRUD.
57. Authorization
All Page Management routes must require:
- authentication
- admin.access
- approved page-management permission
Sidebar visibility is not security.
Direct URL access must be server-side protected.
58. CSRF
All mutating actions must use normal Laravel CSRF protection.
This includes:
- create
- update
- delete
59. Validation
Analysis must recommend concrete validation for:
Title
Potential:
- required
- string
- reasonable maximum length
Slug
Potential:
- required
- normalized format
- unique
- safe characters
- reasonable maximum length
Status
Allowlisted approved values only.
Do not permit arbitrary status strings.
60. Slug Security
Slug input is untrusted.
Controls should prevent:
- path traversal
- leading/trailing slash abuse
- query fragments
- URL schemes
- raw HTML
- whitespace ambiguity
- duplicate normalized slugs
Do not treat slug as arbitrary URL input.
61. Automatic Slug UX
Analysis should determine the desired create-form experience.
Potential preferred behavior:
- title entered
- slug initially suggested/derived from title
- administrator may edit slug
- server normalizes/validates final slug
Do not require heavy JavaScript.
A small Alpine enhancement may be appropriate if it improves UX.
Do not create a new dependency.
62. Update Slug UX
When editing an existing page:
- show current slug
- do not silently regenerate it when title changes
- administrator must intentionally change slug
This behavior protects URL stability.
63. Slug Change Consequences
Changing an existing slug can break future external links.
F007 does not need a redirects subsystem.
However, UI/help text may make it clear that changing a slug changes the eventual page URL.
Redirect management can be a future capability.
64. Page Status UX
Status should use a clear select or equivalent control.
Avoid toggles whose meaning is ambiguous.
Potential options:
- Draft
- Published
The form should clearly indicate current status.
65. Publication Date
Do not add:
published_at
unless Analysis identifies an actual immediate requirement.
If only Draft/Published state is required, status may be sufficient.
Scheduled publishing is out of scope.
66. Created / Updated Metadata
Normal Laravel timestamps should be sufficient.
Do not add manual audit columns such as:
- created_by
- updated_by
unless current product requirements justify them.
F019 Activity Logs is planned later.
67. Business Neutrality
Page Management must remain reusable.
Do not seed or hard-code:
- Dewatering India pages
- industry-specific page names
- dewatering terminology
- company-specific slugs
Core architecture remains business-neutral.
68. Default Pages / Seeder
Analysis must determine whether Page records should be seeded.
Preferred direction:
no page-content seeder.
A reusable CMS should not automatically create business-specific Home/About/Contact pages.
If generic seed data is unnecessary, do not create a Page seeder.
69. Frontend Rendering Boundary
F007 should not require dynamic public rendering merely to prove Page CRUD works.
If frontend rendering is deferred:
- clearly document it
- ensure Draft/Published semantics are ready for later consumption
Do not consider lack of frontend rendering a defect if it is explicitly deferred.
70. Error Handling
Expected behavior:
- invalid create/update returns validation errors
- unauthorized user receives 403
- missing page uses normal 404
- database failures do not expose implementation details
- success feedback follows F003 conventions
Do not add a separate notification system.
71. Responsive UI
Page Management must work on:
- desktop
- narrow/mobile screens
Review should consider:
- page table/list responsiveness
- actions
- create/edit form
- title/slug input
- status selection
- delete confirmation
- no horizontal page overflow
72. Accessibility
Admin UI should include:
- associated labels
- semantic buttons/links
- keyboard-usable controls
- understandable status labels
- existing visible focus behavior
Do not rely only on color to communicate Draft vs Published.
73. Deletion Confirmation
If deletion is supported:
- require explicit user confirmation
- use authorized DELETE request
- keep implementation consistent with existing admin patterns
Do not create custom modal infrastructure unless necessary.
74. Testing Strategy
Focused F007 tests should verify meaningful behavior without excessive duplication.
Expected categories:
Authorization
- guest redirected
- authenticated unauthorized user gets 403
- Super Admin access
- explicitly authorized user access
Navigation
- Pages visible only when authorized
Create
- valid page can be created
- slug generated/normalized according to approved behavior
- selected status persists
Validation
- title required
- slug validation
- duplicate slug rejected
- invalid status rejected
- reserved slug behavior if approved
- unapproved fields cannot be mass assigned
Update
- title updates
- existing slug remains stable when title alone changes
- explicit slug update works
- unique rule ignores current record correctly
- status updates
Browse
- index displays pages
- search works if implemented
- status filter works if implemented
- pagination/order works
Delete
- authorized deletion follows approved strategy
- unauthorized deletion denied
- confirmation exists in UI
Security
- titles/slugs escaped
- no raw HTML rendering
- no unauthorized/mass-assigned fields
- direct routes remain protected
75. Public Rendering Tests
If frontend rendering is explicitly deferred, F007 should not add tests for:
/{slug}
or public page output.
Do not create tests for functionality outside approved scope.
76. Manual UI Verification
Expected later review:
Desktop
- Pages sidebar navigation
- index/list
- status visibility
- search/filter if implemented
- create page
- slug behavior
- edit page
- status change
- validation feedback
- delete confirmation
- success feedback
Mobile
- list/table adapts correctly
- actions remain usable
- create/edit form fits
- title/slug fields fit
- status selector fits
- delete action usable
- no horizontal overflow
77. Existing Known Issues
Known unrelated project issues remain outside F007.
Pint
- app/View/Components/FrontendLayout.php
- routes/frontend.php
Composer Advisories
Previously documented Composer advisories remain unresolved.
Do not remediate these within F007 unless separately approved.
78. Git / GitHub
Follow:
AGENTS.md
Local read-only Git inspection may be used.
Do not:
- access GitHub
- fetch
- pull
- push
- clone
- commit automatically
- create branches automatically
- use GitHub APIs/connectors
The user handles Git/GitHub manually.
79. Analysis Questions
F007 Analysis must explicitly answer:
1. What exact Page fields should exist?
2. Should F007 remain identity/lifecycle only with content deferred to F008?
3. Should Page / pages be used?
4. What maximum title length is appropriate?
5. What exact slug format and maximum length should apply?
6. Should slug initially derive from title?
7. Should slug remain stable when title changes?
8. Should slug remain manually editable?
9. What reserved slugs are required?
10. Should the database enforce unique slug?
11. Should page hierarchy be deferred?
12. Should homepage-specific behavior be deferred?
13. Should Page templates/layout selection be deferred?
14. Should status contain exactly Draft/Published?
15. Should status use enum/constants/string?
16. Is published_at needed?
17. Should pages use permanent deletion or soft deletes?
18. What search/filter/sort behavior is justified?
19. What pagination size is appropriate?
20. Should default Admin receive Page permission?
21. Is one pages.manage permission sufficient?
22. What exact routes/controller/Form Requests are appropriate?
23. Is any service/action layer needed?
24. Should frontend page rendering be implemented now or deferred?
25. Should F004 Dashboard change?
26. Should F006 Media integration remain deferred to F008?
27. Is any seeder required?
28. What focused tests are required?
29. Is any new package required?
80. Expected Direction
Unless Analysis identifies a compelling reason otherwise, preferred direction:
- Page model
- pages table
- identity/lifecycle only
- fields limited approximately to:
  - id
  - title
  - slug
  - status
  - timestamps
- no body/content field
- no featured image
- no SEO fields
- no menu fields
- no page hierarchy
- no page template field
- Draft/Published only
- slug generated from title on create but editable
- existing slug does not change automatically when title changes
- unique safe slug
- simple search by title/slug
- Draft/Published filter
- server-side pagination
- admin table/list
- one pages.manage permission unless analysis justifies more
- default Admin permission unchanged unless explicitly approved
- thin controller
- Store/Update Form Requests
- no service/repository
- no new package
- no frontend catch-all route
- frontend rendering deferred
- F008 responsible for page content
- F009 responsible for menus
- F010 responsible for SEO
- no F004 dashboard change
- no Media picker yet
81. Decisions Requiring User Approval
Before implementation, user should approve:
- exact Page schema
- slug strategy
- reserved-slug strategy
- page statuses
- deletion strategy
- permission model
- default Admin permission decision
- search/filter/pagination behavior
- frontend rendering deferment
- hierarchy/homepage/template deferment
- Media integration deferment
- any new dependency
82. Implementation Plan
Approved and implemented, including separately approved UI corrections: styled deletion dialog, shared-form spacing, list redirects, stacked-card spacing and dialog action spacing.
83. Implementation Record
Files Created
- app/Enums/PageStatus.php
- app/Models/Page.php
- app/Http/Controllers/Admin/PageController.php
- app/Http/Requests/Admin/StorePageRequest.php
- app/Http/Requests/Admin/UpdatePageRequest.php
- database/migrations/2026_10_17_000001_create_pages_table.php
- resources/views/admin/pages/index.blade.php
- resources/views/admin/pages/create.blade.php
- resources/views/admin/pages/edit.blade.php
- resources/views/admin/pages/partials/form.blade.php
- resources/js/page-slug.js
- tests/Feature/Admin/PageManagementTest.php
Files Modified
- app/Support/Rbac.php
- routes/admin.php
- resources/views/admin/layouts/partials/sidebar.blade.php
- resources/js/app.js
- tests/Feature/Rbac/AdminAccessTest.php
- docs/features/F007_PAGE_MANAGEMENT.md
Files Removed
None.
Migration / Model
One pages migration, applied during implementation. Page/pages contains only id, required title varchar(200), required unique slug varchar(150), required status varchar(20) default draft, created_at and updated_at. Only primary and unique-slug indexes; no foreign keys or content/media/menu/SEO/homepage/hierarchy/template fields. Fillable attributes limited to title/slug/status; PageStatus enum casting; no hidden slug-regeneration events.
Permission
pages.manage only; ordinary users also require admin.access. Admin baseline and existing additive seeder implementation unchanged; customized grants preserved; Super Admin uses centralized Gate::before.
Dependency Changes
None. Composer/npm manifests and lockfiles unchanged.
Tests Added / Updated
PageManagementTest covers all six protected routes, navigation, create/default/status behavior, title/slug security/normalization/reservations/uniqueness/race handling, update stability, browsing/pagination, deletion/escaping/dialog markup and successful/invalid save redirects. AdminAccessTest extends baseline/idempotency/custom-grant checks for pages.manage.
84. Validation Record — 2026-10-17
Full Regression Suite
php artisan test — 102 passed, 795 assertions; no failures.
Focused Tests
php artisan test --compact tests/Feature/Admin/PageManagementTest.php tests/Feature/Rbac — 26 passed, 283 assertions; no failures.
Laravel Pint
Project-wide .\vendor\bin\pint.bat --test reports only pre-existing app/View/Components/FrontendLayout.php and routes/frontend.php findings. Explicit F007-scoped --test passes all 10 created/modified PHP files. Unrelated files left unchanged.
Frontend Build
npm.cmd run build — passed, including page-slug.js. Generated public/build output remains ignored/local, not part of the feature change set.
Migration / Schema Review
Passed. Source and read-only local schema inspection confirm exactly the six approved columns, nullable normal timestamps, required title/slug/status lengths, draft default, unique slug and primary key only, no foreign keys. Rollback definition reviewed; no rollback/reset/destructive command executed during validation. No local Page records edited for validation.
Slug / Status Review
Passed. Server normalization is authoritative; omitted/blank create slug derives from title, while unsafe explicit path/URL syntax is rejected before transformation. Final ASCII single-segment max 150, no truncation/suffixing. All 16 approved reserved slugs centralized and checked after normalization. Current model excluded safely from unique validation; genuine unique collision confirmed before friendly slug feedback, unrelated exceptions rethrown. Title-only/status-only updates preserve slug; explicitly blank update slug fails. Draft/Published only; create default Draft, omitted update status unchanged, invalid values rejected. No published_at/scheduling.
Authorization / Routes Review
Passed. Exactly six ID-bound index/create/store/edit/update/destroy admin routes; each has web/auth/can:admin.access/can:pages.manage, confirmed with filtered route:list -vv. No show/public/catch-all route. Navigation follows Media Library, checks pages.manage and uses admin.pages.* active state. F003 shell and F004 dynamic dashboard application code unchanged.
Controller / Requests / Save Review
Passed. Controller remains thin; Form Requests enforce authorized validated fields, create defaults and update partial semantics. Success create/update redirects to admin.pages.index with approved messages; validation returns old input/errors to the form. No unrestricted request persistence or new service/repository abstraction.
Browse / Form / Delete Review
Passed. Grouped parameterized title OR slug search, All/Draft/Published filter, updated_at DESC/id DESC and 20-item server pagination preserve query parameters. Shared labeled identity forms retain create-only optional Alpine suggestion/manual override, spaced 44px-minimum controls, inline errors and clear actions. One shared native dialog uses escaped data/text binding, dimmed backdrop, selected existing DELETE URL, CSRF/method spoofing, autofocus Cancel, Escape/native modal focus handling and duplicate-submit prevention. Cancel does not submit. Permanent authorized deletion returns to index; missing records use 404 and deletion failures cannot report success. No soft deletes.
Security Review
Passed. Plain-text title validation/escaping, safe slug lexical checks and normalization, reservations/uniqueness, status enum validation, server authorization, CSRF and validated-only mass assignment retained. User titles are not interpolated into executable JavaScript; dialog uses x-text. No raw HTML rendering, new dependencies or internal exception disclosure introduced.
Manual UI Review
Passed — confirmed by user
Pages sidebar/list, desktop/mobile responsiveness, form layout/spacing, title/slug controls and suggestions, status, search/filter, index save redirects/feedback, validation, styled deletion dialog including Cancel/Escape/workflow and requested final spacing are approved by user. Manual browser tests were not repeated during final validation.
Scope / Final Diff Review
Passed. All 12 created and 6 modified files reviewed as F007 scope; no unrelated user work overwritten, debug code, temporary routes, secrets, unexpected package/configuration changes or generated build artifacts in the change set. Only feature documentation updated during final validation. F001–F006 behavior preserved apart from authorized RBAC extension; no F008 implementation or specification created.
85. Known Limitations
- Page content remains deferred to F008; no body/editor/page builder.
- Menu assignment remains deferred to F009 and SEO metadata to F010.
- Public dynamic rendering remains deferred; Published indicates future rendering eligibility only.
- Future consuming modules must implement reference/deletion safeguards before referenced Pages can safely be deleted.
- No homepage configuration, hierarchy, templates, revisions, scheduling, activity logging or media picker/featured image.
- Slug edits do not create redirects.
- Known unrelated Pint findings and previously documented Composer advisories remain outside F007.
86. Completion Record
Implementation: Complete
Acceptance Criteria: 54/54 satisfied
Focused Testing: Passed — 26 tests, 283 assertions
Full Regression: Passed — 102 tests, 795 assertions
User Code/UI Review: Approved
Manual UI Review: Passed — confirmed by user
Final Validation: Passed
Final User Approval: Approved
F007-scoped Pint: Passed
Frontend Build: Passed
Feature Status: Complete
87. Acceptance Criteria
Persistence
- [x] Approved Page model/table is implemented.
- [x] Only approved Page identity/lifecycle fields exist.
- [x] Slug uniqueness is enforced appropriately.
- [x] No speculative Page Sections/content storage is introduced.
- [x] No speculative media/menu/SEO relationships are introduced.
Authorization
- [x] Page administration requires authentication.
- [x] Page routes require approved authorization.
- [x] Unauthorized authenticated users receive 403.
- [x] Super Admin access continues through centralized Gate bypass.
- [x] Pages navigation follows authorization.
Create / Update
- [x] Authorized user can create a valid Page.
- [x] Authorized user can edit Page identity/lifecycle fields.
- [x] Slug behavior follows approved generation/stability rules.
- [x] Existing slug does not silently change when title changes.
- [x] Status accepts only approved values.
- [x] Only validated/approved fields persist.
Validation
- [x] Title validation is server-side.
- [x] Slug validation is server-side.
- [x] Duplicate slugs are rejected.
- [x] Unsafe/invalid slug values are rejected or normalized according to approved design.
- [x] Invalid status values are rejected.
- [x] Arbitrary request fields cannot mutate unapproved values.
Security
- [x] Page titles/slugs are escaped when displayed.
- [x] No raw arbitrary HTML is introduced.
- [x] CSRF protects mutations.
- [x] Direct URL authorization is enforced.
- [x] No page-management action depends only on hidden UI controls.
Browsing
- [x] Authorized users can browse Page records.
- [x] Approved search behavior works.
- [x] Approved status filtering works.
- [x] Ordering is deterministic.
- [x] Server-side pagination works where approved.
- [x] Page index remains responsive.
Delete
- [x] Approved deletion strategy is implemented.
- [x] Deletion requires explicit confirmation.
- [x] Unauthorized deletion is denied.
- [x] Future relationship/reference limitations are documented.
Architecture
- [x] No unnecessary repository/service layer is introduced.
- [x] No page-builder architecture is introduced.
- [x] No new package is introduced without approval.
- [x] F007 remains business-neutral.
- [x] No catch-all frontend route is introduced unless explicitly approved.
Scope Boundaries
- [x] Page content remains deferred to F008.
- [x] Menu functionality remains deferred to F009.
- [x] SEO functionality remains deferred to F010.
- [x] Media picker/featured-image behavior is not introduced without approval.
- [x] No hierarchy/homepage/template system is introduced without approval.
- [x] No F008+ implementation is performed prematurely.
Quality
- [x] Focused F007 tests pass.
- [x] F007-modified PHP files pass Pint.
- [x] Frontend build passes when applicable.
- [x] Manual UI review passes.
- [x] Full regression suite passes during final validation.
- [x] Final diff remains scoped to F007.
88. Required Workflow
F007 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Analysis
→ Architecture / Slug / Lifecycle Recommendations
→ USER APPROVES SIGNIFICANT DECISIONS
→ Implementation
→ Focused Tests
→ USER CODE/UI REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ USER FINAL APPROVAL
→ Mark F007 Complete
→ Identify F008 only
→ ASK BEFORE F008 ANALYSIS
Do not automatically proceed to F008.
89. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F007:
- inspect only Page/RBAC/admin-route/admin-UI conventions
- do not research page-builder packages
- do not inspect future modules unnecessarily
- do not design F008 Page Sections during F007
- do not implement frontend catch-all routing prematurely
- avoid hierarchy/template/versioning architecture
- avoid new dependencies
- run focused tests first
- perform full regression only after user UI review
- keep reports concise
- preserve approval gates
90. F007 Completion Rule
F007 may be considered ready for final approval only when:
- approved Page architecture is implemented
- Page CRUD/lifecycle behavior works
- slug behavior is correct and safe
- authorization is correct
- Pages navigation is permission-aware
- deletion follows approved behavior
- no Page Sections/page-builder overreach exists
- no Menu/SEO architecture leaks into F007
- no unnecessary package is introduced
- migration is safe
- focused tests pass
- manual UI review passes
- final regression validation passes
- final diff remains F007-only
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete
