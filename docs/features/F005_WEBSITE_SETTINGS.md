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
