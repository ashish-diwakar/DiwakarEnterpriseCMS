Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\features\F002_RBAC.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/features/F002_RBAC.md
────────────────────────────────────
F002 — Roles, Permissions and Super Admin
Status:
Planned
1. Objective
Establish secure role-based access control for Diwakar Enterprise CMS.
F002 must provide the authorization foundation required by future administrative modules.
The feature should define and implement:
- CMS administrative roles
- permissions
- role-to-permission assignment
- user-to-role assignment
- Super Admin behavior
- admin-area access rules
- initial Super Admin provisioning
- user-management boundaries required for RBAC
- final public-registration behavior
Authentication already exists through F001.
F002 extends authentication with authorization.
2. Background
F001 — Authentication Integration is complete.
Current authentication behavior includes:
- Laravel Breeze authentication
- login
- logout
- development registration
- password reset
- password confirmation
- email verification behavior where applicable
- protected /admin/dashboard
- authenticated-user redirect to admin.dashboard
At the end of F001:
any authenticated user can access the admin dashboard.
This was intentional for F001.
F002 must replace that temporary behavior with proper administrative authorization.
3. Current Security Gap
Authentication answers:
“Who is this user?”
Authorization answers:
“What is this user allowed to do?”
F001 implemented authentication.
F002 must ensure that being authenticated alone does not automatically grant CMS administrative access.
After F002, admin access must depend on approved authorization rules.
4. In Scope
F002 includes:
- define CMS role model
- define CMS permission model
- evaluate the proposed RBAC package
- request approval before installing any new package
- implement approved RBAC mechanism
- define Super Admin behavior
- create initial Super Admin provisioning mechanism
- restrict admin area to authorized users
- assign roles to users
- assign permissions to roles
- prevent unauthorized role/permission changes
- establish reusable authorization patterns for future modules
- define final public-registration behavior
- disable unrestricted public registration if approved as part of this specification
- update relevant authentication/admin tests
- create focused RBAC/security tests
- document final authorization architecture
5. Out of Scope
F002 does not include:
- full user-management CRUD interface unless specifically required for role assignment
- advanced user profile management
- user invitations by email
- organization/tenant management
- multi-tenancy
- subscription plans
- customer accounts
- dealer accounts
- frontend customer roles
- social authentication
- two-factor authentication
- passkeys
- API authentication
- API token permissions
- approval workflows
- content ownership workflow
- page-specific permissions
- media-specific permissions
- blog-specific permissions
- service-specific permissions
- project-specific permissions
- activity logs
- advanced audit reporting
- temporary privilege elevation
- impersonation
- LDAP/Active Directory
- external identity providers
Module-specific permissions may be introduced with their relevant future features.
6. Dependency
Required completed feature:
F001 — Authentication Integration
F001 status:
Complete
No F002 implementation should replace or weaken the Breeze authentication foundation established in F001.
7. Proposed RBAC Dependency
The currently planned package candidate is:
spatie/laravel-permission
This package is:
Not yet approved
Its mention in this specification does not authorize installation.
During F002 Analysis, the agent must determine whether the package is appropriate for:
- Laravel 12 compatibility
- role management
- permission management
- middleware integration
- policy/Gate integration
- Super Admin strategy
- testability
- maintainability
- future CMS modules
If current package information must be verified, use targeted official documentation research only.
8. Package Approval Gate
If Analysis recommends:
spatie/laravel-permission
or any other package:
STOP before installation.
Report:
- package
- version/compatibility where verified
- why it is needed
- why Laravel-native functionality alone is less suitable
- expected files/configuration
- database migrations
- security impact
- maintenance impact
Then request explicit user approval.
Package evaluation and package installation are separate actions.
9. Authorization Architecture Principle
The authorization system must be reusable across future CMS modules.
Future features should be able to express rules such as:
- user may view pages
- user may create pages
- user may edit pages
- user may delete pages
- user may manage settings
without inventing a new authorization architecture for each module.
F002 should establish the foundation only.
Do not pre-create every future module permission unless there is a concrete requirement.
10. Role Model
F002 must define a small, understandable role model.
The minimum required privileged role is:
Super Admin
Additional CMS roles should be created only when their responsibilities are clear.
Potential roles for analysis include:
- Super Admin
- Admin
- Editor
Do not automatically add:
- Author
- Viewer
- Moderator
- Manager
- Customer
- Dealer
unless there is a current product requirement.
Prefer fewer meaningful roles over many speculative roles.
11. Super Admin
The Super Admin represents the highest CMS administrative authority.
The exact implementation strategy must be finalized during Analysis.
Potential responsibilities include:
- full administrative access
- role management
- permission management
- user-role assignment
- access to security-sensitive CMS administration
The implementation must prevent accidental privilege escalation by lower-privileged users.
12. Super Admin Authorization Strategy
Analysis must determine the recommended Super Admin strategy.
Possible approaches may include:
- explicit permissions assigned to the Super Admin role
- centralized authorization bypass using Laravel Gate behavior
- package-supported recommended strategy
Do not implement a global authorization bypass without explicit approval.
The selected approach must be:
- predictable
- testable
- centralized
- maintainable
- safe
13. Super Admin Protection
The system must consider protection against:
- unauthorized assignment of Super Admin
- unauthorized removal of Super Admin privileges
- lower roles modifying Super Admin users
- accidental loss of all administrative access
Exact rules should be finalized during F002 Analysis before implementation.
14. Initial Super Admin Provisioning
F002 must provide a safe method for creating the initial Super Admin.
The solution should not require a permanent publicly accessible registration mechanism.
Potential strategies to evaluate include:
- controlled database seeder
- dedicated Artisan command
- assigning the role to an existing development user
- another Laravel-native controlled mechanism
The selected mechanism must:
- avoid hard-coded credentials
- avoid committing passwords
- avoid publicly exposing account creation
- be repeatable or safely detectable where appropriate
- avoid creating duplicate Super Admin users accidentally
15. Existing Development User
A user may already have been created through the temporary F001 registration flow.
F002 Analysis should determine whether that existing user can safely be promoted to the initial Super Admin.
Do not:
- assume a specific email address
- hard-code the user's password
- expose credentials
- automatically modify production-like data
without an approved provisioning strategy.
16. Public Registration
Public registration was intentionally temporary during F001.
The desired CMS security model after F002 is:
unrestricted public registration should not grant CMS access.
For this private administrative CMS, the preferred final state is:
public registration disabled.
If registration is disabled:
- /register should no longer provide unrestricted account creation
- existing authentication features such as login/logout/password reset should remain functional
- existing users should not be deleted
- administrative account provisioning should use the approved controlled mechanism
The Analysis stage must confirm the safest Laravel/Breeze implementation before changes are made.
17. Admin Area Access
After F002, authentication alone should not be sufficient for CMS administrative access.
An authenticated user without an approved administrative role/permission must not gain access merely by navigating directly to:
/admin
or:
/admin/dashboard
Admin access must be enforced server-side.
18. Unauthorized Admin Access
When an authenticated but unauthorized user requests a protected admin resource:
the application should deny access according to the approved Laravel authorization behavior.
Preferred behavior is normally:
403 Forbidden
Do not silently grant access.
Do not rely on hidden navigation.
19. Guest Access
Guest behavior remains authentication-driven.
A guest requesting a protected admin resource should continue to be redirected to login according to the existing authentication flow.
F002 must not break F001 guest handling.
20. Permission Model
Permissions should represent capabilities rather than pages whenever practical.
Preferred style:
users.view
users.manage
roles.view
roles.manage
or similarly consistent naming.
The exact convention must be finalized during Analysis.
Avoid overly granular permissions unless necessary.
21. Permission Naming
Permission names must follow one consistent convention.
Analysis should recommend a naming scheme that is:
- readable
- predictable
- reusable
- easy to test
Once approved, future modules should follow the same convention.
Do not mix conventions such as:
edit-users
users.create
manage_pages
without a deliberate reason.
22. Core F002 Permissions
F002 should create only permissions required for the authorization foundation.
Potential core permissions may include:
- access admin
- manage users
- manage roles
- manage permissions
Exact permissions must be finalized during Analysis.
Do not pre-create permissions for:
- Pages
- Media
- SEO
- Blog
- Services
- Projects
- Gallery
- FAQ
before those modules are implemented unless a concrete architectural need requires them.
23. Role Assignment
Only appropriately authorized users may assign roles.
Role assignment must be validated server-side.
Do not trust:
- hidden fields
- disabled dropdowns
- frontend role filtering
- JavaScript
to enforce role security.
24. Permission Assignment
Only appropriately authorized users may modify role permissions.
The system must prevent unauthorized permission escalation.
The exact administrative UI required for permission assignment must be determined during Analysis.
Avoid building a large permission-management UI if a smaller secure implementation satisfies F002.
25. User Management Boundary
F002 requires enough user-management functionality to make RBAC usable.
At minimum, Analysis must determine how an authorized administrator can:
- identify existing CMS users
- view their assigned role(s)
- assign/change an approved role
Do not automatically build a complete user-management module containing:
- profile editing
- password administration
- account deletion
- account suspension
- invitation workflows
- avatar management
unless required.
26. One Role vs Multiple Roles
Analysis must determine whether CMS users should normally have:
- one role
- multiple roles
The underlying package may support multiple roles, but product behavior should remain simple.
For a typical business CMS, a single primary administrative role may be sufficient.
Do not add UI complexity merely because a library supports multiple roles.
27. Direct User Permissions
Analysis should determine whether permissions may be assigned directly to users.
Preferred CMS design:
permissions primarily belong to roles.
Direct user permissions should be avoided unless there is a concrete use case.
This reduces authorization complexity.
28. Authorization Implementation
Prefer framework-native authorization integration.
Depending on the approved RBAC mechanism, use appropriate:
- middleware
- Gates
- Policies
- role/permission checks
Do not scatter arbitrary role-name checks throughout controllers and Blade files.
Avoid patterns such as repeatedly writing:
if ($user->role === 'admin')
throughout the application if the approved RBAC system provides a centralized mechanism.
29. Controllers
Controllers must not contain duplicated authorization logic where Laravel Policies, middleware, Gates, or the approved RBAC mechanism are more appropriate.
Keep controllers focused on request/application behavior.
30. Blade Authorization
Blade may hide controls based on permissions for usability.
Examples:
- hide user-management navigation
- hide unauthorized action buttons
However:
Blade checks do not replace server-side authorization.
Direct requests must still be protected.
31. Admin Navigation
F003 will implement the full Admin Application Shell.
Therefore F002 should not prematurely build a complete navigation system.
If minimal navigation is required to operate/test RBAC:
keep it limited to F002 needs.
Full permission-aware sidebar/navigation belongs to F003.
32. Validation Requirements
Applicable F002 validation may include:
- valid user identifier
- valid role
- valid permission
- approved role assignment
- uniqueness of role/permission names
- protection from invalid privilege changes
Never trust user-submitted role or permission identifiers without validation and authorization.
33. Mass Assignment
Security-sensitive attributes must remain protected.
Do not allow arbitrary form submission to change:
- roles
- permissions
- administrative flags
- privilege indicators
through normal model mass assignment.
Use explicit approved APIs/relationships provided by the authorization implementation.
34. Database Impact
Database changes are expected if the approved RBAC implementation requires them.
If Spatie Laravel Permission is approved, package-managed RBAC tables are expected.
Exact migrations must be identified after package approval.
Do not manually invent duplicate role/permission tables if the approved package already provides them.
Existing application tables must not be destructively changed without approval.
35. User Table Impact
Prefer not to add a simple:
role
column to users
if the approved RBAC architecture provides normalized role relationships.
Analysis must confirm the recommended design before schema changes.
Do not introduce both:
- a users.role column
- and a package role system
without a specific reason.
36. Data Preservation
Existing users created during F001 must be preserved.
RBAC migration must not delete or invalidate existing users.
Existing login credentials must continue functioning.
An existing user without an assigned authorized role may become unable to access the CMS admin area after F002 until appropriately assigned.
That is acceptable and expected if defined by the approved implementation.
37. Seed Data
If roles and permissions require baseline data:
use an approved controlled mechanism such as:
- seeder
- provisioning command
- package-supported pattern
Seed data should be:
- deterministic
- safe
- idempotent where practical
Do not hard-code passwords into seeders.
38. Package Configuration
If an approved package publishes configuration:
change only configuration required by the project.
Do not customize every available package option.
Keep default behavior where it safely satisfies requirements.
39. Cache Considerations
If the approved RBAC package caches permissions:
Analysis must understand its cache behavior.
Role/permission changes must not create unexpected stale authorization.
Do not add separate external cache infrastructure for F002.
40. Route Impact
Expected route areas may include:
- admin dashboard authorization
- role administration if required
- permission administration if required
- minimal user-role administration if required
Exact routes must be proposed after repository analysis.
All F002 administrative routes must use appropriate:
- authentication
- authorization
middleware.
41. UI / View Impact
Potential F002 UI may include minimal screens for:
- user role assignment
- roles
- role permission assignment
Only implement screens actually required by the approved F002 plan.
Do not build the full F003 admin shell.
Reuse existing admin layout components where appropriate.
42. Package / Dependency Impact
Expected candidate:
spatie/laravel-permission
Current status:
Evaluation required
Installation status:
Not approved
No dependency may be installed during Analysis.
43. Configuration Impact
Potential impact may include:
- authorization middleware aliases
- package configuration
- role/permission configuration
- registration-route behavior
Exact changes must be determined during Analysis.
Do not change authentication guards unnecessarily.
The existing Laravel web/session authentication architecture should remain the default.
44. Security Requirements
F002 is a security-sensitive feature.
It must follow:
docs/06_SECURITY_RULES.md
Specific concerns include:
- privilege escalation
- unauthorized role assignment
- unauthorized permission assignment
- Super Admin protection
- admin-route protection
- direct URL/request access
- mass assignment
- secure initial administrator provisioning
- disabling unrestricted registration
- preserving password/authentication protections
45. Authorization Negative Paths
Testing must prove both:
authorized access
and
unauthorized denial.
A successful Super Admin test alone is insufficient.
46. Testing Requirements
Focused tests should cover applicable behavior including:
Guest
- guest cannot access protected admin area
- guest follows existing login redirect behavior
Authenticated Without Admin Authorization
- authenticated unauthorized user cannot access protected admin area
Super Admin
- Super Admin can access protected administrative functionality
- Super Admin authorization behaves according to approved strategy
Roles
- authorized user can perform approved role-management operations
- unauthorized user cannot manage roles
Permissions
- authorized user can manage approved permissions
- unauthorized user cannot modify permissions
User Role Assignment
- authorized user can assign approved role
- unauthorized user cannot assign role
- invalid role assignment is rejected
Registration
If public registration is disabled:
- registration route is unavailable or blocked according to approved implementation
Regression
- login continues to work
- logout continues to work
- password reset behavior remains intact
- frontend homepage remains public
47. Super Admin Security Tests
Where applicable, test:
- lower role cannot grant itself Super Admin
- lower role cannot modify protected Super Admin privileges
- unauthorized direct requests are rejected
- Super Admin retains intended access
Exact tests depend on the approved Super Admin strategy.
48. Database Tests
If RBAC tables are introduced, tests should verify behavior through the application/RBAC API rather than testing package internals unnecessarily.
Do not create low-value tests merely proving that package migrations created expected vendor tables.
Focus on application authorization behavior.
49. Package Tests
Do not duplicate the dependency vendor's own test suite.
Test how Diwakar Enterprise CMS uses the package.
50. Focused Test Strategy
During implementation:
run only F002-related tests first.
Potential groups may include:
- authorization tests
- admin-access tests
- role tests
- permission tests
- relevant F001 regression tests
Do not repeatedly run the complete test suite during implementation.
51. Final Validation
After implementation review and explicit approval for final validation, perform applicable checks:
- focused RBAC tests
- complete Laravel test suite
- Laravel Pint
- frontend build if UI/assets changed
- route verification
- migration review
- security review
- final local diff review
Follow:
docs/04_TESTING_STRATEGY.md
52. Manual Verification
Potential manual review may include:
Unauthorized User
Login as an authenticated user without administrative authorization.
Attempt:
/admin/dashboard
Expected:
access denied according to approved behavior.
Super Admin
Login as the provisioned Super Admin.
Expected:
authorized access to the admin dashboard and approved RBAC administration functionality.
Registration
If public registration is disabled:
attempt:
/register
Expected:
registration is not publicly available according to the selected implementation.
Exact manual steps must be updated after implementation.
53. Existing Files Must Be Inspected
Before proposing implementation, inspect only files relevant to F002.
Likely areas include:
- current authentication/user model
- routes/admin.php
- routes/auth.php
- bootstrap/app.php
- current admin controller/view structure
- existing F001 tests
- relevant Breeze registration behavior
- current database migrations
- Composer package state
Do not scan unrelated content modules.
54. Local Git Baseline
Before modifications:
use local read-only Git inspection where useful.
Examples:
git status
git diff
Preserve all user changes.
Do not:
- reset
- restore
- revert
- commit
- fetch
- pull
- push
unless explicitly instructed within the project rules.
55. Pre-existing Pint Findings
F001 identified pre-existing formatting findings in:
app/View/Components/FrontendLayout.php
routes/frontend.php
These are not automatically part of F002.
Do not modify them merely to make project-wide Pint clean unless:
- F002 genuinely requires modification of one of these files
- or the user separately approves cleanup
Report them as pre-existing where necessary.
56. GitHub Restriction
Do not access GitHub or any other Git remote.
Do not:
- use GitHub CLI
- use GitHub APIs
- use GitHub connectors
- fetch
- pull
- push
- clone
- inspect remote branches
- create pull requests
The user handles GitHub manually.
57. Analysis Questions
F002 Analysis must explicitly answer:
1. Is spatie/laravel-permission the recommended RBAC solution for this project?
2. Does installing it require migrations/configuration/middleware changes?
3. What minimum roles should exist now?
4. What minimum permissions should exist now?
5. Should CMS users have one role or multiple roles at this stage?
6. Should direct user permissions be allowed?
7. What Super Admin strategy is recommended?
8. How should the first Super Admin be provisioned safely?
9. Can the existing F001 development user be promoted safely?
10. How should public registration be disabled?
11. How should unauthorized authenticated users be handled?
12. What minimal user/role administration UI is required in F002?
13. Which future functionality should remain deferred to F003 or later?
14. What migrations are expected?
15. What security tests are required?
58. Decisions Requiring User Approval
F002 must not proceed to package installation/implementation until the user has approved the significant decisions resulting from Analysis.
At minimum, approval should cover:
- RBAC implementation mechanism/package
- initial roles
- core permissions
- Super Admin behavior
- Super Admin provisioning method
- public registration behavior
- minimum user-role management scope
59. Expected Product Direction
Unless F002 Analysis identifies a compelling reason otherwise, the preferred direction is:
- Laravel Breeze remains authentication foundation
- RBAC is layered on top of existing authentication
- Super Admin is the highest CMS role
- administrative access requires authorization
- public registration is disabled
- permissions primarily belong to roles
- direct user permissions are avoided
- role model remains small
- future module permissions are added when modules are implemented
- full admin navigation remains F003
This is a preferred direction, not permission to install or implement without the required approval gates.
60. Implementation Plan
Status:
Pending Analysis
The implementation plan must be created only after targeted repository inspection.
Normal plan size:
approximately 3–7 meaningful steps.
If package installation is recommended:
the package approval gate occurs before installation.
61. Implementation Record
Files Created
Pending.
Files Modified
Pending.
Files Removed
Pending.
Migrations
Pending Analysis / Package Approval.
Dependencies
Candidate:
spatie/laravel-permission
Approval:
Pending
Roles Created
Pending.
Permissions Created
Pending.
Tests Added / Updated
Pending.
62. Validation Record
Focused Tests
Pending.
Regression Suite
Pending.
Laravel Pint
Pending.
Frontend Build
Pending / Not applicable.
Routes
Pending.
Migration Review
Pending.
Security Review
Pending.
Final Diff Review
Pending.
63. Known Limitations
Before implementation:
- final RBAC package decision pending
- final role model pending Analysis
- Super Admin implementation strategy pending
- provisioning strategy pending
- public registration remains available until F002 implementation is approved and completed
Additional limitations:
Pending.
64. Completion Record
Implementation:
Pending
Acceptance Criteria:
Pending
Focused Testing:
Pending
User Code Review:
Pending
Final Validation:
Pending
User Review:
Pending
Final User Approval:
Pending
Feature Status:
Planned
65. Required Workflow
F002 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Analysis
→ RBAC/Package Recommendation
→ USER APPROVES SIGNIFICANT DECISIONS
→ PACKAGE APPROVAL IF REQUIRED
→ Approved Implementation Plan
→ Implementation
→ Focused RBAC/Security Tests
→ USER CODE REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ Finalization Report
→ USER FINAL APPROVAL
→ Mark F002 Complete
→ Identify F003 only
→ ASK BEFORE F003 ANALYSIS
66. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F002:
- inspect only authorization-related source
- do not inspect future content modules
- do not design F003
- do not create future module permissions prematurely
- use existing Laravel/Breeze context
- perform package research only when required
- prefer official package documentation if current compatibility must be verified
- avoid multiple RBAC package comparisons unless necessary
- run focused tests during implementation
- defer complete regression testing until final validation
67. Completion Rule
F002 may be considered ready for final approval only when:
- approved RBAC mechanism is implemented
- admin access requires authorization
- Super Admin behavior is implemented and tested
- initial Super Admin can be provisioned securely
- role assignment is secured
- permission assignment is secured where included
- unauthorized authenticated users are denied admin access
- public-registration behavior matches the approved decision
- existing Breeze authentication continues working
- focused security tests pass
- final regression validation passes
- migrations are reviewed
- final diff remains within F002
- user has reviewed the implementation
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete