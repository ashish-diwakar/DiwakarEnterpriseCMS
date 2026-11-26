Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\features\F005_WEBSITE_SETTINGS.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/features/F005_WEBSITE_SETTINGS.md
────────────────────────────────────
F005 — Website Settings
Status:
Complete
1. Objective
Introduce reusable CMS-managed website settings for site-level configuration.
F005 should allow authorized administrators to manage common public website information without hard-coding business-specific values into frontend templates.
The settings foundation should remain:
- reusable
- business-neutral
- secure
- simple to administer
- easy for future frontend themes to consume
- extensible without becoming a generic configuration engine
2. Background
The following foundation is complete.
F001 — Authentication Integration
Provides secure CMS authentication.
F002 — Roles, Permissions and Super Admin
Provides:
- roles
- permissions
- Super Admin
- authorization foundation
F003 — Admin Application Shell
Provides reusable admin navigation/layout.
F004 — Admin Dashboard
Provides the current CMS dashboard.
F005 introduces the first reusable website-content/configuration capability.
3. Purpose of Website Settings
Website Settings should manage values that generally apply across an entire public website.
Examples include:
- website/business name
- public contact information
- physical address
- branding references
- social links
- footer information
- general public-facing website configuration
These settings should be editable through the CMS rather than hard-coded into a specific frontend theme.
4. Core Principle
Website Settings are:
public-facing website configuration
not:
application infrastructure configuration.
Do not use Website Settings to store:
- database credentials
- API secrets
- SMTP passwords
- cloud access keys
- OAuth secrets
- encryption keys
- application environment variables
- authentication configuration
- server configuration
Sensitive infrastructure settings remain outside normal CMS-managed website settings.
5. In Scope
F005 should evaluate and implement the minimum reusable settings model needed for future websites.
Potential settings categories include:
General
- website name
- business/company name
- short site description/tagline
Contact
- public email
- primary phone
- secondary phone where useful
- public address
Branding
- logo reference where supported
- favicon reference where supported
Social
Potential public links such as:
- Facebook
- Instagram
- LinkedIn
- YouTube
- X/Twitter
Only include fields that have practical reuse value.
Footer
- footer/copyright text
Exact initial fields must be finalized during Analysis.
6. Out of Scope
F005 does not include:
- Media Library implementation
- image upload infrastructure
- SEO Manager
- page-specific metadata
- theme management
- frontend theme implementation
- navigation/menu management
- SMTP configuration
- email provider credentials
- analytics configuration
- Google Analytics
- Tag Manager
- API integrations
- payment configuration
- database configuration
- environment variables
- application secrets
- multi-site settings
- per-language settings
- per-user preferences
- dynamic custom field builder
- generic key/value settings editor
- arbitrary JSON configuration editor
- theme customization engine
- advanced branding editor
- CSS customization
Future features may integrate with Website Settings after they exist.
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
8. Media Dependency
F006 — Media Library has not yet been implemented.
Therefore F005 must not prematurely create a custom file-management architecture.
Analysis must determine how branding fields such as:
- logo
- favicon
should be handled before Media Library exists.
Preferred options include:
- defer actual media selection until F006
- allow nullable placeholder/reference fields only if justified
- avoid implementing direct upload logic in F005
Do not duplicate future Media Library functionality.
9. User Roles / Actors
Super Admin
May manage Website Settings through the existing Super Admin authorization bypass.
Admin
May manage Website Settings only if granted the approved settings permission.
Unauthorized Authenticated User
Must not access or modify Website Settings.
Guest
Must not access Website Settings administration.
10. Permission Model
F005 should introduce only the minimum permission required for Website Settings.
Preferred permission:
settings.manage
Analysis should determine whether separate:
settings.view
and:
settings.manage
permissions provide real value.
Preferred direction:
one permission:
settings.manage
because settings administration is primarily an edit capability.
Do not create multiple granular settings permissions without need.
11. Default Role Assignment
Analysis should recommend whether the existing Admin role should receive:
settings.manage
by default.
Preferred conservative direction:
do not automatically grant new sensitive permissions to Admin unless the feature's business purpose clearly justifies it.
Super Admin will retain access through centralized Gate::before.
The user must approve any change to Admin default permissions.
12. Admin Navigation
After F005 implementation, the admin sidebar may gain a new entry:
Settings
Expected visibility:
@can('settings.manage')
Exact route name should follow existing admin route conventions.
Do not add navigation for future features.
13. Website Settings Access
All settings routes must require:
- auth
- can:admin.access
- appropriate settings permission
Do not rely solely on sidebar visibility.
Direct URL access must remain protected.
14. Settings Architecture
Analysis must determine the simplest appropriate persistence model.
Potential approaches include:
Option A — Single WebsiteSettings Row/Table
A structured table with explicit columns.
Potential advantages:
- strongly typed
- easy validation
- predictable schema
- easy frontend consumption
Potential disadvantages:
- migration required when new fields are added
Option B — Key/Value Settings Table
Generic settings such as:
key
value
Potential advantages:
- flexible
Potential disadvantages:
- weaker typing
- more validation complexity
- easier to become an uncontrolled configuration store
Preferred Direction
For the initial reusable CMS, prefer a structured and maintainable approach rather than an unrestricted generic configuration engine.
Analysis must recommend the best option based on actual project needs.
15. Generic Settings Engine Restriction
Do not build a generic arbitrary settings platform unless there is a demonstrated requirement.
Avoid:
- administrator-created setting keys
- arbitrary type definitions
- custom settings schema builder
- nested JSON settings editor
- dynamic settings form generator
F005 should implement known website settings.
16. Singleton Behavior
Website Settings are expected to represent one website configuration.
Analysis should determine whether the application should use:
- one singleton settings record
- deterministic first/only row
- another explicit one-site mechanism
Do not design multi-site behavior.
The application is currently single-site.
17. Data Model
The settings model should have a clear domain name.
Potential:
WebsiteSetting
or:
SiteSetting
Analysis should recommend one name and use it consistently.
Avoid vague names such as:
Setting
if that is likely to become ambiguous later.
18. Initial Field Set
Analysis should recommend the smallest useful initial set.
Potential fields:
- site_name
- business_name
- tagline
- email
- phone
- secondary_phone
- address fields or a reusable address representation
- footer_text
- selected social URLs
- logo/media reference when F006 integration becomes available
- favicon/media reference when F006 integration becomes available
Do not automatically include every imaginable company field.
19. Address Strategy
Analysis should determine whether address should be stored as:
Structured Fields
Potential:
- address line 1
- address line 2
- city
- state
- postal code
- country
or:
Single Text Address
Prefer the simplest approach that supports likely reuse.
Do not over-engineer international address modeling without requirement.
20. Contact Fields
Public contact information should be validated appropriately.
Potential rules include:
Email:
valid email format where present.
Phone:
reasonable length and format.
Do not enforce overly strict country-specific phone formatting unless required.
21. Social URLs
Social links should be:
- optional
- validated as URLs when supplied
Do not require every social network.
Do not allow arbitrary executable/script schemes.
Use safe URL validation.
22. Website Name vs Business Name
Analysis should determine whether separate:
- site name
- business name
are genuinely needed.
For many websites they may be identical.
Avoid duplicate fields unless they serve distinct frontend needs.
23. Tagline
A tagline or short description may be useful for:
- header/footer
- frontend branding
- basic site context
Do not confuse this with SEO meta description.
SEO-specific fields belong to F010.
24. SEO Boundary
F005 should not implement:
- meta title
- meta description
- robots directives
- canonical URL
- Open Graph settings
- JSON-LD management
- sitemap configuration
These belong to F010 — SEO Management.
Do not mix Website Settings and SEO architecture prematurely.
25. Branding Boundary
F005 may define where branding references belong conceptually.
Actual media upload/selection should preferably integrate with:
F006 — Media Library.
Do not create an independent image-upload system solely for Website Settings.
26. Frontend Consumption
F005 should establish a clean way for future frontend templates to access Website Settings.
Analysis should determine whether settings should be exposed through:
- view composer
- service/helper
- explicit controller data
- cached model accessor
- another Laravel-native mechanism
Do not make every Blade view query the database independently.
Do not introduce global state unnecessarily.
27. Current Frontend Usage
F005 may optionally integrate only values already appropriate to the current minimal frontend if doing so is low-risk.
However:
the main objective is the CMS settings foundation.
Do not expand F005 into frontend-theme implementation.
F020/F021 will handle full theme/frontend integration.
28. Caching
Do not introduce settings caching automatically.
Settings will be read frequently but the application is still small.
Analysis may note future caching opportunities.
Do not add:
- Redis
- persistent cache infrastructure
- cache invalidation architecture
without demonstrated need.
29. Settings Form
F005 should provide a clean admin form for editing settings.
Potential grouping:
- General
- Contact
- Social
- Footer/Branding
Keep the UI straightforward.
Do not create tabs unless the number of fields justifies them.
30. Save Behavior
Settings updates should use:
- server-side validation
- authorization
- explicit field persistence
- success feedback
Do not use unrestricted:
$request->all()
for persistence.
31. Update Strategy
Analysis should determine whether settings use:
- one update endpoint
- category-specific endpoints
Preferred direction:
one settings screen and update action unless the field set becomes too large.
Avoid unnecessary controller fragmentation.
32. Form Request
Use a dedicated Form Request if appropriate for Website Settings validation.
Potential:
UpdateWebsiteSettingsRequest
This is preferable to placing extensive validation directly in the controller.
33. Controller
A thin controller is expected.
Potential:
WebsiteSettingsController
Responsibilities may include:
- display settings
- update settings
Do not place business/data abstraction layers around simple settings persistence without need.
34. Route Design
Expected conceptual routes may include:
GET /admin/settings
PUT/PATCH /admin/settings
Potential route names:
admin.settings.edit
admin.settings.update
Exact convention must be determined during Analysis from current admin patterns.
35. Settings Creation
The application should handle a fresh installation where the settings record does not yet exist.
Analysis should recommend a deterministic approach.
Potential patterns:
- firstOrCreate
- seeder
- controlled singleton creation
Do not require manual direct database insertion for normal application use.
36. Seeder
A Website Settings seeder may be used only if it provides meaningful baseline setup.
Do not hard-code Dewatering India-specific business values in reusable CMS seed data.
Safe generic/default values may be acceptable.
Analysis should determine whether a seeder is needed.
37. Business Neutrality
Core Website Settings must remain reusable.
Do not hard-code:
- Dewatering India name
- pump terminology
- dewatering-specific contact values
- industry-specific labels
into the reusable CMS architecture.
Dewatering India content belongs to later implementation/theme/content work.
38. Database Impact
F005 is expected to require a schema change.
The exact table/columns must be proposed during Analysis.
Expected direction:
one website-settings table.
Do not create migration until implementation is explicitly approved.
Do not modify unrelated tables.
39. Migration Safety
The F005 migration should:
- create only required settings structure
- avoid destructive changes
- provide reasonable nullable/default behavior
- define appropriate field lengths/types
No existing production-like data should be destroyed.
40. Indexes
Do not create unnecessary indexes for a singleton settings table.
If a key/value design is recommended instead, uniqueness/index requirements must be analyzed.
41. Dependency Impact
Expected:
None
Use existing Laravel functionality.
Do not install:
- settings packages
- form-builder packages
- media packages
- admin theme packages
unless Analysis identifies a compelling gap and the user explicitly approves it.
42. Security Requirements
Follow:
docs/06_SECURITY_RULES.md
F005-specific security requirements include:
- settings routes protected server-side
- only authorized users may update settings
- validate all external input
- prevent mass assignment
- escape rendered public values
- reject unsafe URLs
- no secrets in Website Settings
- no environment configuration exposure
- no raw arbitrary HTML unless separately designed and sanitized
43. Sensitive Configuration Restriction
The settings form must never expose or accept:
- APP_KEY
- database password
- mail password
- API secret keys
- OAuth credentials
- cloud keys
- GitHub tokens
- payment secrets
Website Settings are public/business configuration only.
44. HTML Content
Avoid rich HTML settings in F005 unless required.
Fields such as footer text should default to plain text.
Do not render administrator-managed content as raw HTML without sanitization architecture.
45. Validation Requirements
Analysis should define validation for each approved field.
Potential examples:
Site/business name:
- required or nullable based on business rule
- reasonable maximum length
Email:
- nullable or required
- valid email
Phone:
- nullable
- reasonable max length
URLs:
- nullable
- valid URL
- safe schemes
Footer text:
- nullable
- reasonable max length
Do not define arbitrary limits without justification.
46. Authorization Tests
Focused tests should verify:
Guest:
cannot access settings.
Authenticated user without settings.manage:
403.
Authorized user:
can view settings.
Authorized user:
can update settings.
Super Admin:
can manage settings through centralized authorization bypass.
47. Validation Tests
Focused tests should verify meaningful invalid inputs such as:
- invalid email
- invalid URL
- overlong values where relevant
Do not create excessive one-test-per-rule coverage if grouped tests remain clear.
48. Persistence Tests
Tests should verify:
- valid settings persist
- existing settings update rather than creating duplicate configuration records
- unapproved fields cannot be mass-assigned
- fresh application settings record can be created safely if that is the approved design
49. Navigation Tests
If Settings is added to admin navigation:
- authorized user sees Settings link
- unauthorized user does not see Settings link
Navigation visibility remains presentation-only.
Direct routes must still enforce permission.
50. Dashboard Integration
F004 dashboard should not automatically receive a Website Settings card merely because F005 exists.
Analysis may recommend whether a Settings quick link/card adds real value.
Preferred direction:
avoid changing F004 unless useful and explicitly included in F005 scope.
Sidebar navigation may be sufficient.
51. Manual UI Verification
Expected manual review:
Authorized User
- Settings navigation visible
- settings form loads
- grouped fields are understandable
- save action works
- success message displays
- existing values persist after reload
Unauthorized User
Automated tests should cover route denial; manual testing is optional.
Mobile
- form fields fit narrow viewport
- labels remain readable
- save button remains usable
- no horizontal overflow
52. Existing Known Issues
Known unrelated project issues remain outside F005:
Pint
- app/View/Components/FrontendLayout.php
- routes/frontend.php
Composer Advisories
Previously documented security advisories remain unresolved.
Do not remediate these within F005 unless separately approved.
53. Git / GitHub
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
54. Analysis Questions
F005 Analysis must explicitly answer:
1. What exact initial Website Settings fields should exist?
2. Should site name and business name be separate?
3. What address structure is appropriate?
4. Which social URLs should be included initially?
5. Should logo/favicon fields be deferred until F006?
6. Should settings use a structured singleton table or key/value architecture?
7. What should the model be named?
8. How should a missing settings record be created safely?
9. Is a seeder necessary?
10. What permission should protect Website Settings?
11. Should Admin receive that permission by default?
12. What routes/controller/request classes are appropriate?
13. How should future frontend templates consume settings without querying in every Blade view?
14. Is caching necessary now?
15. Should F004 dashboard change at all?
16. What focused tests are required?
17. Is any new dependency required?
55. Expected Direction
Unless Analysis identifies a compelling reason otherwise, preferred direction:
- structured WebsiteSetting model/table
- one settings record
- explicit typed columns
- one admin edit/update screen
- settings.manage permission
- Super Admin automatically allowed through Gate bypass
- no direct media upload until F006
- no SEO fields
- no secrets
- no generic settings engine
- no new package
- no caching yet
- no F004 dashboard modification unless justified
56. Decisions Requiring User Approval
Before implementation, the user should approve:
- settings persistence architecture
- initial field list
- permission model
- Admin default permission decision
- logo/favicon handling before F006
- missing-record creation strategy
- frontend consumption strategy if introduced during F005
Any new package requires separate approval.
57. Implementation Plan
Status:
Complete
Expected implementation plan size:
approximately 5–7 meaningful steps.
Do not implement until approved.
58. Implementation Record
Files Created
- database/migrations/2026_10_06_000001_create_website_settings_table.php
- app/Models/WebsiteSetting.php
- app/Http/Controllers/Admin/WebsiteSettingsController.php
- app/Http/Requests/Admin/UpdateWebsiteSettingsRequest.php
- resources/views/admin/settings/edit.blade.php
- tests/Feature/Admin/WebsiteSettingsTest.php
Files Modified
- app/Support/Rbac.php
- database/seeders/RolesAndPermissionsSeeder.php
- routes/admin.php
- resources/views/admin/layouts/partials/sidebar.blade.php
- tests/Feature/Admin/DashboardTest.php
- tests/Feature/Rbac/AdminAccessTest.php
- docs/features/F005_WEBSITE_SETTINGS.md
Files Removed
None.
Migration
- Created website_settings singleton table for approved Website Settings fields.
Model
- Created WebsiteSetting model with SINGLETON_ID and singleton finder.
Permission
- Added settings.manage permission. Default Admin role does not receive it automatically.
Dependency Changes
Expected:
None
Final:
None.
Tests Added / Updated
- Added focused Website Settings feature coverage.
- Updated dashboard permission-count expectation for the new web-guard permission.
- Updated RBAC default-permission coverage and seeder preservation coverage.
59. Validation Record
Focused Tests
- php artisan test --filter=WebsiteSettingsTest
  Result: 12 passed, 61 assertions.
- php artisan test tests/Feature/Admin/WebsiteSettingsTest.php tests/Feature/Admin/DashboardTest.php tests/Feature/Admin/AdminShellTest.php tests/Feature/Rbac/AdminAccessTest.php tests/Feature/Rbac/RolePermissionManagementTest.php
  Previous result: 37 passed, 148 assertions.
Regression Suite
- php artisan test
  Result: 73 passed, 242 assertions.
Laravel Pint
- .\vendor\bin\pint.bat --test
  Result: Failed only for known unrelated files app\View\Components\FrontendLayout.php and routes\frontend.php.
- .\vendor\bin\pint.bat --test app/Models/WebsiteSetting.php app/Http/Controllers/Admin/WebsiteSettingsController.php app/Http/Requests/Admin/UpdateWebsiteSettingsRequest.php app/Support/Rbac.php database/seeders/RolesAndPermissionsSeeder.php database/migrations/2026_10_06_000001_create_website_settings_table.php tests/Feature/Admin/WebsiteSettingsTest.php tests/Feature/Admin/DashboardTest.php tests/Feature/Rbac/AdminAccessTest.php routes/admin.php
  Result: Passed for F005 PHP files.
Frontend Build
- npm.cmd run build
  Result: Passed.
Migration Review
Passed: migration creates only the approved website_settings table and approved fields, is non-destructive, has no unnecessary indexes, and contains no logo/favicon or SEO columns.
Authorization Review
Passed: settings routes require auth, admin.access, and settings.manage; Super Admin continues through Gate::before.
Security Review
Passed: validation is server-side, persistence uses validated fields only, unsafe URLs and unapproved fields are rejected, footer text is escaped, and no secrets/media/SEO fields were added.
Manual UI Review
Passed — confirmed by user.
Final Diff Review
Passed: changes remain scoped to F005; no debugging code, temporary routes, secrets, dependency changes, generated build artifacts, F006 implementation, F010 implementation, or frontend/theme integration found.
60. Known Limitations
Expected F005 limitation:
logo/favicon media selection may remain deferred until:
F006 — Media Library
SEO-specific settings remain deferred until:
F010 — SEO Management
Frontend/theme-specific presentation remains deferred to later frontend/theme features.
Additional limitations:
- Logo and favicon media selection remain deferred until F006 Media Library.
- SEO-specific settings remain deferred until F010 SEO Management.
- Frontend/theme-specific settings consumption remains deferred to later frontend/theme features.
61. Completion Record
Implementation:
Complete
Acceptance Criteria:
38/38 satisfied
Focused Testing:
Passed
User Review:
Approved
Manual UI Review:
Passed — confirmed by user
Final Validation:
Passed
Final User Approval:
Approved
Feature Status:
Complete
62. Acceptance Criteria
Persistence
- [x] Website Settings use one approved reusable persistence model.
- [x] Settings can be created safely on a fresh installation.
- [x] Updating settings does not create uncontrolled duplicate configuration records.
- [x] No generic arbitrary setting-key system is introduced unless explicitly approved.
Authorization
- [x] Settings administration requires authentication.
- [x] Settings administration requires approved authorization.
- [x] Unauthorized authenticated users receive 403.
- [x] Super Admin retains access through centralized Gate bypass.
- [x] Settings sidebar visibility follows authorization.
Form / Validation
- [x] Settings form displays approved fields only.
- [x] Valid settings persist.
- [x] Invalid email is rejected where applicable.
- [x] Invalid URLs are rejected.
- [x] Server-side validation is used.
- [x] Arbitrary request fields cannot update sensitive/unapproved values.
Security
- [x] Website Settings contain no infrastructure secrets.
- [x] No environment variables are exposed.
- [x] Dynamic values use escaped output.
- [x] No raw arbitrary HTML is introduced.
- [x] No unsafe external URL handling is introduced.
Architecture
- [x] No unnecessary repository layer is introduced.
- [x] No generic settings engine is introduced without need.
- [x] No new package is introduced without approval.
- [x] No media-upload subsystem is duplicated before F006.
- [x] No SEO architecture is introduced before F010.
- [x] Website Settings remain business-neutral.
Admin UI
- [x] Settings page uses the F003 admin shell.
- [x] Settings navigation appears only when authorized.
- [x] Form is responsive.
- [x] Save feedback is clear.
- [x] Existing values remain visible after save/reload.
Quality
- [x] Focused F005 tests pass.
- [x] Migration is reviewed.
- [x] F005-modified PHP files pass Pint.
- [x] Frontend build passes when applicable.
- [x] Manual UI review passes.
- [x] Full regression suite passes during final validation.
- [x] Final diff remains scoped to F005.
63. Required Workflow
F005 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Analysis
→ Architecture / Field Recommendations
→ USER APPROVES SIGNIFICANT DECISIONS
→ Implementation
→ Focused Tests
→ USER CODE/UI REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ USER FINAL APPROVAL
→ Mark F005 Complete
→ Identify F006 only
→ ASK BEFORE F006 ANALYSIS
Do not automatically proceed to F006.
64. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F005:
- inspect only settings/RBAC/admin-shell-relevant files
- do not research settings packages unless needed
- avoid generic configuration-engine design
- defer media implementation to F006
- defer SEO to F010
- avoid frontend-theme redesign
- run focused tests first
- perform full regression only after user review
- keep reports concise
65. F005 Completion Rule
F005 may be considered ready for final approval only when:
- approved settings architecture is implemented
- approved fields are manageable through CMS
- authorization is correct
- validation is correct
- settings persist safely
- no secrets are exposed
- no generic settings-engine overreach exists
- F003 shell remains intact
- F002 authorization remains intact
- migration is safe
- no unnecessary dependency is introduced
- media functionality is not duplicated prematurely
- focused tests pass
- manual UI review passes
- final regression validation passes
- final diff remains F005-only
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete


## 66. Multiple Contact Entries Enhancement — 26 November 2026

Historical contact-only preparation record. Its pending statuses and commands describe the earlier iteration, not the final state. The accepted consolidated completion evidence is recorded in section 67; do not repeat the historical installation.

Enhancement status: **Implemented — awaiting Windows PHPUnit verification, manual database installation and browser acceptance.** The original F005 completion record and its 38/38 accepted criteria remain historical and unchanged. F001–F009 remain Complete. This enhancement is not yet accepted. Public Page Rendering and F010 are not implemented here.

### Approved behavior and administration

- Replace the three fixed contact controls with separately ordered Email addresses and Phone numbers. Add, edit, label, remove with confirmation, move up/down, choose a primary and activate/deactivate individual entries within the existing Save Settings workflow.
- Allow 50 entries per type (100 total); empty collections are valid. Phone types are Mobile, Landline, Toll-free and Other. Labels are optional, maximum 120 characters.
- At most one active primary per type. Choosing a new primary clears the previous flag transactionally. Deactivating/removing a primary leaves that type without a primary; the administrator must explicitly choose a replacement. No automatic promotion.
- Alpine manages unsaved rows without network requests or page reloads. A themed native HTML dialog confirms removal; Cancel/Escape keeps the row. Unsaved changes trigger navigation protection. Indexed errors and old input survive validation redirects. A hydration flag prevents an unloaded/disabled JavaScript editor from accidentally submitting an empty replacement collection.
- Retain General, Address, Social Links and Footer. Existing settings.manage authorization, centralized Super Admin bypass, routes and singleton identity/persistence remain unchanged. No permissions, dependencies or unrelated feature changes.

### Additive database design and persistence

New migration: database/migrations/2026_11_26_000002_create_website_contacts_table.php. No historical migration was rewritten or executed by Codex.

website_settings retains all original columns and adds nullable contacts_initialized_at and unsigned contacts_version (default 0). New InnoDB table website_contacts:

| Column | Definition / purpose |
| --- | --- |
| id | Generated bigint primary key |
| website_setting_id | Restrictive foreign key to website_settings.id |
| type | Server-derived email or phone; varchar(20) |
| subtype | Nullable varchar(20); required approved subtype for phones, null for emails |
| label | Nullable varchar(120) |
| value | Display value, varchar(255); HTTP phone limit 80 |
| normalized_value | Canonical comparison/link value, varchar(255) |
| is_primary / is_active | Booleans, default false / true |
| sort_order | Unsigned integer, server-derived list position starting at 1 per type |
| primary_type | Nullable generated varchar(20): type when primary, otherwise NULL |
| created_at / updated_at | Laravel timestamps |

Unique keys enforce (website_setting_id, type, normalized_value) and (website_setting_id, primary_type). An index covers owner/type/order/id. Database primary uniqueness complements server validation; the active-primary rule is application-enforced, not a database CHECK constraint.

General settings and contact changes share one transaction. The existing fixed-ID singleton upsert and locked ID-1 row serialize collection updates. A version mismatch rejects stale submissions and rolls back their general settings changes. Validate all submitted IDs against the locked owner's collection and server-derived type before changing records. Client owner/type/order/canonical fields are rejected. Related rows retain their IDs; omitted rows are deleted only from the submitted owner's complete collection. Transaction-local temporary canonical keys permit valid address swaps without unique-key collisions; failure rolls back the temporary keys, primary flags and general settings together.

MariaDB DDL is not transactionally atomic: preserve a verified recovery backup and stop for review on any migration failure. Do not blindly rerun or roll back a partially applied migration. The down method removes only the new contact table/metadata and retains legacy columns; do not use it after normalized-only edits without preserving those edits first. MariaDB-specific concurrent-write behavior and this migration's MariaDB installation remain unverified by Codex.

### Validation and safe public access

- Email: one ASCII FILTER_VALIDATE_EMAIL address, maximum 255 characters; reject lists, controls, unsafe delimiters and display-name syntax. Trim outer ASCII spaces; lowercase the complete address for duplicate comparison, while retaining entered case for display. This deliberate policy treats local-part case variants as duplicates. Internationalized email is not supported.
- Phone: one syntactically validated number with 7–15 digits, optional leading +, balanced parentheses and ordinary spaces/dots/hyphens. Preserve domestic leading zeroes. Landline/Other may have x/ext/ext./extension followed by 1–8 extension digits. Reject controls, URI schemes, lists, unsupported characters, ambiguous international (0) notation and invalid extensions. No country is inferred, no allocation/real-world reachability check is performed, and domestic/international equivalents are not deduplicated unless their canonical forms match.
- Only contact-value fields bypass Laravel's automatic trimming so validation can reject leading/trailing controls instead of silently removing them. Persistence deliberately trims outer ASCII spaces only.
- WebsiteSetting exposes contacts(), activeEmailContacts(), activePhoneContacts(), primaryEmail() and primaryPhone(). Ordered active collections exclude inactive records; primary helpers return null when no explicit active primary exists. Uninitialized collections return empty/null. There is no fallback to legacy values.
- WebsiteContact::mailtoUrl() and telUrl() return safe validated URLs, or null for inactive/invalid/mismatched stored values. Mailbox components are percent-encoded. Consumers must escape labels/values and render links only for non-null URLs. The repository had no public frontend consumers of the three legacy fields, so no theme templates were changed. Public rendering, APIs and SEO remain deferred.

### Legacy preservation and explicit initialization

Original public_email (varchar 255), primary_phone and secondary_phone (varchar 50 each) remain untouched snapshots; they are no longer editable through the HTTP settings request. Read-only preserved values remain visible while initialization is pending. Other settings may still be saved.

The manual settings:initialize-contacts command defaults to writing only when explicitly run without --dry-run. Its --dry-run path performs no row-lock, collection or initialization-marker writes. It does not create a missing Website Settings singleton; save General settings first if no ID-1 row exists.

Supported legacy separators: comma, semicolon and CR/LF for email; comma, vertical bar and CR/LF for phone. Slash and ambiguous free text require reconciliation. Skip empty separators; retain first meaningful occurrence/order; collapse canonical duplicates. The first identifiable public email and first primary_phone become primary; secondary_phone does not invent primary intent. Any invalid/ambiguous entry blocks the entire conversion, with sanitized field/index diagnostics and every original raw column retained.

A protected --reconcile= JSON file outside public/ may explicitly supply complete emails and phones arrays, maximum 256 KiB, with label/value/is_active/is_primary and phone subtype, without contact IDs or storage metadata. Review all original values privately and account for each before approving this override. Reconciliation does not erase original columns. Existing uninitialized normalized rows stop initialization for review. The marker and rows are committed together; subsequent initialization is a no-op, including after intentional deletion of every contact. Normalized contacts become authoritative after initialization; legacy snapshots are not synchronized or resurrected.

### Files changed

Created:

- app/Actions/Settings/InitializeWebsiteContacts.php
- app/Actions/Settings/SyncWebsiteContacts.php
- app/Actions/Settings/UpdateWebsiteSettings.php
- app/Console/Commands/InitializeWebsiteContacts.php
- app/Models/WebsiteContact.php
- app/Support/ContactValue.php
- app/Support/LegacyWebsiteContacts.php
- app/Support/WebsiteContactsInput.php
- database/migrations/2026_11_26_000002_create_website_contacts_table.php
- resources/js/website-contacts.js
- resources/views/components/admin/website-contacts.blade.php
- tests/Feature/Admin/WebsiteContactsTest.php
- tests/JavaScript/website-contacts.test.js
- tests/Unit/ContactValueTest.php

Modified:

- app/Http/Controllers/Admin/WebsiteSettingsController.php
- app/Http/Requests/Admin/UpdateWebsiteSettingsRequest.php
- app/Models/WebsiteSetting.php
- bootstrap/app.php
- resources/js/app.js
- resources/views/admin/settings/edit.blade.php
- tests/Feature/Admin/WebsiteSettingsTest.php
- docs/features/F005_WEBSITE_SETTINGS.md

### Actual verification and pending gates

| Check | Result |
| --- | --- |
| Accepted pre-enhancement full regression | Historical user evidence: 191 tests / 1,421 assertions; not a result for these changes |
| PHP syntax | Passed, 16 changed/new non-Blade PHP files |
| Scoped Pint --test | Passed, 16 changed/new non-Blade PHP files |
| JavaScript editor tests | Passed, 5 tests: collection limit, primary switching, removal confirmation, ordering/identity and unsaved navigation |
| Standalone offline SQLite / normalization checks | Passed, 43 checks: 26 normalization cases plus additive schema, generated unique keys, FK, safe links, raw preservation, dry run, initialization, value swap, stale-save rollback, intentional empty state, no resurrection and in-memory down behavior; no Laravel bootstrap, .env or network DB |
| Focused PHPUnit | Blocked before bootstrap: Cannot open bootstrap script tests/bootstrap.php, consistent with the known runner file-access discrepancy |
| Full regression | Not run because the same PHPUnit startup blocker prevents the focused suite; requires ordinary Windows execution |
| Production frontend build | Passed: Vite 7.3.6, 60 modules; programmatic build used a unique empty temporary envDir, avoiding real .env access |
| Live database / browser acceptance | Pending manual installation and review; no live database accessed or changed |

The 18 new feature test methods cover authorization, collections/labels/types/order, primary policy, unsafe/duplicate input, empty collections, stale versions/IDOR, metadata rejection/limits, legacy conversion/reconciliation/idempotence, safe links/escaping, unique constraints and injected transactional failure. ContactValueTest supplies 26 normalization cases and one pure legacy-parser case. Existing 15 WebsiteSettingsTest methods retain singleton, authorization and unrelated-settings coverage with the updated contact request contract. Source inventory is not a PHPUnit pass count.

The formatter could not write through its runner permission check; the reported formatting changes were applied using normal workspace edits and rechecked with Pint --test. No permissions or vendor files were changed. No sandbox investigation or full-suite retry was performed.

### Consolidated ordinary-PowerShell PHPUnit verification

Run from an ordinary Windows PowerShell session after reviewing the diff. This uses the existing isolated bootstrap; never substitute a real database. The preliminary createApplication check also guards the complete test connection list before any suite migrations.

~~~powershell
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath 'C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms'
@'
<?php
require 'tests/bootstrap.php';
$test = new Tests\Feature\Admin\WebsiteContactsTest('test_contacts_require_existing_settings_authorization');
$app = $test->createApplication();
if ($app['config']->get('database.default') !== 'sqlite'
    || $app['config']->get('database.connections.sqlite.database') !== ':memory:'
    || array_keys($app['config']->get('database.connections')) !== ['sqlite']) {
    throw new RuntimeException('STOP: isolated SQLite configuration required');
}
echo "Isolation verified: SQLite :memory: only.\n";
'@ | php
if ($LASTEXITCODE -ne 0) { throw 'STOP: test isolation preflight failed' }
php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result --filter '(WebsiteSettingsTest|WebsiteContactsTest|ContactValueTest)'
if ($LASTEXITCODE -ne 0) { throw 'STOP: focused tests failed; do not install' }
php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result
if ($LASTEXITCODE -ne 0) { throw 'STOP: full regression failed; do not install' }
~~~

Record actual test/assertion totals and failures before installation. Do not treat the standalone 43 checks as substitutes for the focused/full PHPUnit suites.

### Manual installation and preservation checklist

Only after Windows tests pass and installation is approved:

1. Arrange a quiet CMS-only write window. Preserve a checksum-verified recovery backup outside the web root, including the schema and all Website Settings/contact values. Privately record ID 1, every original setting, raw legacy values and unrelated business-record evidence. Do not stop shared services or reinitialize menus/RBAC.
2. Reuse the established process-only migrator/runtime credential approach documented in F009's installation reference: secure password prompts, restore process variables in finally, explicit mysql / 127.0.0.1 / 3312 / dss_enterprise_cms, blank DB_URL/DB_SOCKET, and an absent unique process-only APP_CONFIG_CACHE path. Reuse only the generic account wrapper and identity check; do not execute F009's pending-migration checks, snapshot workflow or seeders again. Keep permanent .env on dss_cms_runtime.
3. Before each account's operation, independently verify SQL version 11.4.13, port 3312, data directory C:/MariaDBData/CMSDEV11413, selected schema dss_enterprise_cms and CURRENT_USER matching the exact requested dedicated account at 127.0.0.1/localhost. Require website_settings to be InnoDB. Confirm the new migration is pending and its table/metadata are absent. If evidence differs, STOP; do not repair engines, grants or schema automatically.
4. Under dss_cms_migrator only, apply exactly the new migration (not an unrestricted migration run):

~~~powershell
php artisan migrate --database=mysql --path=database/migrations/2026_11_26_000002_create_website_contacts_table.php --force --no-interaction
if ($LASTEXITCODE -ne 0) { throw 'STOP: migration failed; preserve partial state for review' }
~~~

5. Restore the runtime process credentials, recheck identity and the existing least-privilege schema DML grants. Privately verify all original Website Settings values/ID and unrelated business records still match the backup. Verify the new FK, canonical unique key, generated-primary unique key, ordering index and InnoDB engine; no role/permission updates are needed.
6. With dss_cms_runtime, run the read-only initialization plan:

~~~powershell
php artisan settings:initialize-contacts --dry-run
if ($LASTEXITCODE -ne 0) { throw 'STOP: review preserved legacy values and reconciliation needs' }
~~~

7. Compare planned counts/order/primary intent with each original contact privately. If any value is ambiguous, stop and approve an explicit protected reconciliation JSON outside public/. Review it with --dry-run --reconcile= first. Do not invent or discard contact values to obtain a passing initialization.
8. After conversion review, run exactly one approved initialization, or its separately reviewed --reconcile= variant:

~~~powershell
php artisan settings:initialize-contacts
if ($LASTEXITCODE -ne 0) { throw 'STOP: initialization failed; preserve evidence for review' }
~~~

9. Verify original legacy columns byte-for-byte unchanged; General/Address/Social/Footer unchanged; owner ID 1, correct per-type counts, order, canonical duplicates, intended primary flags, initialized marker and version. Only expected metadata/updated_at changes are allowed. Confirm repeating the initializer preserves edits and deliberate emptiness; use isolated fixtures for destructive edge-case demonstrations rather than deleting real contacts as a test. Close all temporary account scopes and dispose secure prompt values; permanent runtime settings remain unchanged.
10. Browser acceptance: add/edit multiple labeled emails and all phone types; save/reload; move rows; switch primary; deactivate primary and observe no silent replacement; cancel/confirm removal; inspect inline errors/old input; check unauthorized access; verify escaped labels and safe active-only link helpers in isolated tests. Review narrow/mobile layout and unsaved navigation. Preserve General, Address, Social and Footer. Approve the enhancement only after manual installation and browser verification.

Recovery: stop on any unexpected result. Preserve the protected backup and failed-state evidence. Do not use migrate:fresh, automatic down/rollback, seeders or destructive resets. Restore only through a separately reviewed recovery procedure; a schema rollback cannot recover normalized-only contact edits. No live installation, credential read, ACL/configuration change or backup operation was executed by Codex.


## 67. Consolidated Website Contacts, Business Addresses and Location Contacts

**Status: Complete — Installed, Verified and User Accepted.** Final user approval granted on 26 November 2026. This section is the authoritative consolidated completion record for default website contacts, business addresses and location-specific contacts. It supersedes section 66's preliminary pending statuses and installation/test commands. The original F005 Complete / 38-of-38 acceptance record and F001–F009 completion remain unchanged. Public Page Rendering and F010 remain deferred; no next feature is authorized by this closure.

### Approved scope and collection policy

- Default Website Contacts remain independent website-wide emails and phones. Existing helper names continue to return only this default scope; the primary business address never replaces default contacts.
- Business Addresses replace the single editable Address textarea. Labels are freely editable Unicode text. Each address supports address lines, city/locality, state/region, postal code as a string, optional country, activation, primary designation and deterministic ordering.
- Each address optionally owns independent emails/phones using the existing ContactValue validation and WebsiteContact link helpers. The same value may appear in multiple scopes, but canonical duplicates within one scope/type are rejected.
- At most one active primary business address per website, and one active primary email/phone per scope/type. No automatic replacement on deletion/deactivation. Changing the primary address does not change contact primary flags.
- Deactivation retains location contacts and their flags, but public helpers hide them while the address is inactive. Reactivation restores eligibility of individually active contacts.
- No silent cascade deletion. An omitted address with contacts is rejected unless a removal acknowledgment contains exactly its current contact IDs. The UI requires each contact to be explicitly removed first; it then permits removing the address in the same Save Settings transaction. Unknown/stale/cross-owner acknowledgments fail. Empty addresses can be removed directly. Default contacts are unaffected.
- Original public_email, primary_phone, secondary_phone and address columns remain preserved snapshots and are prohibited as editable HTTP inputs. General, Social Links and Footer remain otherwise unchanged. No roles, permissions, routes, dependencies or separate branch-management module were added.

### Final migration order and schema

The original contact migration was preserved, and two dependency-ordered additive migrations extended it without rewriting history. All three migrations were subsequently applied successfully by the user using the dedicated migrator account on MariaDB 11.4.13. Codex performed no live migration or database operation. The accepted installation evidence is recorded below.

1. database/migrations/2026_11_26_000002_create_website_contacts_table.php
2. database/migrations/2026_11_26_000003_create_website_addresses_table.php
3. database/migrations/2026_11_26_000004_scope_website_contacts_to_addresses.php

website_settings retains every legacy column. Collection metadata is contacts_initialized_at, addresses_initialized_at and contacts_version. The existing contacts_version now versions **all default contact, address and nested contact changes**, including successful initialization. One combined Settings save increments it once; general-only saves preserve the collections.

website_addresses uses InnoDB: generated bigint id; restrictive website_setting_id FK; required label varchar(120) and address_line_1 text; optional address_line_2 varchar(255), city/state/country varchar(120), postal_code varchar(32); normalized_hash char(64); is_primary/is_active booleans; unsigned sort_order; nullable generated primary_key (1 when primary, otherwise NULL); timestamps. Indexes/constraints:

- Unique (website_setting_id, id), supporting composite contact ownership.
- Unique (website_setting_id, primary_key), enforcing at most one primary address.
- Unique (website_setting_id, normalized_hash), preventing duplicate labeled addresses.
- Ordered lookup index (website_setting_id, sort_order, id).

website_contacts retains section 66's fields and gains nullable website_address_id plus generated scope_key = COALESCE(website_address_id, 0). Zero is reserved for default scope; application-created address IDs are positive. The original settings-owner FK is retained. A restrictive composite FK (website_setting_id, website_address_id) references website_addresses (website_setting_id, id), preventing cross-owner location references. Scoped unique keys are:

- (website_setting_id, scope_key, type, normalized_value).
- (website_setting_id, scope_key, primary_type).

This non-null scope expression prevents NULL address IDs from bypassing default-scope uniqueness. Additional lookup index: owner/address/type/order/id. No separate address_contacts table. Database uniqueness supplements server validation; active-primary eligibility is application-enforced.

All submitted changes share the existing singleton upsert/row lock and one encompassing transaction. Retained address/contact IDs remain stable; owner, scope and order are assigned by the server. Contact IDs cannot move between scopes. The sync actions preserve omitted contact collections; they replace only explicitly submitted complete collections. Temporary transaction-local canonical/hash values allow valid swaps without false unique conflicts. Any failure rolls back General, default contacts, address rows, nested contacts, primary flags and version together.

MariaDB DDL is not atomic. A failed migration requires review of preserved partial state; do not rerun blindly. Reversing the scope migration refuses while address-specific contacts remain. Its SQLite reversal drops the FK by columns, while MariaDB uses the explicit constraint name. Down migrations are not a recovery strategy for normalized-only edits.

### Address validation, duplicates and formatting

- Required label: 120 characters; required address line 1: 2,000 characters, preserving CR/LF multiline content.
- Optional line 2: 255; city/state/country: 120 each; postal code: 32, preserved as text including leading zeroes.
- Unicode/international addresses are supported. No country or postal-code assumptions, geocoding or automatic component extraction.
- Reject controls, except CR/LF in line 1. Address fields and nested contact values bypass automatic HTTP trimming so unsafe controls reach validation. Values are escaped in Blade/Alpine text; formatted addresses remain plain text.
- Duplicate policy: SHA-256 of the ordered label/address components after whitespace collapsing and Unicode case folding. Include the label so intentionally different labeled locations may share a physical address. Exact/case/whitespace variants with the same label are rejected. Unicode normalization beyond case folding and geographic equivalence detection are not performed.
- formattedAddress() preserves meaningful lines, excludes blank components and removes repeated components using case/whitespace comparison. No independently editable full-address field exists. Consumers must escape the result, e.g. escaped text with whitespace-pre-line, never arbitrary raw HTML.

### Complete-submission integrity and limits

- Default contacts: maximum 50 emails and 50 phones.
- Business addresses: maximum 50.
- Per address: maximum 10 emails and 10 phones.
- Aggregate: maximum 100 contact records across all scopes.
- Form scalar-field budget: min(900, PHP max_input_vars minus 20) when PHP defines a positive limit. The UI uses a conservative field estimate and refuses additions beyond the budget. Individual maxima cannot necessarily all be reached simultaneously.
- A header placed before normal form data declares that collections are expected. A final completion marker is placed after all collection inputs. Root and nested readiness flags and exact collection counts must agree. Missing/truncated nested data cannot become an unintended empty replacement; validation fails before persistence.
- Removed-address IDs and exact child acknowledgments are bounded/validated. Empty acknowledgments are allowed only for addresses actually lacking contacts.
- PHP/server body and input limits still apply; no configuration was changed. For very restrictive limits, increase capacity only through separately approved environment work. This enhancement provides no chunked/AJAX save queue.
- The shared stale-version gate rejects repeat submissions from an old form, including duplicate creation attempts. No client-supplied internal hashes, scope keys, owners or sort positions are accepted.

### Legacy initialization and preservation

Default legacy email/phone values initialize into default scope only. Existing default normalization, supported separators, primary intent, ambiguity handling and repeat-safe marker behavior remain intact.

Address initialization is independent, read-only in dry-run mode and transactional in write mode. Keep the complete legacy address unchanged. Supported automatic cases:

- A single unambiguous multiline address: retain its complete text in address_line_1, label Business address, no invented primary, structured fields left empty.
- Multiple blocks with explicit header-only **Label:** lines: retain labels/order and each block's complete body, including CR/LF. For example Office Address: and Works: on their own lines. Commas and ordinary newlines never delimit locations.
- Only the explicit labels Primary address / Primary business address imply a primary. Head Office does not.
- Inline headings such as Office: 10 Road, unlabeled paragraphs, empty/invalid blocks, unsupported controls or over-limit data require reconciliation. No partial conversion or silent correction.
- No address-specific contacts are invented. Embedded location-contact associations require reviewed reconciliation; default website contacts are never assigned automatically.

Commands use the shared protected JSON reader (outside public/, maximum 256 KiB) and print statuses/counts/sanitized errors, not original values. Missing ID 1 returns failure; save General settings first. Unexpected uninitialized address records stop conversion. Once initialized, repeated commands preserve edits, empty collections and intentional deletions.

Contact reconciliation remains a complete emails/phones object. Address reconciliation is a complete addresses object whose records use the approved address fields/flags. To explicitly associate reviewed contacts, include contacts_present: 1 and complete emails/phones arrays; no existing address/contact IDs are allowed during initialization. CLI reconciliation does not require browser hydration/count metadata. The raw address/contact columns remain unchanged even after an explicit reconciliation override.

### Administration and public helper contracts

The Contact section now contains Default Website Contacts and Business Addresses. Address cards have responsive fields, active/primary controls, ordering and themed removal confirmation. Nested email/phone controls reuse a Blade component and the existing Alpine editor. Each contact section can expand/collapse; addresses with validation errors expand automatically. Input, removal acknowledgments and original child-ID evidence survive validation redirects. Reordering moves errors with their rows. Cancel/Escape preserves data; unsaved navigation protection remains.

While legacy conversion is pending, the original values display read-only with guidance. Other initialized collections and General/Social/Footer remain editable. The permanent Save Settings route and settings.manage authorization are unchanged.

- WebsiteSetting::contacts(), activeEmailContacts(), activePhoneContacts(), primaryEmail(), primaryPhone(): default scope only.
- WebsiteSetting::addresses(): ordered administration relationship, including inactive addresses.
- activeAddresses(): initialized, active addresses only, with batched contacts and owner-marker loading.
- primaryAddress(): explicit active primary or null; no legacy/default fallback.
- WebsiteAddress::contacts(): ordered owned contacts; composite database FK enforces settings ownership.
- WebsiteAddress activeEmailContacts(), activePhoneContacts(), primaryEmail(), primaryPhone(): require initialized addresses and an active owning location, then respect contact activation. No default substitution.
- WebsiteContact::mailtoUrl()/telUrl(): existing safe normalization/encoding plus address eligibility for scoped contacts.
- Bounded eager loading keeps address/contact iteration independent of the number of addresses. Raw administration relationships intentionally include inactive records; public consumption must use the active helpers.

No frontend Page renderer, Content API, SEO output or external maps are implemented.

### Complete combined implementation inventory

Modified existing tracked files:

- app/Http/Controllers/Admin/WebsiteSettingsController.php
- app/Http/Requests/Admin/UpdateWebsiteSettingsRequest.php
- app/Models/WebsiteSetting.php
- bootstrap/app.php
- resources/js/app.js
- resources/views/admin/settings/edit.blade.php
- tests/Feature/Admin/WebsiteSettingsTest.php
- docs/features/F005_WEBSITE_SETTINGS.md

New files across the complete enhancement (implementation inventory before documentation closure):

- app/Actions/Settings/InitializeWebsiteContacts.php
- app/Actions/Settings/InitializeWebsiteAddresses.php
- app/Actions/Settings/SyncWebsiteContacts.php
- app/Actions/Settings/SyncWebsiteAddresses.php
- app/Actions/Settings/UpdateWebsiteSettings.php
- app/Console/Commands/InitializeWebsiteContacts.php
- app/Console/Commands/InitializeWebsiteAddresses.php
- app/Models/WebsiteContact.php
- app/Models/WebsiteAddress.php
- app/Support/ContactValue.php
- app/Support/AddressValue.php
- app/Support/LegacyWebsiteContacts.php
- app/Support/LegacyWebsiteAddresses.php
- app/Support/WebsiteContactsInput.php
- app/Support/WebsiteAddressesInput.php
- app/Support/SettingsSubmission.php
- app/Support/SettingsReconciliationFile.php
- database/migrations/2026_11_26_000002_create_website_contacts_table.php
- database/migrations/2026_11_26_000003_create_website_addresses_table.php
- database/migrations/2026_11_26_000004_scope_website_contacts_to_addresses.php
- resources/js/website-contacts.js
- resources/views/components/admin/website-contacts.blade.php
- resources/views/components/admin/website-contact-rows.blade.php
- resources/views/components/admin/website-addresses.blade.php
- tests/Feature/Admin/WebsiteContactsTest.php
- tests/Feature/Admin/WebsiteAddressesTest.php
- tests/Unit/ContactValueTest.php
- tests/Unit/AddressValueTest.php
- tests/JavaScript/website-contacts.test.js
- tests/Standalone/website-settings-collections.php

The existing contact enhancement was protected work at the start of this extension; the combined inventory includes that prior work. No unrelated user changes were discarded.

### Final accepted combined verification

Implementation inventory: **38 files before final documentation closure**. The final roadmap synchronization is part of this documentation-only closure. Original preparation results below are retained alongside the user's successful ordinary-Windows PHPUnit, installation and browser evidence. No tests, build or database checks were rerun during closure.

| Check | Observed result |
| --- | --- |
| PHP syntax | Passed — 30 changed/new non-Blade PHP files |
| Scoped Pint --test | Passed — same 30 PHP files |
| JavaScript | Passed — 10 editor tests |
| Standalone SQLite :memory: | Passed — 68 checks, including established normalization, migration preservation, scoped/default uniqueness, composite ownership, restricted removal, initialization, visibility, IDOR, transactional failure/rollback, truncation/hydration and safe reversal |
| Offline MySQL/MariaDB SQL generation | Passed — 5 grammar-contract checks; no server connected and no SQL executed; not a live MariaDB migration proof |
| Blade generated-PHP parsing | Passed — 4 templates; subsequent Windows suites and user browser acceptance also passed |
| Production build | Passed — Vite 7.3.6, 60 modules; empty temporary envDir prevented real .env access |
| Combined focused PHPUnit | Passed — 81 tests, 445 assertions; ordinary Windows execution, 19.975 seconds |
| Full regression | Passed — 260 tests, 1,799 assertions; ordinary Windows execution, 24.665 seconds |
| PHP runtime / PHPUnit | 8.5.12 / 11.5.56 |
| Windows test database isolation | Passed — isolated SQLite :memory: configuration |
| Active database installation | Passed — user-installed on MariaDB 11.4.13; details below |
| Browser acceptance | Passed — all 23 checks, confirmed by user |

The new WebsiteAddressesTest contains 21 feature methods and AddressValueTest contains 3 unit methods. Existing WebsiteContactsTest, WebsiteSettingsTest and ContactValueTest were retained/extended. These source-inventory counts are distinct from the actual accepted focused result of 81 tests and full result of 260 tests. The earlier 191-test / 1,421-assertion baseline remains historical.

The previous Codex execution attempt stopped before tests/bootstrap.php could load. Ordinary Windows execution subsequently passed using the isolated SQLite in-memory test configuration. This does **not** establish that the underlying Codex runner file-access discrepancy was resolved.

Offline tests identified and corrected an eager-loading relation defect and SQLite FK-reversal incompatibility before handoff. Successful offline tests do not prove MariaDB concurrency or production readiness. MariaDB-specific concurrent-write testing remains unperformed. No sandbox/ACL investigation or permission changes were attempted.

### Accepted manual database installation

The user confirmed successful installation on MariaDB **11.4.13** using the dedicated migrator account:

| Migration | Result / execution time |
| --- | --- |
| 2026_11_26_000002_create_website_contacts_table.php | Passed — 247.83 ms |
| 2026_11_26_000003_create_website_addresses_table.php | Passed — 220.78 ms |
| 2026_11_26_000004_scope_website_contacts_to_addresses.php | Passed — 270.74 ms |

Installation verification confirmed **23 database tables and 13 applied migrations**, preservation of the Website Settings singleton, scoped and default-scope contact uniqueness, independent primary-contact constraints, the composite address-ownership foreign key, restrictive address deletion, address uniqueness/primary constraints, ordering indexes, and InnoDB/utf8mb4 compatibility. No role, permission, account or grant changes were performed. Permanent runtime account configuration was retained.

### Recovery backup evidence

- Filename: F005_before_enhancement_20261126_180623.sql.
- Size: **31,320 bytes**.
- SHA-256: **003C629F92CF4BCFD902FEBF67016122642E4A36E9D652B66A16C2DF831988AB**.
- Created outside the web root and checksum-verified before installation.
- The database contained no scheduled CMS events. The successful backup excluded scheduled events because the event scheduler was disabled.
- **Backup restoration was not tested.** Checksum/integrity evidence is not independent restore proof.

### Accepted initialization and preservation evidence

Initialization completed under the dedicated runtime account. Initial normalized inventory, before browser acceptance edits:

| Item | Initial result |
| --- | --- |
| Default emails | 1 |
| Default phone numbers | 3 |
| Business addresses | 2 |
| Address-specific contacts | 0 |
| Initialization markers | Both populated |
| Shared collection version | 2 |

The address initializer required explicit reconciliation because the original text had ambiguous location boundaries. A privately reviewed reconciliation file was successfully validated before initialization. Office Address was explicitly designated primary. Both initializers subsequently reported already-initialized status during repeat-safe dry runs. No business contact values, address contents or credentials are recorded here.

**Preservation result: manual preservation acceptance with an unresolved automated fingerprint discrepancy.** Post-migration fingerprint verification passed before initialization. After initialization, all **19 unrelated database tables** matched their original fingerprints, but the Website Settings fingerprint did not match. Repeated verification through the original Laravel database connection confirmed that the discrepancy was isolated to Website Settings. Its exact cause was not established.

The user manually confirmed that the original Website Settings fields retained their previous values and explicitly accepted original-data preservation. This is manual acceptance; **automated Website Settings fingerprint verification did not pass**. No cause is inferred from timestamps, metadata or normalization, and no investigation or record repair was performed during closure.

### Accepted browser review — 23/23

All checks passed and were confirmed by the user.

Default Website Contacts — **5/5**:

- [x] Existing email and three phone numbers displayed correctly.
- [x] Labeled contacts could be added and edited.
- [x] Primary contacts could be switched.
- [x] Contact ordering could be changed.
- [x] Contacts could be activated and deactivated.

Business Addresses — **5/5**:

- [x] Office Address and Works displayed correctly.
- [x] Structured address fields displayed expected values.
- [x] Office Address was correctly primary.
- [x] A temporary address could be added and edited.
- [x] Address ordering and activation could be changed.

Address-specific Contacts — **6/6**:

- [x] An email and phone could be added to a temporary address.
- [x] Changes persisted after save and reload.
- [x] Default website contacts remained unaffected.
- [x] Primary contacts could be selected independently per address.
- [x] Address deactivation preserved location contacts.
- [x] Address deletion required explicit contact removal.

General UI and Persistence — **7/7**:

- [x] Save Settings worked correctly.
- [x] Validation errors displayed correctly and preserved input.
- [x] Removal confirmation and cancellation worked.
- [x] Unsaved changes warning worked.
- [x] General, Social Links and Footer remained unchanged.
- [x] Mobile layout had no horizontal overflow.
- [x] Temporary test records were removed, and intended primary flags/order were restored.

### Historical ordinary-Windows verification procedure — completed

Retained as first-install evidence/reference, not a pending instruction to rerun. The user completed Windows verification with the results recorded above. Any future execution requires its own approved scope. This procedure uses the isolated existing PHPUnit bootstrap, not Artisan bootstrapped against the runtime database; actual application/session state and Media stay outside these tests.

~~~powershell
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath 'C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms'
@'
<?php
require 'tests/bootstrap.php';
$test = new Tests\Feature\Admin\WebsiteAddressesTest('test_addresses_and_new_scoped_contacts_save_together_without_replacing_defaults');
$app = $test->createApplication();
if ($app['config']->get('database.default') !== 'sqlite'
    || $app['config']->get('database.connections.sqlite.database') !== ':memory:'
    || array_keys($app['config']->get('database.connections')) !== ['sqlite']) {
    throw new RuntimeException('STOP: only isolated SQLite :memory: is permitted');
}
echo "Isolation verified: SQLite :memory: only.\n";
'@ | php
if ($LASTEXITCODE -ne 0) { throw 'STOP: isolation preflight failed' }
php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result --filter '(WebsiteSettingsTest|WebsiteContactsTest|WebsiteAddressesTest|ContactValueTest|AddressValueTest)'
if ($LASTEXITCODE -ne 0) { throw 'STOP: combined focused suite failed; do not install' }
php vendor/phpunit/phpunit/phpunit --no-progress --do-not-cache-result
if ($LASTEXITCODE -ne 0) { throw 'STOP: full regression failed; do not install' }
node --test tests/JavaScript/website-contacts.test.js
if ($LASTEXITCODE -ne 0) { throw 'STOP: editor tests failed' }
php tests/Standalone/website-settings-collections.php
if ($LASTEXITCODE -ne 0) { throw 'STOP: synthetic persistence checks failed' }
~~~

Actual focused/full counts and assertions are recorded above. No additional verification was run for closure. Standalone checks remain distinct from PHPUnit and MariaDB installation evidence.

### Historical coordinated manual installation — completed

The following approved first-install runbook is retained for historical reference. The user completed installation and initialization; **do not repeat these migrations or initializers on the current deployment**. Its original fingerprint-check requirements are qualified by the actual preservation result above: the Website Settings discrepancy remains unresolved and was accepted manually. Future installations/recovery require separate authorization.

1. Establish a quiet CMS-only write window and **one current, checksum-verified recovery backup** outside the web root. Include the schema and original ID-1 Website Settings row, all raw legacy values and existing business/RBAC/migration evidence. Review privately; do not print sensitive records to chat. Do not stop shared services or rerun default-menu/permission initialization.
2. Reuse only the generic process-only account wrapper / authenticated identity check from the established F009 installation approach. Do not repeat its historical migration checks, snapshot framework or seeders. Use secure prompts, explicit mysql / 127.0.0.1 / 3312 / dss_enterprise_cms, blank DB_URL/DB_SOCKET and a unique absent process-only APP_CONFIG_CACHE path. Restore variables in finally and dispose secure prompt values. Permanent .env remains dss_cms_runtime; no installed framework or credentials file is added here.
3. Before every account operation, verify server 11.4.13, SQL port 3312, data directory C:/MariaDBData/CMSDEV11413, selected schema and the exact requested dedicated CURRENT_USER at 127.0.0.1/localhost. Verify website_settings is InnoDB and all three reviewed migrations are pending with no conflicting partial columns/tables. If the expected baseline differs, STOP rather than rewriting history or modifying grants/engines.
4. Under temporary **dss_cms_migrator** credentials only, execute the exact dependency-ordered files:

~~~powershell
$contactMigration = 'database/migrations/2026_11_26_000002_create_website_contacts_table.php'
$addressMigration = 'database/migrations/2026_11_26_000003_create_website_addresses_table.php'
$scopeMigration = 'database/migrations/2026_11_26_000004_scope_website_contacts_to_addresses.php'
foreach ($migration in @($contactMigration, $addressMigration, $scopeMigration)) {
    php artisan migrate --database=mysql --path=$migration --force --no-interaction
    if ($LASTEXITCODE -ne 0) { throw 'STOP: preserve partial migration state and request review' }
}
~~~

5. Restore **dss_cms_runtime** process credentials and repeat identity verification. Confirm its existing least-privilege schema DML access covers the new tables. Verify preserved legacy values and unrelated records byte-for-byte, ID 1, three new migration records, InnoDB tables, all unique/order keys, generated expressions and both ownership FKs. No account, grant, role or permission changes are part of this installation.
6. Run both read-only plans in the same session, stopping on any issue:

~~~powershell
php artisan settings:initialize-contacts --dry-run
if ($LASTEXITCODE -ne 0) { throw 'STOP: privately reconcile default contacts' }
php artisan settings:initialize-addresses --dry-run
if ($LASTEXITCODE -ne 0) { throw 'STOP: privately reconcile legacy addresses' }
~~~

7. Privately account for every original email/phone/address block and planned order/primary intent. Ambiguous values remain untouched until a complete protected reconciliation file is explicitly reviewed. If required, review --dry-run --reconcile= for the relevant command before any write. Never associate general website contacts with a location automatically.
8. After both plans/reconciliations are approved, initialize under the runtime account:

~~~powershell
php artisan settings:initialize-contacts
if ($LASTEXITCODE -ne 0) { throw 'STOP: default initialization failed; preserve evidence' }
php artisan settings:initialize-addresses
if ($LASTEXITCODE -ne 0) { throw 'STOP: address initialization failed; preserve evidence' }
~~~

For a reviewed reconciliation, replace only that command with its approved --reconcile= variant. The two initializers are separate transactions, not one cross-command transaction. If the second fails, the first remains safely initialized and repeat-safe; stop and report rather than deleting or reinitializing it. Normal Save Settings remains one atomic transaction across all scopes.

9. Compare all four raw legacy columns and unrelated settings/business records with the backup. Only expected initialization metadata/version/updated_at and new normalized rows may differ. Verify owner/scope, each individual converted contact/address, labels, order, empty location contacts, intended primaries and marker state. Repeat initializer dry runs to confirm no-op status; exercise destructive/empty-state edge cases with synthetic tests, not deletion of real business contacts.
10. Complete one browser acceptance: default contacts; several freely labeled/international addresses; each location's independent emails/phones/primary flags; add/edit/order/save/reload; active/inactive styling; deactivate/reactivate without child deletion; explicit child/address removal and Cancel/Escape; errors/old input/automatic expansion; stale forms; hydrated/complete form guards; unsaved navigation; mobile layout. Verify unrelated General/Social/Footer and menu behavior remain unchanged.
11. Preserve the backup and restore normal runtime process context. Any unexpected result stops installation. No migrate:fresh, unrestricted rollback, resets, seeders or ad hoc deletion. Recovery of normalized-only edits needs a separately reviewed backup/restore plan. Accept the combined enhancement only after actual Windows verification, MariaDB installation and browser review.

### Retained limitations and final completion

- Live MariaDB concurrent-write behavior remains unverified.
- The automated Website Settings fingerprint discrepancy remains unresolved; original-value preservation was manually confirmed and accepted by the user. The 19 unrelated tables passed fingerprint comparison.
- Backup integrity/checksum was verified, but restoration was not tested.
- Existing email/phone normalization limits and conservative legacy reconciliation rules remain.
- PHP request-size, max_input_vars and scalar-field budgets still apply.
- Public rendering, Content API and SEO integration remain deferred.
- Successful ordinary-Windows tests do not establish resolution of the separate Codex runner issue.

**Final enhancement status: Complete — Installed, Verified and User Accepted.** Implementation, isolated automated verification, manual installation/initialization and all 23 browser checks are accepted. Original F005 remains Complete with its historical 38/38 criteria; F001–F009 remain Complete. No next feature is started.

This final closure modified documentation only. No application/test/migration code, database records, backups, credentials, .env, ACLs, services, configuration, dependencies, storage or Git state were changed or operated on by Codex during closure. No tests, builds, migrations or initializers were rerun.
