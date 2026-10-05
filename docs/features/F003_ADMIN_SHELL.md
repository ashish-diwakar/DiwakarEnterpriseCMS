F003 — Admin Application Shell
Status:
Planned
1. Objective
Create the reusable administrative application shell for Diwakar Enterprise CMS.
F003 establishes the common admin interface used by future CMS modules.
The shell should provide:
- reusable admin layout
- responsive sidebar
- header/top bar
- authenticated-user controls
- logout access
- permission-aware navigation
- page title/header structure
- reusable content area
- mobile navigation behavior
- consistent administrative presentation
F003 is primarily an application-shell and navigation feature.
It must not implement future CMS business modules.
2. Background
The following foundation is already complete.
F001 — Authentication Integration
Provides:
- login
- logout
- password flows
- CMS authentication layout
- protected admin dashboard
F002 — Roles, Permissions and Super Admin
Provides:
- Super Admin
- Admin
- role/permission authorization
- admin.access
- user/role administration foundation
- Super Admin authorization bypass
- public registration disabled
- protected administrative routes
F003 builds the reusable visual/admin-navigation layer on top of these foundations.
3. Current State
The project already contains an admin layout concept and administrative views.
Existing implementation must be inspected during Analysis before changes are proposed.
Do not assume:
- current layout files must be replaced
- current dashboard markup is final
- existing navigation components are reusable without review
The actual repository is authoritative.
4. In Scope
F003 includes:
- reusable admin application layout
- admin sidebar
- admin header/top navigation
- authenticated user display
- logout control
- responsive/mobile sidebar behavior
- active navigation state
- permission-aware navigation
- reusable page heading/title area
- reusable main content area
- basic admin branding
- integration with existing dashboard
- integration with existing F002 user/role screens
- clean handling of desktop and mobile admin navigation
- accessibility-conscious navigation behavior
- shared Blade components/partials where appropriate
- focused layout/navigation tests where useful
5. Out of Scope
F003 does not include:
- new CMS content modules
- Website Settings
- Media Library
- Pages
- Page Sections
- Menu Builder
- SEO Manager
- Services
- Projects
- Blog
- Testimonials
- Clients
- Gallery
- FAQ
- Contact Messages
- Activity Logs
- Theme Architecture
- public website redesign
- analytics dashboard
- charts
- reporting
- notifications system
- global search
- command palette
- dark mode unless separately approved
- customizable admin themes
- drag-and-drop navigation
- user-defined sidebar menus
- multi-tenancy navigation
- advanced breadcrumbs engine
- complex frontend SPA architecture
Do not implement future roadmap modules merely to populate the sidebar.
6. Dependencies
Required completed features:
F001 — Authentication Integration
Status:
Complete
F002 — Roles, Permissions and Super Admin
Status:
Complete
No additional package is expected to be required.
7. User Roles / Actors
Relevant actors:
Super Admin
Has full CMS authorization through the approved centralized Super Admin strategy.
Admin
Has permissions assigned through F002.
Authenticated Unauthorized User
Must not gain access to the admin shell merely because authenticated.
Guest
Must continue to follow authentication behavior established in F001/F002.
8. Admin Shell Access
The admin shell is part of the protected CMS administration area.
Access must continue to require:
- authentication
- approved authorization
F003 must not weaken F002 route protection.
The layout itself is presentation.
Security remains enforced server-side through routes, middleware, Gates, Policies, and permissions as appropriate.
9. Layout Structure
F003 should establish one reusable admin layout rather than duplicating complete HTML structures in each module.
Expected conceptual structure:
Admin Layout
→ Sidebar
→ Header
→ Page Heading
→ Main Content
→ Optional Flash Messages
→ Optional Page Actions
Exact Blade/component architecture must be determined during Analysis from the existing project structure.
10. Existing Admin Layout
Existing project layout concepts must be reused where practical.
Potential existing files may include:
app/View/Components/AdminLayout.php
resources/views/components/admin-layout.blade.php
and:
resources/views/admin/layouts/
Do not create a second competing admin-layout architecture without justification.
11. Sidebar
The sidebar should provide reusable navigation for implemented CMS functionality.
Initial navigation should include only currently available destinations.
Potential F003 navigation:
- Dashboard
- Users
- Roles
Do not add placeholder links for unimplemented future modules unless explicitly approved.
12. Future Navigation
Future modules will add sidebar entries as their features are implemented.
Examples eventually may include:
- Settings
- Media
- Pages
- Menus
- SEO
- Services
- Projects
- Blog
F003 must make such future additions straightforward without implementing those modules now.
13. Permission-Aware Navigation
Sidebar items should be displayed according to authorization where appropriate.
Examples:
User administration may depend on:
users.view
Role administration may depend on:
roles.view
Use Laravel authorization mechanisms such as:
@can
where appropriate.
Do not use hard-coded role-name checks when permission checks are sufficient.
14. Navigation Security
Permission-aware navigation is a usability feature.
It is not a security boundary.
Routes/controllers must remain protected server-side even when a sidebar item is hidden.
Direct URL requests must not bypass authorization.
15. Active Navigation State
The sidebar should visually indicate the active section.
Prefer route-name-based matching.
Example conceptual behavior:
admin.dashboard
→ Dashboard active
admin.users.*
→ Users active
admin.roles.*
→ Roles active
Avoid brittle hard-coded URL parsing when named routes provide a cleaner solution.
16. Admin Header
The admin header/top bar should provide useful application-level controls.
Expected minimum:
- mobile menu toggle
- current user identity
- user/account menu where useful
- logout
Do not add speculative notification/search systems.
17. User Display
The admin shell may display:
- authenticated user's name
- role
Do not display:
- password
- password hash
- remember token
- internal security values
- sensitive authentication data
18. Logout
F003 must provide an obvious logout action.
Logout must continue using the existing secure Breeze:
POST /logout
flow.
Do not create:
GET /logout
Do not implement custom session logout logic.
19. Account Menu
A simple authenticated-user dropdown may be used where appropriate.
Potential contents:
- user's name
- user's role
- logout
Do not implement profile-management features merely to fill the menu.
20. Admin Branding
Use neutral project branding:
Diwakar Enterprise CMS
The shell should remain reusable for future websites.
Do not hard-code:
Dewatering India
into core admin architecture.
Website-specific branding may later come from Website Settings.
21. Logo
F003 may use:
- simple text branding
- an existing generic CMS logo if already present
Do not create a complex branding-management system.
Dynamic site branding belongs to future Website Settings functionality.
22. Responsive Design
The admin shell should work at practical desktop, tablet, and mobile sizes.
Desktop:
- persistent or appropriate sidebar
- header
- content area
Mobile:
- sidebar should collapse/hide appropriately
- menu toggle should expose navigation
- content should remain usable
Do not introduce a heavy JavaScript framework for navigation.
23. Alpine.js
Alpine.js is already available through the current frontend stack.
It may be used for lightweight interactions such as:
- mobile sidebar toggle
- account dropdown
Do not add React/Vue or another frontend framework for the admin shell.
24. JavaScript Scope
Keep admin-shell JavaScript minimal.
Prefer Alpine.js and standard browser behavior.
Do not introduce:
- complex state management
- SPA routing
- additional build systems
25. Tailwind CSS
Use the existing Tailwind-based frontend setup.
Do not introduce:
- Bootstrap
- another CSS framework
- admin template dependency
unless separately approved.
26. Third-Party Admin Themes
Do not install or adopt a third-party admin dashboard theme/package during F003 without explicit approval.
The preferred direction is a lightweight project-owned Blade/Tailwind admin shell.
27. Page Heading
Admin pages should have a consistent page-heading structure.
Potential elements:
- title
- optional description
- optional actions
Examples:
Users
Manage CMS users and assigned roles
with an optional page action.
Do not force every page to have every element.
28. Page Actions
The layout should allow future pages to provide contextual actions.
Examples:
- Add Page
- Upload Media
- Add Service
No such future actions should be implemented in F003.
The shell should merely support the pattern where useful.
29. Flash Messages
F003 may establish reusable display of normal session status/flash messages if the project already needs them.
Potential categories:
- success
- error
- warning
- information
Do not build a complex notification framework.
30. Validation Errors
Feature-specific forms should continue displaying their validation errors appropriately.
F003 does not need to create a global validation architecture unless existing views clearly benefit from a small reusable component.
31. Dashboard Integration
The existing admin dashboard must render inside the finalized F003 admin shell.
Do not turn F003 into the full dashboard feature.
F004 will implement the actual Admin Dashboard content.
During F003, the dashboard may remain simple.
32. F002 Screen Integration
The existing F002 screens should be visually integrated into the admin shell:
- Users
- Roles
Do not materially expand their business functionality.
Minor markup changes needed for consistent layout are acceptable.
33. Breadcrumbs
A simple breadcrumb capability may be considered if it materially improves navigation.
Do not build a general breadcrumb package or complex hierarchy system unless needed.
If breadcrumbs are not currently necessary, defer them.
34. Accessibility
Admin navigation should follow reasonable accessibility practices.
Consider:
- semantic navigation elements
- meaningful button labels
- keyboard-accessible controls
- focus behavior
- aria attributes where required for toggles
- sufficient structural clarity
- non-color-only active state where practical
Do not pursue unnecessary accessibility abstractions.
35. Mobile Menu Accessibility
If a mobile menu toggle is implemented:
- use an actual button
- provide an accessible label
- expose expanded/collapsed state where appropriate
- ensure navigation remains keyboard usable
36. Route Names
Navigation should prefer named routes.
Current expected examples include:
admin.dashboard
and F002 admin route names.
Exact names must be verified from the repository during Analysis.
Do not guess route names in implementation.
37. Navigation Configuration Strategy
Analysis should determine whether navigation should be:
- directly expressed in Blade
- represented by a small PHP configuration/structure
- implemented through Blade components
Use the simplest maintainable solution.
Do not create a database-driven admin navigation system.
38. Blade Components
Create reusable Blade components only when they reduce meaningful duplication.
Potential examples:
- sidebar link
- dropdown
- flash message
- page heading
Do not create a component for every HTML element.
39. Partial vs Component
Use:
Blade component
when encapsulated reusable behavior/presentation provides value.
Use:
partial
when simple shared markup is sufficient.
Do not force one mechanism universally.
40. Authorization in Components
Components may use:
@can
or passed authorization state for presentation.
Do not perform unrelated database queries inside layout components merely to determine navigation.
Use the authenticated user's already available authorization context.
41. Database Impact
Expected:
None
F003 should not require schema changes.
If Analysis determines a database migration is required:
STOP.
Explain why.
Request approval before implementation.
42. Dependency Impact
Expected:
None
Use the existing:
- Blade
- Tailwind CSS
- Alpine.js
- Vite
- Laravel authorization
If a new dependency appears necessary:
STOP.
Explain the need and request approval.
43. Configuration Impact
Expected:
minimal or none.
F003 should not modify:
- authentication architecture
- authorization architecture
- session configuration
- permission schema
- database infrastructure
44. Routes
F003 should not introduce significant business routes.
Existing:
- Dashboard
- Users
- Roles
routes should be reused.
A route change is acceptable only if necessary to support the approved shell/navigation behavior.
Do not introduce future module routes.
45. Security Requirements
Follow:
docs/06_SECURITY_RULES.md
Specific F003 requirements:
- do not weaken auth
- do not weaken can:admin.access
- use POST logout
- hide unauthorized navigation appropriately
- preserve server-side authorization
- do not expose sensitive user values
- escape user-controlled values
- avoid unsafe raw HTML
- no privilege behavior in client-side JavaScript
46. XSS Safety
Authenticated user names, role names, flash messages, and other dynamic values must use normal escaped Blade output unless explicitly sanitized.
Do not use raw:
{!! !!}
for ordinary admin-shell values.
47. Testing Requirements
F003 testing should focus on meaningful behavior rather than CSS implementation details.
Potential automated tests:
- authorized admin dashboard renders
- admin layout renders expected shell content
- logout action remains available/functional
- unauthorized users remain denied
- user navigation appears for authorized user
- roles navigation appears for authorized user
- permission-restricted navigation is not shown where applicable
- existing F002 routes continue to function
- frontend homepage remains unaffected
Avoid brittle assertions against large exact HTML structures.
48. Authorization Regression
F003 must preserve F002 behavior.
Tests should ensure:
Guest:
cannot access admin.
Unauthorized authenticated user:
cannot access admin.
Admin:
can access authorized area.
Super Admin:
can access authorized area.
Do not weaken these rules merely to render navigation.
49. Manual Verification
Manual browser verification will be important for F003.
Expected areas:
Desktop
Verify:
- sidebar renders
- header renders
- page content renders
- active navigation works
- account menu works
- logout is accessible
Mobile / Narrow Width
Verify:
- sidebar is not permanently obstructing content
- menu toggle works
- navigation can be opened/closed
- content remains usable
Users / Roles
Verify:
- existing F002 pages use the common shell
- navigation is consistent
Final manual steps should be updated after implementation.
50. Visual Scope
F003 should look professional and coherent.
However, avoid spending excessive scope on:
- pixel-perfect theme design
- elaborate animations
- visual effects
- extensive dashboard widgets
- branding customization
Priority order:
1. correct structure
2. security
3. usability
4. responsiveness
5. maintainability
6. visual polish
51. Existing Files Must Be Inspected
Before planning implementation, inspect only F003-relevant files.
Likely areas include:
- app/View/Components/AdminLayout.php
- resources/views/components/admin-layout.blade.php
- resources/views/admin/layouts/
- admin layout partials
- admin dashboard view
- F002 user/role views
- routes/admin.php
- relevant Vite/Tailwind/Alpine setup
- relevant admin tests
Do not scan future module code.
52. Pre-existing Pint Findings
Known pre-existing formatting findings exist in:
app/View/Components/FrontendLayout.php
routes/frontend.php
F003 is an admin-only feature.
These files should remain out of scope unless F003 unexpectedly requires modification.
Do not fix them merely to obtain a project-wide clean Pint run.
53. Existing Composer Advisories
F002 documented existing Composer security advisories unrelated to the approved RBAC dependency.
F003 must not perform dependency remediation unless separately approved.
Do not:
- run broad Composer updates
- change package constraints
- suppress advisories
as part of F003.
54. Git / GitHub
Follow AGENTS.md.
Local read-only Git commands may be used for:
- working-tree baseline
- diff review
Do not:
- access GitHub
- fetch
- pull
- push
- clone
- commit automatically
- create branches automatically
- use remote APIs/connectors
The user handles GitHub manually.
55. Analysis Questions
F003 Analysis must answer:
1. What admin layout structure already exists?
2. Which existing files can be reused?
3. Is there duplicate admin-layout markup that should be consolidated?
4. What is the simplest reusable sidebar architecture?
5. What routes should appear in navigation now?
6. Which navigation items require permission checks?
7. How should active route state be detected?
8. How should the authenticated user's name/role be displayed?
9. Where should logout be placed?
10. Is Alpine.js already initialized and suitable for mobile sidebar/dropdown interactions?
11. What Blade components/partials are genuinely needed?
12. What F002 views require layout integration?
13. Is any package required?
14. Is any database change required?
15. What focused tests are appropriate?
16. What behavior needs manual browser verification?
56. Decisions Expected From Analysis
Analysis should recommend:
- final admin layout structure
- sidebar implementation approach
- header/account-menu approach
- mobile navigation approach
- permission-aware navigation approach
- active-state approach
- reusable component/partial list
- exact current navigation entries
These implementation details may proceed after normal plan approval if they remain inside F003.
57. Decisions Requiring Separate Approval
STOP and request separate approval if Analysis recommends:
- new package
- database migration
- replacement frontend framework
- third-party admin theme
- architectural change outside existing Blade/Tailwind approach
- major authentication/authorization changes
These are not expected for F003.
58. Expected Navigation at F003 Completion
Unless Analysis finds a better minimal structure based on actual routes, expected initial navigation is approximately:
General
- Dashboard
Administration
- Users
- Roles
Only show entries the current user is authorized to view.
Do not create menu entries for unimplemented roadmap modules.
59. Implementation Plan
Status:
Pending Analysis
The implementation plan should normally contain:
approximately 4–7 meaningful steps.
Do not implement until the user approves the plan.
60. Implementation Record
Files Created
Pending.
Files Modified
Pending.
Files Removed
Pending.
Database Changes
Expected:
None
Final:
Pending.
Dependencies
Expected:
None
Final:
Pending.
Tests Added / Updated
Pending.
61. Validation Record
Focused Tests
Pending.
Regression Suite
Pending.
Laravel Pint
Pending.
Frontend Build
Pending.
Route Review
Pending.
Security Review
Pending.
Responsive Manual Review
Pending.
Final Diff Review
Pending.
62. Known Limitations
Current expected limitation:
The dashboard content itself remains intentionally basic until:
F004 — Admin Dashboard
Future module navigation is added only as those modules are implemented.
Additional limitations:
Pending implementation.
63. Completion Record
Implementation:
Pending
Acceptance Criteria:
Pending
Focused Testing:
Pending
User Code Review:
Pending
Manual UI Review:
Pending
Final Validation:
Pending
Final User Approval:
Pending
Feature Status:
Planned
64. Acceptance Criteria
Layout
- [ ] Admin pages use one reusable admin application shell.
- [ ] Admin shell includes a sidebar.
- [ ] Admin shell includes a header/top bar.
- [ ] Admin shell provides a consistent content area.
- [ ] Dashboard renders inside the common shell.
- [ ] F002 Users page renders inside the common shell.
- [ ] F002 Roles page renders inside the common shell.
Navigation
- [ ] Dashboard navigation is available to authorized admin users.
- [ ] Users navigation follows appropriate permission visibility.
- [ ] Roles navigation follows appropriate permission visibility.
- [ ] Current section has a clear active state.
- [ ] No links to unimplemented future modules are introduced.
User Controls
- [ ] Current authenticated user identity is visible appropriately.
- [ ] Current role may be displayed without exposing sensitive information.
- [ ] Logout is easily accessible.
- [ ] Logout continues using secure POST behavior.
Responsive Behavior
- [ ] Admin shell is usable on desktop.
- [ ] Admin shell is usable on narrow/mobile layouts.
- [ ] Mobile navigation can be opened and closed.
- [ ] Mobile controls remain accessible.
Authorization
- [ ] Guest admin access remains protected.
- [ ] Authenticated unauthorized users remain denied.
- [ ] Navigation visibility does not replace backend authorization.
- [ ] Super Admin access remains functional.
- [ ] Admin access remains permission-driven.
Architecture
- [ ] Existing Blade/Tailwind/Alpine stack is reused.
- [ ] No unnecessary frontend/admin-theme dependency is introduced.
- [ ] No database schema change is introduced.
- [ ] Admin-shell code does not contain future business-module functionality.
- [ ] Reusable components/partials are introduced only where justified.
Quality
- [ ] Focused F003 tests pass.
- [ ] Existing F001/F002 authorization behavior remains intact.
- [ ] Frontend build succeeds.
- [ ] F003-modified PHP files pass Pint.
- [ ] Manual browser review confirms basic responsive shell behavior.
65. Required Workflow
F003 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Repository Analysis
→ Implementation Plan
→ USER APPROVES IMPLEMENTATION
→ Implementation
→ Focused Tests
→ USER CODE/UI REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ USER FINAL APPROVAL
→ Mark F003 Complete
→ Identify F004 only
→ ASK BEFORE F004 ANALYSIS
Do not automatically proceed to F004.
66. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F003:
- inspect only admin-shell-related files
- reuse existing layouts
- avoid frontend-framework research
- avoid third-party admin-theme research
- do not design future modules
- do not create placeholder future navigation
- use existing Tailwind and Alpine
- run focused tests first
- perform full validation only after user review
- keep implementation reports concise
67. F003 Completion Rule
F003 may be considered ready for final approval only when:
- common admin shell is implemented
- sidebar works
- header works
- logout is available
- navigation is permission-aware
- active state works
- responsive navigation works
- dashboard uses common shell
- F002 management screens use common shell
- F002 authorization remains intact
- no unnecessary package was introduced
- no schema change occurred
- focused tests pass
- frontend build passes
- final regression validation passes
- user has reviewed the UI
- final diff remains scoped to F003
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete