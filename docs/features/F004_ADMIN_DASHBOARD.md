Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\features\F004_ADMIN_DASHBOARD.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/features/F004_ADMIN_DASHBOARD.md
────────────────────────────────────
F004 — Admin Dashboard
Status:
Complete
1. Objective
Create a useful administrative dashboard for Diwakar Enterprise CMS using the application shell completed in F003.
The dashboard should provide an authenticated administrator with a concise overview of currently implemented CMS functionality.
F004 should improve the usefulness of:
/admin/dashboard
without introducing future business modules prematurely.
The dashboard should remain:
- reusable
- business-neutral
- permission-aware
- responsive
- maintainable
- lightweight
2. Background
The following foundation is complete.
F001 — Authentication Integration
Provides secure CMS authentication.
F002 — Roles, Permissions and Super Admin
Provides:
- Super Admin
- Admin
- roles
- permissions
- protected admin area
- user-role administration
- role-permission administration
F003 — Admin Application Shell
Provides:
- reusable admin layout
- sidebar
- header
- permission-aware navigation
- user/account controls
- logout
- responsive/mobile shell
- shared flash messages
- common page-heading structure
F004 builds dashboard content inside that existing shell.
3. Current State
The current dashboard intentionally contains minimal placeholder-style administration content.
It currently provides access to implemented administration areas such as:
- Users
- Roles
F003 deliberately avoided dashboard widgets, statistics, analytics, and summaries.
F004 is responsible for replacing the basic dashboard content with useful current-state information.
The actual repository implementation must be inspected during Analysis before changes are proposed.
4. In Scope
F004 may include:
- dashboard overview cards
- current CMS user count
- current role count
- current permission count where useful
- concise administrative summary
- permission-aware quick links
- current-user context where useful
- appropriate empty states
- responsive dashboard layout
- reusable dashboard-card presentation where justified
- focused dashboard tests
- efficient dashboard queries
Only information backed by currently implemented functionality may be displayed.
5. Out of Scope
F004 does not include:
- Website Settings
- Media Library
- Pages
- Page Sections
- Menu Builder
- SEO
- Services
- Projects
- Blog
- Testimonials
- Clients
- Gallery
- FAQ
- Contact Messages
- Activity Logs
- business analytics
- traffic analytics
- charts
- graphs
- reports
- financial statistics
- content statistics for modules not yet implemented
- notifications
- recent activity feed
- audit log feed
- system-health dashboard
- server monitoring
- database monitoring
- Google Analytics
- external analytics services
- artificial intelligence
- configurable dashboard widgets
- drag-and-drop dashboard
- personalized dashboard layouts
Do not create placeholder statistics for future modules.
6. Dependencies
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
7. Relevant Users
Super Admin
May view all currently available dashboard information through the approved Super Admin authorization mechanism.
Admin
May view dashboard information according to assigned permissions.
Authenticated Unauthorized User
Must remain unable to access the admin dashboard.
Guest
Must continue to be redirected through the authentication flow.
8. Dashboard Route
Existing dashboard route:
admin.dashboard
Existing URL:
/admin/dashboard
F004 should reuse the existing route unless Analysis identifies a compelling reason otherwise.
Do not create a second dashboard route.
9. Dashboard Authorization
Existing route authorization must remain intact.
Expected protection:
- authentication
- admin.access
Super Admin access continues through the centralized authorization bypass established in F002.
F004 must not change the authorization architecture.
10. Dashboard Content Principle
Every dashboard value should answer a useful administrative question.
Examples:
- How many CMS users currently exist?
- How many administrative roles exist?
- How many permissions currently exist?
- Which administration areas can I access?
Do not display numbers merely to make the dashboard appear populated.
11. Initial Summary Metrics
Analysis should evaluate the usefulness of displaying:
Users
Total CMS user accounts.
Potential permission requirement:
users.view
Roles
Total configured CMS roles.
Potential permission requirement:
roles.view
Permissions
Total configured CMS permissions.
This may be useful primarily to Super Admin or users authorized for role/permission management.
The exact visibility should be determined during Analysis.
12. Permission-Aware Metrics
Dashboard data should not reveal information that the current user is not authorized to access.
If a user does not have permission to view users, the dashboard should not expose user-management details merely through a statistic.
Use Laravel authorization mechanisms.
Do not hard-code role-name checks where permissions are sufficient.
13. Super Admin
Super Admin authorization should continue through the centralized:
Gate::before
strategy established in F002.
Do not duplicate:
Super Admin
special cases throughout dashboard code.
Normal:
can()
and related authorization APIs should remain sufficient.
14. Quick Actions
F004 may provide quick links to functionality that already exists.
Potential quick actions:
- View Users
- View Roles
Display actions only when the user is authorized.
Do not create quick-action buttons for future modules.
15. Dashboard vs Sidebar
The dashboard does not need to duplicate the entire sidebar.
Quick actions should exist only when they provide practical convenience.
Avoid creating a second navigation system inside the dashboard.
16. Welcome / Context
The dashboard may provide a concise welcome/context area.
Potential information:
- current user's name
- primary role
- brief administrative message
Avoid unnecessary personalization.
Do not display sensitive information.
17. Current Role Display
If displaying the user's role:
use the existing F002/F003 role model.
Do not query or display multiple roles as if multi-role behavior were part of the CMS product model.
The application continues to use one primary role per user.
18. Data Retrieval
Dashboard statistics must not be queried directly from Blade templates.
Retrieve required information through an appropriate application layer such as:
- controller
- focused action/service where genuinely useful
- existing route/controller architecture
Use the simplest Laravel-native approach.
19. Dashboard Controller
Analysis must determine whether the current dashboard route should remain a route closure or move to a dedicated controller.
If dashboard data becomes non-trivial, a dedicated controller is likely preferable.
Do not introduce a service/repository layer automatically.
20. Repository Pattern
Do not create repositories solely for dashboard counts.
Eloquent/query builder is sufficient for normal dashboard summary queries.
Only introduce an abstraction if there is a concrete architectural need.
21. Query Efficiency
Dashboard summary queries should be efficient.
Avoid:
- loading all users to count them
- loading all roles to count them
- N+1 relationships
- unnecessary repeated queries
Use database count queries where appropriate.
22. Caching
F004 should not introduce caching by default.
Current dashboard data volume is expected to be small.
Do not add:
- Redis
- cache infrastructure
- dashboard cache layer
without demonstrated need.
23. Reusable Dashboard Cards
A small reusable Blade component may be created if several dashboard summary cards share meaningful structure.
Potential example:
dashboard statistic card
Possible contents:
- label
- value
- optional description
- optional link
Do not create an overly generic widget framework.
24. Dashboard Card Authorization
Authorization should normally be determined before displaying sensitive dashboard content.
A hidden card must not replace server-side route authorization for the destination it links to.
25. Empty States
Dashboard sections should handle valid empty states gracefully.
Examples:
- zero users other than initial administrator where possible
- no optional quick actions available
- no additional module data yet
Do not show misleading placeholder numbers.
26. Visual Design
Use the admin shell established in F003.
Dashboard content should visually match:
- Users
- Roles
- shared page headings
- existing cards/forms
Use the existing Tailwind CSS stack.
Do not redesign the entire admin shell.
27. Responsive Layout
Dashboard summary cards and quick actions must remain usable on:
- desktop
- tablet
- mobile/narrow screens
Prefer a simple responsive grid.
Do not introduce complex JavaScript for dashboard layout.
28. JavaScript
Expected:
None
F004 should not require additional JavaScript unless Analysis identifies a genuine need.
Do not introduce client-side charting libraries.
29. Database Impact
Expected:
None
F004 should read existing data only.
If Analysis identifies a schema requirement:
STOP.
Explain why.
Request approval before implementation.
30. Dependency Impact
Expected:
None
Do not install:
- chart libraries
- dashboard packages
- analytics packages
- admin templates
- reporting packages
without separate approval.
31. Configuration Impact
Expected:
None
F004 should not modify:
- authentication configuration
- RBAC configuration
- sessions
- permission cache configuration
- application infrastructure
32. Security Requirements
Follow:
docs/06_SECURITY_RULES.md
Specific F004 requirements:
- dashboard remains protected server-side
- permission-sensitive data remains permission-aware
- user information uses escaped output
- role names use escaped output
- no secrets/security values displayed
- no raw HTML for normal dashboard data
- no client-side authorization
- no direct SQL built from user input
33. Sensitive Counts
A count may itself reveal information.
For example:
an administrator without:
users.view
should not automatically receive a user-count statistic unless explicitly justified.
Dashboard summaries should follow the same authorization intent as the underlying functionality.
34. Dashboard Links
All dashboard links should use named routes.
Do not hard-code URLs where an existing named route is available.
Examples:
admin.users.index
admin.roles.index
Exact route names must be verified during Analysis.
35. Users Summary
If included, the Users summary may contain:
- total user count
- link to user administration
Only show it when authorization permits.
Do not display:
- email lists
- password/security information
- unnecessary personal data
on the dashboard.
36. Roles Summary
If included, the Roles summary may contain:
- total role count
- link to roles administration
Only show it when authorization permits.
Do not expose complex permission configuration directly on the dashboard.
37. Permissions Summary
If included, the permissions count should remain informational.
Do not build permission editing into dashboard cards.
Permission editing remains on the Roles administration screen.
38. Dashboard Quick Links
Potential initial links:
- Manage Users
- Manage Roles
Only display actions the user can actually perform/view.
Do not show disabled buttons for inaccessible features unless there is a compelling UX reason.
Prefer hiding unauthorized actions.
39. Future Dashboard Expansion
Future features may later add their own dashboard information.
Examples may eventually include:
- Pages
- Media
- Services
- Projects
- Blog
- Contact Messages
These additions must occur only when those features exist.
F004 should establish a maintainable structure without implementing those future summaries now.
40. Dashboard Extensibility
The dashboard should be straightforward to extend.
However, do not build:
- plugin system
- dashboard-widget registry
- database widget configuration
- drag/drop layout engine
Simple Blade/controller extension is sufficient at this stage.
41. Existing Dashboard Content
Analysis should review the current Dashboard content added during F003.
Determine:
- what should remain
- what should be replaced
- what should be consolidated
Avoid unnecessary visual duplication between:
- summary cards
- quick actions
- sidebar navigation
42. Existing Users / Roles Screens
F004 should not modify the Users or Roles business functionality.
Changes to those pages should normally be:
None
unless a small dashboard-linked integration issue genuinely requires them.
43. Admin Shell
F003 admin shell architecture must remain intact.
Do not modify:
- responsive sidebar architecture
- account dropdown behavior
- logout architecture
- permission-aware sidebar behavior
unless a dashboard integration defect genuinely requires a scoped fix.
44. Authentication / Authorization Regression
F004 must preserve:
Guest:
redirected to login.
Authenticated unauthorized user:
403.
Admin with admin.access:
dashboard access allowed.
Super Admin:
dashboard access allowed through centralized bypass.
Additional summary content must respect its own relevant permissions.
45. Testing Requirements
Focused tests should verify meaningful dashboard behavior.
Potential tests:
- authorized user can render dashboard
- unauthorized authenticated user remains denied
- guest remains redirected
- user count appears for user authorized with users.view
- user-sensitive summary is absent without users.view
- role summary appears when authorized
- role-sensitive summary is absent when unauthorized
- Super Admin can see approved dashboard summaries
- quick links use authorized functionality
- dashboard does not expose future-module placeholders
Do not test exact Tailwind class lists.
46. Query Testing
Do not create excessive performance tests for simple count queries.
Code review should confirm efficient count queries.
If Analysis identifies unexpectedly expensive behavior, propose targeted tests/optimization.
47. Manual Verification
Manual browser review should verify:
Super Admin
- dashboard renders
- expected summary cards appear
- Users/Roles quick actions work
- responsive layout looks correct
Admin
Verify dashboard content corresponds to Admin's actual permissions.
For the approved baseline Admin role:
- admin.access
- users.view
- roles.view
appropriate Users/Roles summaries may appear.
Restricted Permission Scenario
If practical during testing, verify removal of a view permission also removes the corresponding dashboard summary/action.
Do not modify production-like authorization state unnecessarily.
48. Responsive Manual Review
Desktop:
- summary cards align appropriately
- content does not stretch awkwardly
- quick actions are clear
Mobile:
- cards stack cleanly
- text remains readable
- links/buttons remain usable
- no horizontal overflow from dashboard content
49. Existing Known Issues
Known unrelated project issues remain:
Pint
- app/View/Components/FrontendLayout.php
- routes/frontend.php
Composer Advisories
Previously documented dependency advisories remain unresolved.
F004 must not remediate these unless separately approved.
50. Git / GitHub
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
The user manages Git/GitHub manually.
51. Analysis Questions
F004 Analysis must answer:
1. What does the current dashboard contain?
2. Is the dashboard currently implemented through a route closure or controller?
3. Should a dedicated DashboardController be introduced?
4. Which current statistics are actually useful?
5. Which statistics require permission checks?
6. Should total users be shown?
7. Should total roles be shown?
8. Should total permissions be shown?
9. Which quick actions are useful without duplicating sidebar navigation excessively?
10. Is a reusable dashboard-card Blade component justified?
11. Can all required values be retrieved with simple efficient queries?
12. Are any additional services/actions genuinely necessary?
13. What focused tests should be added?
14. What manual browser checks are required?
15. Is any package required?
16. Is any database change required?
52. Expected Direction
Unless repository Analysis identifies a better minimal approach, the preferred F004 direction is:
Overview
A concise dashboard heading/welcome area.
Summary Cards
Permission-aware cards based only on existing functionality, potentially:
- Users
- Roles
- Permissions
Quick Administration
Permission-aware links to existing:
- Users
- Roles
No Analytics
No charts or speculative metrics.
No Future Modules
No Cards for Pages, Media, Blog, Services, etc.
53. Permissions Visibility
Expected direction:
Users card/action
Requires:
users.view
Roles card/action
Requires:
roles.view
Permissions information
Analysis should determine whether this should require:
roles.view
or:
roles.manage_permissions
Prefer the least confusing authorization mapping.
Do not create a new permission solely for the dashboard.
54. Implementation Plan
Status:
Implemented for review
Expected plan size:
approximately 3–6 meaningful steps.
Do not implement until the user approves the plan.
55. Implementation Record
Files Created
- app/Http/Controllers/Admin/DashboardController.php
- resources/views/components/admin/dashboard-stat-card.blade.php
- tests/Feature/Admin/DashboardTest.php
Files Modified
- routes/admin.php
- resources/views/admin/dashboard/index.blade.php
- docs/features/F004_ADMIN_DASHBOARD.md
Files Removed
None.
Database Changes
Expected:
None
Final:
None.
Dependencies
Expected:
None
Final:
None.
Tests Added / Updated
- Added focused dashboard feature coverage in tests/Feature/Admin/DashboardTest.php.
56. Validation Record
Focused Tests
- php artisan test --filter=DashboardTest
  Result: 11 passed, 54 assertions.
- php artisan test tests/Feature/Admin/DashboardTest.php tests/Feature/Admin/DashboardAuthenticationTest.php tests/Feature/Admin/AdminShellTest.php tests/Feature/Rbac/AdminAccessTest.php
  Result: 25 passed, 89 assertions.
Regression Suite
- php artisan test
  Result: 60 passed, 175 assertions.
Laravel Pint
- .\vendor\bin\pint.bat --test
  Result: Failed only for known unrelated files app\View\Components\FrontendLayout.php and routes\frontend.php.
- .\vendor\bin\pint.bat --test app/Http/Controllers/Admin/DashboardController.php routes/admin.php tests/Feature/Admin/DashboardTest.php
  Result: Passed for F004 PHP files.
Frontend Build
- npm.cmd run build
  Result: Passed.
Authorization Review
Passed: dashboard route keeps auth and admin.access protection; summaries/actions are permission-aware; Super Admin continues through Gate::before.
Query Review
Passed: count queries are aggregate queries in the controller and are run only after permission checks; no Blade queries, caching, collection counting, or N+1 behavior introduced.
Manual UI Review
Passed — confirmed by user.
Final Diff Review
Passed: changes remain scoped to F004 dashboard/controller/component/test/spec files; no debugging code, temporary routes, secrets, dependency changes, database changes, generated build artifacts, or future-module implementation found.
57. Known Limitations
At F004 completion:
dashboard information will intentionally be limited to implemented CMS foundation functionality.
Additional dashboard summaries will be added only after corresponding modules exist.
No analytics or reporting functionality is expected.
Additional limitations:
- Dashboard information remains intentionally limited to existing Users, Roles, and Permissions functionality.
- No analytics, future-module summaries, charts, activity feed, or reporting are included.
58. Completion Record
Implementation:
Complete
Acceptance Criteria:
33/33 satisfied
Focused Testing:
Passed
User Code Review:
Approved
Manual UI Review:
Passed — confirmed by user
Final Validation:
Passed
Final User Approval:
Approved
Feature Status:
Complete
59. Acceptance Criteria
Access
- [x] Dashboard remains protected by authentication.
- [x] Authenticated unauthorized users remain denied.
- [x] Admin with admin.access can access dashboard.
- [x] Super Admin can access dashboard.
Dashboard Structure
- [x] Dashboard uses the F003 admin shell.
- [x] Dashboard has a clear overview/heading.
- [x] Dashboard content is responsive.
- [x] No duplicate/competing dashboard layout is introduced.
Summary Data
- [x] Dashboard displays only information backed by implemented functionality.
- [x] User summary is permission-aware if included.
- [x] Role summary is permission-aware if included.
- [x] Permission summary is permission-aware if included.
- [x] Summary queries are efficient.
- [x] No sensitive information is exposed.
Quick Actions
- [x] Quick actions link only to implemented functionality.
- [x] Quick actions follow authorization.
- [x] Named routes are used.
- [x] No future-module placeholder actions are introduced.
Architecture
- [x] No repository layer is introduced without need.
- [x] No unnecessary service/action abstraction is introduced.
- [x] No database schema changes are introduced.
- [x] No new dependency is introduced.
- [x] No analytics/charting framework is introduced.
Security
- [x] Authorization remains server-side.
- [x] Dashboard values use escaped output.
- [x] Permission-sensitive information is not shown to unauthorized users.
- [x] F002 Super Admin behavior remains unchanged.
Quality
- [x] Focused F004 tests pass.
- [x] Existing authentication/RBAC behavior remains intact.
- [x] F004-modified PHP files pass Pint.
- [x] Frontend build passes if relevant assets/views require validation.
- [x] Manual browser review passes.
- [x] Final regression suite passes.
60. Required Workflow
F004 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Dashboard Analysis
→ Implementation Plan
→ USER APPROVES IMPLEMENTATION
→ Implementation
→ Focused Tests
→ USER CODE/UI REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ USER FINAL APPROVAL
→ Mark F004 Complete
→ Identify F005 only
→ ASK BEFORE F005 ANALYSIS
Do not automatically proceed to F005.
61. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F004:
- inspect only dashboard-related files
- reuse F003 shell
- use simple count queries
- avoid analytics research
- avoid chart libraries
- avoid future-module analysis
- do not redesign Users/Roles
- run focused tests first
- perform full regression only after user review
- keep reports concise
62. F004 Completion Rule
F004 may be considered ready for final approval only when:
- useful dashboard content is implemented
- data is based only on existing functionality
- dashboard data is permission-aware
- queries are efficient
- quick actions are appropriately authorized
- F003 shell remains intact
- F002 authorization remains intact
- no database changes occurred
- no new dependency was introduced
- no future-module placeholders exist
- focused tests pass
- manual UI review passes
- final regression validation passes
- final diff remains scoped to F004
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete
