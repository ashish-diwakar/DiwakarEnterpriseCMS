Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\features\F001_AUTHENTICATION.md
This specification reflects the project's current state: Laravel Breeze Blade scaffolding is already installed successfully, while the next work is to integrate Breeze authentication into the CMS architecture without introducing RBAC yet.
────────────────────────────────────
COPY START — docs/features/F001_AUTHENTICATION.md
────────────────────────────────────
F001 — Authentication Integration
Status:
Planned
1. Objective
Integrate the existing Laravel Breeze authentication functionality into the Diwakar Enterprise CMS architecture.
The feature must provide a clean authentication foundation for future CMS administration while preserving Laravel Breeze's standard authentication behavior.
Authentication views should integrate with the CMS-specific view/layout structure rather than permanently relying on Breeze's default generated presentation.
This feature establishes authentication only.
Authorization, roles, permissions, and Super Admin behavior belong to F002.
2. Background
The project currently uses:
- PHP 8.2.12
- Laravel 12
- Blade
- Tailwind CSS
- Alpine.js
- Vite
- Laravel Breeze
Laravel Breeze Blade scaffolding has already been installed successfully.
The Vite production build has also completed successfully.
The project already separates:
Admin routes:
routes/admin.php
Frontend routes:
routes/frontend.php
The project also has separate frontend and administration view structures.
Existing CMS layout concepts include:
<x-admin-layout>
<x-admin-guest-layout>
<x-frontend-layout>
The exact existing source files must be inspected during the Analysis stage before implementation.
Do not assume every planned layout file already contains its final implementation.
3. In Scope
F001 includes:
- preserve Laravel Breeze authentication backend behavior
- integrate login with the CMS authentication presentation
- integrate registration with the CMS authentication presentation
- integrate forgot-password view
- integrate reset-password view
- integrate confirm-password view
- integrate email-verification view where applicable
- preserve logout functionality
- establish/reuse an admin guest authentication layout
- protect the CMS admin dashboard with authentication
- redirect unauthenticated admin access to login
- ensure authenticated access to the protected admin dashboard
- preserve the public frontend homepage
- preserve existing Breeze authentication behavior
- verify authentication functionality using focused automated tests
- verify authentication routes and relevant views
4. Out of Scope
The following are explicitly excluded from F001:
- roles
- permissions
- Spatie Laravel Permission installation
- Super Admin behavior
- Admin role behavior
- Editor role behavior
- Author role behavior
- Viewer role behavior
- user-management screens
- administrator invitation workflow
- final administrator provisioning strategy
- final public-registration policy
- two-factor authentication
- social login
- Google login
- Facebook login
- Apple login
- Microsoft login
- passkeys
- OAuth server functionality
- API authentication
- Sanctum integration unless already required by existing Breeze behavior
- final CMS visual design
- advanced admin navigation
- activity logging
- login-history reporting
- CAPTCHA
- external email-provider configuration
These items require separate approved features.
5. Dependencies
Existing dependencies:
- Laravel 12
- Laravel Breeze
- Blade
- Tailwind CSS
- Alpine.js
- Vite
- Laravel session authentication
- existing Laravel user model/database structure
Required project infrastructure:
- routes/admin.php
- routes/frontend.php
- CMS Blade layout structure
- existing Breeze authentication controllers/routes/views
No new third-party dependency is expected for F001.
6. User Roles / Actors
F001 recognizes only these authentication states:
Guest
A visitor who is not authenticated.
Authenticated User
A user successfully authenticated through Laravel's authentication system.
F001 does not distinguish authenticated users by role or permission.
Role-based authorization begins in F002.
7. User / System Behavior
Guest Visits Login
When a guest visits the login page:
- the login form is displayed
- the CMS authentication/guest layout is used
- existing Breeze login functionality remains operational
Valid Login
When valid credentials are submitted:
- authentication succeeds
- Laravel establishes the authenticated session
- the session-security behavior provided by Laravel/Breeze is preserved
- the user is redirected according to the approved application flow
The exact redirect destination must be confirmed during Analysis based on the current Breeze implementation.
The preferred CMS destination is expected to be the admin dashboard unless existing behavior or requirements indicate otherwise.
Invalid Login
When incorrect credentials are submitted:
- authentication must fail
- the user must remain unauthenticated
- a safe validation/authentication error must be displayed
- no sensitive information should be exposed
Logout
When an authenticated user logs out:
- the authenticated session must end
- the Laravel/Breeze logout behavior must be preserved
- the user should be redirected to the appropriate public/login destination
The final destination should follow the approved implementation plan.
Guest Access to Admin Dashboard
When an unauthenticated user attempts to access:
/admin/dashboard
the application must not display the dashboard.
The guest must be redirected to the login flow.
Authenticated Access to Admin Dashboard
When an authenticated user accesses:
/admin/dashboard
the dashboard should render successfully.
No role-based restriction is required in F001.
Registration
During F001 development, the Breeze registration flow may remain available.
A guest should be able to:
- access the registration page
- submit valid registration data
- receive validation errors for invalid registration data
- become authenticated according to standard Breeze behavior
Public registration is temporary.
Its production behavior will be decided in F002.
Forgot Password
The existing Breeze forgot-password workflow should remain functional.
The presentation should use the approved CMS authentication layout where applicable.
Do not configure a production mail provider as part of F001.
Reset Password
The Breeze reset-password functionality must remain functional.
Do not replace Laravel's password-reset implementation with a custom token system.
Password Confirmation
Where Breeze requires password confirmation for protected operations, the existing functionality should remain intact.
Email Verification
If email verification is currently enabled/applicable in the installed authentication implementation, preserve its functionality.
Do not introduce a new mandatory email-verification policy unless already required by the existing implementation or separately approved.
8. Business Rules
BR-001
Authentication must use Laravel/Breeze's established authentication mechanism.
Do not build a custom authentication system.
BR-002
Public registration is temporarily allowed during development.
It is not automatically approved for production.
BR-003
F001 distinguishes only:
- guest
- authenticated user
Role-based authorization is deferred to F002.
BR-004
Authentication presentation should integrate with the CMS layout architecture.
BR-005
Existing Breeze backend behavior should be preserved unless a change is specifically required by this feature.
BR-006
Admin dashboard access requires authentication.
BR-007
Authentication integration must not break the public frontend homepage.
9. Acceptance Criteria
Authentication UI
- [ ] Login page renders successfully.
- [ ] Login page uses the approved CMS authentication layout.
- [ ] Registration page renders successfully during development.
- [ ] Registration page uses the approved CMS authentication layout.
- [ ] Forgot-password page renders successfully.
- [ ] Reset-password page renders successfully when invoked through the supported flow.
- [ ] Confirm-password page remains functional where applicable.
- [ ] Email-verification page remains functional where applicable.
Login
- [ ] User can authenticate using valid credentials.
- [ ] Invalid credentials are rejected.
- [ ] Authentication errors do not disclose sensitive information.
Logout
- [ ] Authenticated user can log out.
- [ ] User is unauthenticated after logout.
Registration
- [ ] Valid registration succeeds while registration is enabled.
- [ ] Invalid registration input is rejected.
- [ ] Duplicate email registration is rejected according to existing validation.
Admin Protection
- [ ] Guest cannot access /admin/dashboard.
- [ ] Guest attempting to access /admin/dashboard is redirected to authentication.
- [ ] Authenticated user can access /admin/dashboard.
Frontend
- [ ] Public frontend homepage continues to render successfully.
Architecture
- [ ] Authentication views are integrated without unnecessary duplication.
- [ ] No custom authentication implementation replaces Breeze.
- [ ] No RBAC functionality is introduced.
- [ ] No new authentication package is installed.
Testing
- [ ] Relevant authentication-focused tests pass.
- [ ] Admin dashboard authentication behavior is tested.
- [ ] Relevant final regression tests pass during final validation.
- [ ] Frontend build passes if authentication view/layout changes affect frontend assets.
10. Security Requirements
F001 must follow:
docs/06_SECURITY_RULES.md
Specific requirements include:
- use Laravel's password hashing
- never store plaintext passwords
- never log passwords
- preserve Laravel session-security behavior
- preserve CSRF protection
- preserve password-reset security
- do not expose reset tokens
- do not expose authentication secrets
- regenerate/authenticate sessions according to Laravel/Breeze behavior
- use server-side authentication enforcement
- do not rely on hidden admin navigation for security
- do not weaken Breeze security mechanisms merely to integrate custom views
11. Validation Requirements
Existing Breeze validation should be preserved unless analysis identifies a justified project-specific change.
Applicable registration validation includes existing Breeze requirements such as:
- name required
- email required
- valid email format
- unique email
- password requirements
- password confirmation
Login must validate required authentication input.
Do not introduce stronger or additional password rules without approval.
12. Database Impact
Expected:
None
Laravel's initial authentication/database tables already exist.
The current database includes relevant Laravel tables such as:
- users
- password_reset_tokens
- sessions
F001 should not require a new application database schema.
If Analysis determines a migration is required:
STOP.
Explain why.
Request approval before adding an unexpected schema change.
13. Route Impact
Existing Breeze authentication routes must be inspected during Analysis.
Expected authentication routes include functionality for:
- login
- logout
- register
- forgot password
- reset password
- password confirmation
- email verification where applicable
Admin route behavior must ensure:
/admin/dashboard
requires authentication.
Do not add duplicate authentication routes unnecessarily.
Do not move unrelated routes.
14. UI / View Impact
Expected view areas may include:
resources/views/auth/
and/or:
resources/views/admin/auth/
CMS layout areas may include:
resources/views/components/
resources/views/admin/layouts/
Exact files must be confirmed from the actual repository during Analysis.
The intended end state is:
authentication screens use the CMS administration guest/authentication presentation while preserving Breeze behavior.
Do not perform final visual/theme design during F001.
15. View Migration Safety
If Breeze-generated authentication views are being reorganized:
prefer a safe incremental approach.
Where appropriate:
1. inspect existing Breeze views
2. create/integrate CMS equivalents
3. update references
4. test authentication
5. remove obsolete duplication only after verifying it is no longer required
Do not delete working Breeze views before replacement behavior is verified.
16. Package / Dependency Impact
Expected:
None
Laravel Breeze is already installed.
Do not install:
- Spatie Laravel Permission
- Jetstream
- Fortify as a separate replacement architecture
- Sanctum unless genuinely required by existing project behavior
- social authentication libraries
- CAPTCHA libraries
as part of F001.
If an unexpected dependency is believed necessary:
follow the package-approval workflow and STOP before installation.
17. Configuration Impact
Expected impact should be minimal.
Possible relevant areas include:
- route middleware
- authentication redirect behavior
- view references
Do not change:
- authentication driver
- session architecture
- mail provider
- database authentication storage
- application key
without separate approval.
18. Testing Requirements
Use existing Breeze authentication tests where available rather than unnecessarily recreating equivalent tests.
Inspect current tests first.
Applicable testing should verify:
Login
- login page renders
- valid user authenticates
- invalid credentials fail
Registration
- registration page renders
- valid user registration succeeds
- invalid registration is rejected where covered by existing tests
Logout
- authenticated user can log out
Password Reset
Preserve and run existing relevant Breeze password-reset tests.
Email Verification
Preserve and run existing relevant verification tests where applicable.
Admin Dashboard Protection
Add or update tests verifying:
- guest cannot access /admin/dashboard
- authenticated user can access /admin/dashboard
Frontend Regression
Verify the public home route remains functional where applicable.
19. Focused Test Strategy
During implementation:
run only relevant authentication tests first.
The exact command must be based on the actual test structure.
Potential examples include:
php artisan test tests/Feature/Auth
or selected authentication test classes.
Do not assume the test path without inspecting the repository.
Do not run the complete suite repeatedly during implementation.
20. Final Validation Requirements
After user code review and approval to proceed with final validation, perform applicable checks once.
Expected checks:
- relevant authentication tests
- complete Laravel regression suite
- Laravel Pint for changed PHP files
- frontend production build if view/assets integration requires it
- relevant route verification
- security review
- final local diff review
Do not contact GitHub.
21. Manual Verification
The user may manually verify the following pages where applicable:
Login
/login
Expected:
CMS-integrated authentication page renders correctly.
Registration
/register
Expected:
Registration page renders correctly while registration remains enabled.
Admin Dashboard as Guest
/admin/dashboard
Expected:
Redirect to authentication.
Admin Dashboard as Authenticated User
/admin/dashboard
Expected:
Dashboard renders.
Frontend Home
/
Expected:
Public frontend remains operational.
Additional password-reset/email-verification manual testing may depend on local mail configuration.
Do not claim the user completed these checks until they confirm.
22. Existing Files Must Be Inspected
Before proposing implementation, inspect only the directly relevant current files.
Likely areas include:
- authentication routes
- Breeze authentication controllers
- existing authentication views
- admin routes
- admin dashboard view
- layout components
- authentication tests
Do not assume file structure solely from this specification.
The actual repository is authoritative.
23. Git / GitHub Requirements
Local read-only Git inspection may be used according to AGENTS.md.
Before editing, inspect local working state where useful.
Preserve existing user changes.
Do not:
- access GitHub
- fetch
- pull
- push
- clone
- create pull requests
- use GitHub APIs
- use GitHub connectors
- create commits automatically
The user handles Git/GitHub manually.
24. Risks / Notes
Temporary Public Registration
Public registration currently exists as part of Breeze development scaffolding.
It must not automatically be treated as permanent production behavior.
F002 will define administrative provisioning and the final registration policy.
Layout Refactoring
Moving or replacing Breeze-generated views may break view references if performed too aggressively.
The implementation should be incremental and tested.
Authentication Redirects
Breeze's current redirect behavior must be inspected rather than guessed.
Any CMS-specific redirect change should be documented in the implementation plan.
Email-Based Flows
Password-reset and email-verification behavior may depend on the local mail environment.
Automated tests should use Laravel testing facilities rather than real external email delivery.
25. Implementation Plan
Status:
Pending Analysis
The agent must inspect the actual repository before completing this section.
The plan should normally contain approximately 3–7 meaningful steps.
Do not implement before the user approves the plan.
26. Implementation Record
Files Created
Pending.
Files Modified
Pending.
Files Removed
Pending.
Only remove files after confirming they are obsolete and safe to remove.
Migrations
Expected:
None
Final result:
Pending.
Dependencies
Expected:
None
Final result:
Pending.
Tests Added / Updated
Pending.
27. Validation Record
Focused Tests
Pending.
Regression Suite
Pending.
Laravel Pint
Pending.
Frontend Build
Pending.
Routes
Pending.
Migration Review
Expected:
Not applicable
Final result:
Pending.
Security Review
Pending.
Final Diff Review
Pending.
28. Acceptance Criteria Result
Total criteria:
To be calculated during finalization.
Satisfied:
Pending.
Not satisfied:
Pending.
Any unsatisfied required acceptance criterion blocks completion.
29. Known Limitations
Current known limitation:
Public registration is temporary and will be resolved as part of the approved RBAC/administrator-provisioning work in F002.
Additional limitations:
Pending implementation.
30. Completion Record
Implementation:
Pending
Focused Testing:
Pending
User Code Review:
Pending
Final Validation:
Pending
Acceptance Criteria:
Pending
Final User Approval:
Pending
Feature Status:
Planned
Do not mark this feature Complete until explicit user approval is received.
31. Required Workflow
F001 must use the following progression:
Analysis
→ Implementation Plan
→ USER APPROVAL
→ Implementation
→ Focused Authentication Tests
→ USER CODE REVIEW
→ Corrections if required
→ USER APPROVAL FOR FINAL VALIDATION
→ Final Validation
→ Finalization Report
→ USER FINAL APPROVAL
→ Mark F001 Complete
→ Identify F002 only
→ ASK PERMISSION BEFORE F002 ANALYSIS
Do not automatically proceed to F002.
32. Low-Cost Processing Requirement
Follow:
docs/08_COST_CONTROL.md
For F001:
- inspect only authentication-related files
- reuse existing Breeze tests where possible
- avoid broad repository scans
- avoid web research unless genuinely required
- do not research RBAC yet
- do not research Spatie yet
- do not design F002
- run focused tests before the full suite
- wait for user review before expensive final validation
33. F001 Completion Rule
F001 may be presented as ready for completion only when:
- authentication integration works
- CMS authentication layout works
- login works
- logout works
- development registration works
- relevant password flows remain intact
- admin dashboard requires authentication
- authenticated user can access admin dashboard
- public frontend remains operational
- focused tests pass
- final regression checks pass
- security review passes
- final diff is clean
- user has reviewed the implementation
The agent must then request final approval.
Only the user can authorize changing:
Status:
from:
Ready for Final Approval
to:
Complete