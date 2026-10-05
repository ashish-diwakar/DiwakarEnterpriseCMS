Diwakar Enterprise CMS — Security Rules
1. Purpose
This document defines mandatory security rules for Diwakar Enterprise CMS.
Security is a core product requirement.
Security controls must not be weakened merely to:
- make development easier
- make a test pass
- reduce implementation time
- reduce token usage
- avoid fixing an underlying problem
Agents must follow these rules together with:
- AGENTS.md
- ARCHITECTURE.md
- docs/02_ENGINEERING_RULES.md
- docs/03_DEVELOPMENT_WORKFLOW.md
- docs/04_TESTING_STRATEGY.md
2. Security Principle
Use defense in depth.
Do not rely on a single security mechanism.
Where applicable, combine:
- authentication
- authorization
- server-side validation
- database constraints
- CSRF protection
- output escaping
- secure storage
- secure configuration
- auditability
- negative-path testing
3. Authentication
Administrative functionality must require authentication unless explicitly defined otherwise.
Do not expose administrative pages simply because they are not linked from the frontend.
Protected routes must use appropriate authentication middleware.
4. Public Registration
Public registration may remain temporarily enabled during early development.
It is not intended to become an unrestricted production CMS administrator-registration mechanism.
The RBAC/Super Admin feature must define:
- how the initial administrator is provisioned
- whether public registration remains available
- who may create additional users
- what role new users receive
Do not make permanent registration decisions outside that feature.
5. Password Handling
Never:
- store plaintext passwords
- log passwords
- display passwords
- email passwords
- hard-code default production passwords
- commit passwords to source control
Use Laravel's approved password hashing facilities.
Do not implement custom password hashing.
6. Password Validation
Password requirements should follow approved project requirements and Laravel capabilities.
Do not introduce arbitrary password rules merely because they appear more secure.
Security rules should balance:
- strength
- usability
- framework support
- actual risk
Any significant password-policy change should be included in the relevant feature specification.
7. Password Reset
Password reset functionality must use secure Laravel mechanisms.
Reset tokens must not be:
- logged
- exposed in public pages
- stored in plain application logs
- reused improperly
Password-reset responses should not expose sensitive account information unnecessarily.
8. Email Verification
If email verification is enabled:
- verification links must use Laravel-supported mechanisms
- access requiring verification must be enforced server-side
- verification state must not be controlled directly by normal user input
Do not assume email verification alone provides authorization.
9. Authorization
Authentication and authorization are different controls.
Being logged in does not automatically permit administrative actions.
Protected operations must validate whether the authenticated user is authorized.
Use approved mechanisms such as:
- Policies
- Gates
- authorization middleware
- role/permission middleware after RBAC is implemented
10. Server-Side Enforcement
Security must be enforced server-side.
Do not rely on:
- hidden navigation
- hidden buttons
- disabled form fields
- JavaScript conditions
- CSS
- obscure URLs
UI restrictions may improve usability, but backend authorization remains mandatory.
11. Super Admin
A Super Admin concept is planned.
Do not automatically implement unrestricted authorization bypass.
The RBAC feature must explicitly define:
- Super Admin privileges
- bypass behavior if any
- restrictions
- audit expectations
- protection against accidental privilege removal
Super Admin behavior must be tested.
12. Role and Permission Changes
Role and permission management is security-sensitive.
Applicable implementation must prevent:
- unauthorized role assignment
- unauthorized permission assignment
- privilege escalation
- users granting permissions they are not allowed to grant
- accidental removal of required administrative access
Exact behavior belongs in the RBAC feature specification.
13. Privilege Escalation
Every administrative feature must consider whether request parameters could be manipulated to gain additional privilege.
Examples include attempting to modify:
- role IDs
- permission IDs
- owner/user IDs
- administrative flags
- publication rights
- security settings
Sensitive fields must not be trusted simply because they originate from a form.
14. Form Requests and Validation
All external input must be validated server-side.
Validation should reject values outside approved requirements.
Do not trust:
- browser validation
- JavaScript validation
- Alpine.js validation
- select-option lists
- hidden fields
Attackers can send requests directly.
15. Allowed Values
For security-sensitive values, validate against explicit allowed values.
Do not accept arbitrary request values for:
- status
- role
- permission
- sort fields
- file type
- action name
- redirect URL
- configurable security behavior
Use enums, validation rules, or approved lookup data where appropriate.
16. Mass Assignment
Never persist unrestricted request data.
Avoid:
$request->all()
for model create/update operations.
Use:
- validated data
- explicit field selection
- appropriate model mass-assignment protection
Sensitive fields must not become mass assignable merely for convenience.
17. CSRF Protection
Laravel CSRF protection must remain enabled for normal web forms and state-changing web requests.
Do not disable CSRF middleware to solve form errors.
Investigate and fix the underlying form/request implementation.
18. GET Requests Must Not Perform Destructive Actions
Do not use GET routes for operations that:
- delete records
- change status
- publish/unpublish content
- modify permissions
- alter settings
- perform other state changes
Use appropriate HTTP methods.
19. XSS Protection
Blade escaped output should be the default.
Use:
{{ $value }}
for untrusted content.
Do not render user-managed values using raw output without a defined sanitization strategy.
20. Rich Text Content
If the CMS later supports rich HTML content:
Do not simply trust stored HTML.
The approved feature must define:
- allowed HTML
- sanitization mechanism
- disallowed elements
- disallowed attributes
- script/event-handler handling
- URL handling
Rich-text security must be reviewed before raw rendering is permitted.
21. JavaScript Injection
Do not place untrusted content directly into JavaScript contexts.
When server data must be passed to JavaScript:
use Laravel/Blade-supported safe encoding mechanisms.
Avoid manual string concatenation into script blocks.
22. SQL Injection
Use:
- Eloquent
- Laravel query builder
- parameterized queries
Do not concatenate untrusted input into SQL.
Raw SQL must use parameter binding.
23. Dynamic Sorting
Do not pass arbitrary user-provided column names directly into:
- orderBy
- raw queries
- dynamic SQL
Whitelist permitted sort fields.
The same principle applies to dynamic filter fields.
24. Route Model Binding
Route Model Binding may be used where appropriate.
Authorization must still be performed.
Successfully binding a model does not mean the user is authorized to access it.
25. ID Manipulation
Do not assume record IDs provided by users are authorized.
Consider insecure direct object reference risks.
Example:
User A changing a URL from:
/admin/resource/10
to:
/admin/resource/11
must not gain unauthorized access.
26. Resource Ownership
If future features introduce user-owned resources:
authorization must verify ownership or approved elevated permission.
Do not rely on hidden IDs or filtered listings alone.
27. File Upload Security
Every upload feature must validate applicable properties:
- MIME type
- extension
- size
- content category
- authorization
- storage location
Do not trust the browser-provided file name or MIME type alone.
28. Executable Files
Do not allow uploaded executable/server-side files into web-executable locations.
Examples requiring strict control include:
- PHP
- shell scripts
- executable binaries
- server configuration files
Media Library behavior must explicitly define allowed upload types.
29. Uploaded File Names
Do not use untrusted original file names as the sole storage identity.
Prefer safe generated names.
Original file names may be retained as metadata where useful and safe.
Prevent:
- path traversal
- directory injection
- unsafe special characters
- unintended overwrite
30. Image Uploads
For image features, validate actual supported image formats.
Do not treat an arbitrary file with an image extension as a valid image.
Image-processing behavior must avoid unnecessary resource exhaustion.
31. File Size
Every upload feature must enforce reasonable maximum file sizes.
Do not allow unlimited uploads.
Exact limits should be defined by the relevant feature.
32. File Downloads
If the CMS later serves protected downloads:
authorization must be checked before serving the file.
Do not expose protected storage paths directly.
33. Path Traversal
Never construct filesystem paths directly from untrusted user input without safe normalization and validation.
Do not allow values such as:
../
to escape intended storage locations.
Use Laravel filesystem abstractions.
34. Secrets
Never commit or expose:
- passwords
- API keys
- OAuth secrets
- access tokens
- refresh tokens
- private keys
- database passwords
- encryption secrets
- GitHub tokens
Secrets belong in protected environment configuration.
35. .env
.env contains environment-specific configuration and secrets.
Do not:
- commit .env
- expose .env contents in agent reports
- paste sensitive .env values into documentation
- add actual production credentials to .env.example
36. .env.example
.env.example may contain:
- safe variable names
- non-sensitive defaults
- placeholders
It must not contain real secrets.
Example placeholder behavior is acceptable.
Real credentials are not.
37. Application Key
Laravel APP_KEY is sensitive application configuration.
Do not expose or reuse production keys.
Do not regenerate the application key in an existing environment without understanding the consequences.
Changing the key can invalidate encrypted data.
38. Debug Mode
Production environments must not intentionally expose Laravel debugging details publicly.
Development may use debugging as required.
Do not solve an issue by enabling unsafe production debug behavior.
39. Error Messages
Public-facing errors should not expose:
- database credentials
- SQL details
- stack traces
- filesystem paths unnecessarily
- secret configuration
- tokens
- internal authorization details
Detailed diagnostic information belongs in secure development/logging contexts.
40. Logging
Do not log sensitive information.
Avoid logging:
- passwords
- reset tokens
- access tokens
- private keys
- API secrets
- full authentication payloads
- sensitive personal data without need
Logs should contain enough information for troubleshooting without creating a new security risk.
41. Activity Logs
Administrative audit logs and application technical logs serve different purposes.
Future Activity Log functionality should capture meaningful administrative actions without exposing sensitive values.
Do not automatically log complete before/after payloads for security-sensitive data.
42. Session Security
Use Laravel session facilities.
Do not build a custom authentication session mechanism.
Security-sensitive flows should consider:
- session regeneration after login
- proper logout
- invalidation behavior
- framework-standard CSRF/session protections
Do not weaken these mechanisms.
43. Cookies
Security-sensitive cookies should use Laravel/framework configuration.
Do not manually create insecure authentication cookies.
Production cookie configuration should eventually consider:
- Secure
- HttpOnly
- SameSite
according to deployment requirements.
44. Remember Me
If persistent login/"remember me" behavior is enabled, use Laravel-supported functionality.
Do not implement custom long-lived authentication tokens without a defined security requirement.
45. Open Redirects
Do not redirect users to arbitrary request-provided URLs without validation.
Prefer:
- named routes
- application-controlled destinations
- safe intended redirects
Validate any user-controllable redirect target.
46. External Links
When administrators may configure external links:
validate URLs appropriately.
When opening untrusted external links in a new tab, consider applicable browser protections.
Exact rendering behavior should remain consistent with feature requirements.
47. HTML Forms
Sensitive operations should use appropriate methods such as:
- POST
- PUT/PATCH
- DELETE
and include CSRF protection.
Do not perform state changes through simple links using GET.
48. Destructive Actions
Delete and other destructive actions require appropriate authorization.
For high-impact operations, consider whether confirmation is required by the feature.
Do not infer confirmation requirements without specification, but identify significant risk during planning.
49. Soft Delete Security
If soft deletes are used:
authorization should apply to:
- delete
- restore
- permanent delete
Restoring a record must not bypass normal authorization.
50. Configuration Security
Security-related configuration should not be editable by ordinary users.
If future CMS settings expose sensitive configuration, access must be explicitly restricted.
Do not expose raw environment variables through administration screens.
51. Website Settings
General site settings such as:
- business name
- address
- telephone
- public email
- branding
are different from security configuration.
Do not mix secrets or infrastructure credentials into normal website settings.
52. Production Credentials
Do not create interfaces that display:
- database passwords
- cloud secrets
- SMTP passwords
- API secret keys
unless an explicit secure credential-management feature is separately approved.
53. External Services
Do not connect to external services without explicit approval.
If an approved integration requires credentials:
- use environment-based configuration
- avoid logging credentials
- avoid exposing credentials to Blade/JavaScript
- restrict access to relevant configuration
54. GitHub
Agents must not connect to GitHub.
Do not:
- authenticate with GitHub
- use GitHub tokens
- access GitHub APIs
- use GitHub connectors
- inspect private repositories remotely
- push/fetch/pull
The user handles GitHub manually.
55. Source Control Secrets
Before finalizing a feature, review changes for accidental secrets.
Be especially alert to:
- .env
- copied configuration
- debug logs
- credentials pasted into tests
- API examples containing real tokens
Do not perform remote secret scans unless explicitly approved.
56. Dependency Security
Do not install dependencies casually.
Every new package increases security and maintenance surface.
Before approval, consider:
- package necessity
- maintenance status
- framework compatibility
- dependency footprint
- known security concerns where current verification is required
Do not perform broad web research unless needed.
57. Dependency Updates
Do not upgrade packages merely to obtain newer versions.
Security-driven updates should be treated deliberately.
If a known security issue materially affects the installed application:
report it and propose the narrowest safe update.
Obtain approval before significant dependency changes.
58. Vendor Code
Never directly modify:
vendor/
or third-party package source under:
node_modules/
Security fixes should use supported package upgrades or extension points.
59. Authorization Tests
Sensitive administrative features should include negative authorization tests.
Examples:
- guest cannot access
- Viewer cannot modify
- Editor cannot perform Admin-only operation
- unauthorized direct request is rejected
Exact roles depend on approved RBAC specifications.
60. Authentication Tests
Authentication functionality should test applicable behavior such as:
- successful login
- failed login
- logout
- protected-route behavior
- password reset
- registration where currently enabled
Do not rely solely on manual browser testing.
61. Validation Tests
Security-relevant validation should have tests for important invalid input.
Examples:
- invalid role
- invalid file type
- invalid ID
- protected field modification
- malformed URL
- oversized upload
62. Mass Assignment Tests
Where privilege-related fields exist, tests should verify ordinary requests cannot manipulate protected values.
This is particularly important for:
- roles
- permissions
- security flags
- ownership
- administrative status
63. File Upload Tests
When file uploads are implemented, security testing should include applicable cases:
- allowed type succeeds
- disallowed type fails
- oversized file fails
- unauthorized user fails
- storage behavior is safe
Use test/fake storage.
64. XSS Tests
When user-controlled content may render as HTML or appear in sensitive contexts, add relevant XSS-oriented tests.
Do not create broad security test suites for simple escaped text fields where Blade's default behavior already covers the requirement unless risk justifies it.
65. SQL Injection Testing
Normal Eloquent usage generally provides query parameterization.
Do not create artificial SQL injection tests for every query.
Give additional scrutiny to:
- raw SQL
- dynamic ordering
- dynamic column names
- dynamic expressions
66. Security Review per Feature
Every feature should receive a proportional security review.
Small display-only feature:
minimal review.
Authentication/RBAC/file upload/user administration:
stronger review.
Do not perform a full penetration test for every minor feature.
67. Security Findings Outside Scope
If an agent discovers a security issue unrelated to the current feature:
Do not silently ignore a serious issue.
Classify it roughly as:
- critical
- high
- medium
- low
without overstating certainty.
If it creates immediate material risk:
STOP and report it.
If it is non-blocking:
record it as a recommended future security task.
Do not perform a large unrelated refactor without approval.
68. Security Through Obscurity
Do not consider a feature secure merely because:
- the route is difficult to guess
- navigation does not expose it
- an ID is random-looking
- only administrators know the URL
Security must be enforced programmatically.
69. Rate Limiting
Use Laravel rate limiting where an approved feature requires abuse protection.
Potential candidates may include:
- login
- password reset
- public contact forms
- public APIs
Do not introduce complex rate-limiting infrastructure without need.
70. Brute Force Protection
Authentication should rely on Laravel-approved throttling/security mechanisms where applicable.
Do not invent custom lockout logic without a specific requirement.
71. Contact Forms
When public contact functionality is implemented, consider:
- server-side validation
- rate limiting
- spam protection
- safe output rendering
- email-header safety
- input length limits
Do not automatically add paid CAPTCHA services without approval.
72. Spam Protection
Prefer low-cost, maintainable measures first.
Potential options may include:
- honeypot
- rate limiting
- validation
- timing-based techniques
External CAPTCHA providers require separate approval.
73. SEO Security
SEO metadata is still user-managed input.
Escape values appropriately when rendering:
- title
- description
- Open Graph fields
- structured-data values
Do not assume SEO fields are safe simply because only administrators edit them.
74. Structured Data
When generating JSON-LD or other structured data from CMS content:
use safe JSON encoding.
Do not manually concatenate untrusted strings into JSON/JavaScript output.
75. Admin UI
Admin UI must not expose security-sensitive details unnecessarily.
Examples:
- password hashes
- authentication tokens
- internal secret keys
- environment values
Administrator access does not justify exposing raw secrets.
76. User Listing
When user management is implemented:
display only fields required for administration.
Do not expose:
- password hashes
- remember tokens
- reset tokens
- private security data
77. Audit Information
If administrative audit data contains:
- IP addresses
- user-agent data
- identifiers
store and display only what is justified by the approved requirements.
Avoid unnecessary surveillance-style logging.
78. Data Minimization
Store only data required by product functionality.
Do not collect extra personal information merely because it might be useful later.
Future privacy requirements should influence data collection decisions.
79. Database Credentials
Database credentials belong in environment configuration.
Do not:
- commit them
- display them in admin
- place them in documentation
- embed them in migrations
- write them in tests
80. Database User Permissions
Production database-user privilege design will be determined during deployment planning.
Do not assume development root-level database credentials are appropriate for production.
81. Backups
Backup architecture is not yet approved.
If backup functionality is introduced later, it must consider:
- confidentiality
- storage security
- access control
- retention
- restoration
- encryption where appropriate
Do not automatically upload backups to an external provider.
82. Encryption
Use Laravel-supported encryption where an approved feature requires application-level encrypted values.
Do not invent custom cryptography.
Do not encrypt everything unnecessarily.
Encryption does not replace authorization.
83. Hashing vs Encryption
Passwords should be hashed, not reversibly encrypted.
Use encryption only when the application genuinely needs to retrieve the original value.
Do not confuse the two.
84. Sensitive Data in URLs
Avoid placing secrets or sensitive information in URLs/query strings.
URLs may be logged by browsers, servers, proxies, and analytics systems.
Use secure framework-supported flows for sensitive tokens.
85. Caching Security
If caching is introduced:
do not cache sensitive or user-specific data in a way that could leak between users.
Cache keys must appropriately distinguish security contexts when required.
86. Authorization Caching
If permission caching is introduced by an approved RBAC package:
understand invalidation behavior.
Role/permission changes should not leave stale unauthorized access longer than intended.
87. API Security
If APIs are introduced later:
the feature specification must define:
- authentication
- authorization
- rate limiting
- validation
- response data
- CORS
- versioning where relevant
Do not create unsecured endpoints merely for frontend convenience.
88. CORS
Do not broaden CORS configuration without a concrete requirement.
Avoid permissive production settings such as unrestricted origins unless explicitly justified.
89. HTTP Security Headers
Production HTTP security-header strategy may be defined during deployment/security hardening.
Do not add a large security-header package prematurely.
When introduced, verify compatibility before enforcing restrictive policies.
90. Content Security Policy
CSP may be considered later as part of production security hardening.
Do not implement a restrictive CSP casually without verifying:
- Vite assets
- Alpine.js
- third-party resources
- inline behavior
- frontend requirements
Treat it as a deliberate security/deployment decision.
91. HTTPS
Production administration and authentication must be served over HTTPS.
Local development may use HTTP.
Do not infer production security from local development URLs.
Deployment configuration will define HTTPS enforcement.
92. Security and Environment Differences
Do not weaken application security merely because development is local.
Some production controls may be deployment-specific, but application-level protections should remain consistent.
93. Security Review Before Finalization
Before final feature approval, check applicable items:
- authentication
- authorization
- validation
- mass assignment
- CSRF
- XSS
- SQL injection
- file handling
- privilege escalation
- sensitive information exposure
- destructive actions
- external connections
- logging
Report only relevant findings.
94. Critical Security Stop Rule
If implementation would require knowingly introducing a serious vulnerability:
STOP.
Explain the problem.
Provide the safest practical alternatives.
Do not implement insecure behavior merely because it appears to be requested indirectly.
95. Security vs Budget
The project has a limited AI budget.
Security work should still be risk-based and efficient.
Prefer:
- targeted security review
- relevant negative tests
- framework-native protections
- avoiding unnecessary dependencies
Avoid:
- broad security audits on every tiny feature
- excessive automated scanning without need
- long theoretical security reports
Do not sacrifice required security controls to reduce cost.
96. Security Research
Use web research only when current information is genuinely needed.
Examples:
- package vulnerability verification
- current official Laravel security behavior
- current dependency advisory
Prefer authoritative sources.
Do not browse broadly for generic security advice when project rules already provide the answer.
97. No Remote Security Scanning
Do not scan:
- GitHub
- external servers
- production websites
- remote infrastructure
without explicit user approval.
Local project security review remains the default.
98. Security Documentation
Do not create large security documents for each feature.
Record feature-specific security requirements and validation in the active feature specification.
Update this file only when the project's overall security policy changes.
99. Security Completion Criteria
A feature involving security-sensitive behavior is not ready for completion until:
- required authorization exists
- input validation exists
- relevant negative tests pass
- sensitive data is protected
- no known blocking vulnerability remains
- the user has reviewed the implementation
100. Final Security Principle
Default to Laravel's established security mechanisms.
Do not create custom security systems where framework-standard approaches are sufficient.
Security should be:
- explicit
- server-enforced
- testable
- maintainable
- proportional to risk
The user remains the final authority for approved security requirements and significant security architecture changes.