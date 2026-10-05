Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\02_ENGINEERING_RULES.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/02_ENGINEERING_RULES.md
────────────────────────────────────
Diwakar Enterprise CMS — Engineering Rules
1. Purpose
This document defines implementation rules for Diwakar Enterprise CMS.
These rules apply to normal feature development unless an approved feature specification or architectural decision explicitly requires otherwise.
The primary goals are:
- correctness
- security
- maintainability
- readability
- testability
- predictable Laravel conventions
- minimal unnecessary complexity
- low AI-processing cost
2. General Engineering Principle
Make the smallest safe change that satisfies the approved requirement.
Do not:
- redesign unrelated code
- refactor working areas without need
- add speculative functionality
- create abstractions for future possibilities
- change project-wide patterns during a small feature
- upgrade dependencies merely because newer versions exist
Prefer incremental development.
3. Laravel Conventions First
Use Laravel-native capabilities before introducing custom infrastructure.
Prefer:
- Eloquent
- Form Requests
- Policies
- Gates
- middleware
- Blade
- Blade components
- Laravel validation
- Laravel filesystem
- Laravel notifications
- Laravel mail abstraction
- Laravel configuration
- Laravel testing facilities
Do not recreate framework functionality without a concrete reason.
4. PHP Coding Standard
Use modern, readable PHP compatible with the approved project PHP version.
Follow PSR conventions and Laravel project conventions.
Use:
- meaningful class names
- meaningful method names
- explicit return types where practical
- typed parameters where appropriate
- clear control flow
- early returns when they improve readability
Avoid:
- deeply nested conditionals
- unnecessarily clever code
- cryptic variable names
- large methods with multiple responsibilities
5. Strict Types
Do not add or remove declare(strict_types=1); across the project merely for consistency.
If a file already uses strict types, preserve it.
For newly created application files, use the existing project convention.
Do not perform a repository-wide strict-types refactor unless explicitly approved.
6. Controllers
Controllers should remain thin.
Controllers may:
- accept requests
- receive route-bound models
- invoke authorization
- use validated request data
- invoke Actions or Services when needed
- return views
- return redirects
- return responses
Controllers should not contain:
- large business workflows
- complex persistence orchestration
- repeated validation rules
- reusable calculations
- file-processing implementations
- external integration details
7. Form Requests
Use Form Request classes for meaningful create/update forms and non-trivial validation.
Form Requests should contain:
- validation rules
- appropriate authorization where useful
- custom messages only where they improve usability
- safe input preparation when required
Do not create a Form Request for trivial requests where Laravel inline validation is clearly simpler.
8. Validation Rules
All external input must be validated server-side.
Consider validation for:
- required fields
- maximum lengths
- formats
- allowed values
- numeric ranges
- dates
- URLs
- email addresses
- foreign-key existence
- uniqueness
- uploaded file type
- uploaded file size
Validation should reflect actual business requirements rather than arbitrary restrictions.
9. Validated Persistence
Persist validated or explicitly selected data.
Prefer:
$request->validated()
or explicit field selection.
Avoid:
$request->all()
for model persistence.
Do not allow request input to control sensitive fields unless explicitly intended.
Examples of sensitive fields may include:
- role
- permissions
- administrative status
- publication ownership
- internal flags
- protected metadata
10. Authorization
Authorization must be enforced on the server.
For sensitive operations, verify that the current user has permission to perform the action.
Do not treat any of the following as authorization:
- hiding a link
- hiding a button
- disabling a form field
- omitting a navigation item
- relying on an obscure URL
Use the approved authorization architecture.
11. Authentication
Do not duplicate authentication checks manually when Laravel middleware can provide them clearly.
Administrative routes should ultimately use appropriate authentication middleware.
Do not expose administrative actions publicly because the UI does not link to them.
12. Eloquent Models
Keep models focused on persistence-related behavior and meaningful domain behavior.
Models may contain:
- relationships
- casts
- scopes
- accessors
- mutators
- small cohesive behavior
Avoid putting unrelated infrastructure or HTTP concerns into models.
Models must not depend on controllers or Blade views.
13. Eloquent Relationships
Define relationships explicitly.
Use appropriate relationship types.
Consider:
- foreign-key constraints
- deletion behavior
- eager loading
- optional relationships
- pivot-table requirements
Avoid repeated manual queries when a relationship expresses the domain more clearly.
14. N+1 Query Prevention
Be alert for N+1 queries.
When displaying lists with related data:
- inspect required relationships
- eager-load where appropriate
- avoid loading unnecessary large relationships
Do not blindly eager-load every relation.
Optimize based on actual usage.
15. Query Efficiency
Use Eloquent and Laravel query builder by default.
Avoid raw SQL unless it provides a clear benefit.
When using raw SQL:
- use parameter binding
- document why it is needed
- ensure portability where relevant
- test it
Consider indexes for fields frequently used in:
- filtering
- joins
- ordering
- unique lookup
- slug lookup
16. Database Transactions
Use database transactions when multiple writes represent one logical operation.
Example:
- create a page
- create its dependent records
- assign required relationships
If part of the logical operation fails, the operation should roll back when appropriate.
Do not add transactions around every single independent write.
17. Migrations
Every schema change must use a Laravel migration.
Migrations should:
- have a clear purpose
- be minimal
- consider existing data
- include appropriate indexes
- use foreign keys where appropriate
- support rollback where practical
Do not modify an already-applied historical migration merely to change current behavior.
Create a new migration for subsequent schema changes.
18. Destructive Schema Changes
Potentially destructive schema changes require explicit user approval.
Examples include:
- dropping columns
- dropping tables
- changing data types with data-loss risk
- removing constraints with security/data-integrity implications
- mass data conversion
Before performing such work, explain:
- reason
- impact
- rollback plan
- data risk
Then wait for approval.
19. Seeders
Use seeders for controlled baseline data where appropriate.
Do not include:
- real passwords
- production secrets
- API keys
- personal credentials
If initial administrative users require provisioning, follow the approved RBAC/security specification.
20. Factories
Use factories primarily for:
- automated tests
- safe development/test data
Prefer factories over repetitive manual test setup when they improve readability.
Do not create factories that generate unsafe or unrealistic data by default.
21. Actions
Create an Action when one meaningful application operation deserves its own class.
Examples:
- publish a page
- upload media
- create an administrative user
- update website settings
Do not create Actions for trivial one-line CRUD operations solely to follow a pattern.
22. Services
Create a Service when reusable behavior represents a clear business or integration capability.
Examples might include:
- image processing
- SEO processing
- external-provider integration
A Service must have a focused responsibility.
Avoid generic catch-all services.
23. Dependency Injection
Prefer constructor or method dependency injection where appropriate.
Avoid using service location patterns merely for convenience.
Do not instantiate complex dependencies manually when Laravel's container should manage them.
24. Interfaces
Do not create an interface automatically for every class.
Use an interface when:
- multiple implementations exist
- an external provider needs abstraction
- a meaningful test boundary is needed
- interchangeable behavior is actually required
Avoid unnecessary interface/class pairs.
25. Repository Pattern
Do not create repositories for routine Eloquent CRUD.
Introduce a repository only when there is a concrete persistence-abstraction requirement.
Any repository introduction should be justified in the feature plan.
26. Enums
Use enums for stable application states where they improve correctness.
Examples may include:
- publication state
- status
- workflow state
Do not use enums for values intended to be managed dynamically by administrators.
27. Constants and Magic Values
Avoid unexplained magic values.
Prefer:
- named constants
- enums
- configuration
- clearly named variables
depending on the nature of the value.
Do not move every literal into configuration unnecessarily.
28. Blade Views
Blade templates should focus on presentation.
Do not perform database queries directly in Blade.
Do not place complex business logic in Blade.
Views may contain:
- simple conditions
- loops
- formatting
- component composition
Prepare complex data before rendering the view.
29. Blade Escaping
Use normal escaped Blade output:
{{ $value }}
for user-controlled or untrusted content.
Use raw output only when:
- there is a clear requirement
- the content is trusted or appropriately sanitized
- XSS implications have been reviewed
Do not use raw HTML output merely to make formatted content display.
30. Blade Components
Use Blade components for reusable interface elements where they improve consistency.
Examples:
- layout
- form input
- validation error
- alert
- breadcrumb
- button
- modal
- table component
Avoid creating components for unique one-off markup that is easier to understand inline.
31. Frontend JavaScript
Use Alpine.js for small client-side interactions where appropriate.
Do not add large JavaScript frameworks without architectural approval.
Keep server-side functionality usable and secure even if client-side behavior is bypassed.
32. CSS and Tailwind
Use existing Tailwind utilities and project CSS conventions.
Avoid:
- introducing another CSS framework
- large duplicated style sheets
- excessive inline styles
- arbitrary styling abstractions without need
Reusable style patterns may be extracted when repetition becomes meaningful.
33. File Uploads
Every file upload feature must validate:
- MIME type
- file extension where appropriate
- file size
- allowed file category
- storage destination
Do not trust the original client filename.
Do not allow executable uploads into publicly executable locations.
Use Laravel storage facilities.
34. File Naming
For managed uploads, use safe application-generated file names where appropriate.
Avoid relying on user-provided names for storage identity.
Preserve original names only as metadata when useful and safe.
35. Deletion Behavior
Before deleting records, consider:
- foreign-key dependencies
- related media
- related content
- audit requirements
- accidental deletion risk
- soft deletes
- recovery requirements
Do not implement cascade deletion casually.
Exact deletion behavior belongs in the relevant feature specification.
36. Soft Deletes
Do not apply SoftDeletes to every model automatically.
Use them when recovery/audit requirements justify them.
If a feature requires soft deletion, specify expected restore/permanent-delete behavior.
37. Error Handling
Do not silently suppress errors.
Unexpected failures should be:
- handled appropriately
- logged where useful
- presented to users safely
Avoid empty exception handlers.
Do not expose stack traces or sensitive technical details intentionally in production-facing output.
38. Exceptions
Use exceptions for exceptional conditions, not normal branching.
Where custom exceptions add clarity, they may be introduced.
Do not create large hierarchies of custom exception classes without need.
39. Logging
Log meaningful operational failures and relevant technical events.
Do not log:
- passwords
- reset tokens
- access tokens
- API keys
- private keys
- unnecessary personal information
- full sensitive request payloads
Logging should be useful, not noisy.
40. Security-Sensitive Data
Treat security-sensitive values carefully.
Examples:
- credentials
- tokens
- password-reset data
- authorization state
- protected configuration
Never expose these values in:
- logs
- exceptions
- debug output
- Blade pages
- JavaScript
- documentation
- test snapshots
41. CSRF
Retain Laravel CSRF protection for ordinary web forms.
Do not disable CSRF to resolve implementation problems.
Correct the form/request implementation instead.
42. XSS
Treat user-managed content as potentially unsafe.
Use escaped output by default.
If the CMS later allows rich HTML content, define an approved sanitization strategy before rendering arbitrary stored HTML.
43. SQL Injection
Use Eloquent/query-builder parameter binding.
Do not concatenate untrusted input into SQL.
Raw queries must use safe bindings.
44. Mass Assignment
Review model mass-assignment configuration carefully.
Do not expose internal/protected attributes merely to make form persistence easier.
Use approved model configuration and validated input.
45. Open Redirects
Do not redirect to arbitrary user-provided URLs without validation.
Use named routes and application-controlled destinations where possible.
46. URL and Slug Handling
Validate and normalize slugs according to the approved feature requirements.
Consider:
- uniqueness
- reserved paths
- lowercase normalization where required
- route conflicts
Do not define complex slug infrastructure before the relevant feature requires it.
47. Pagination
Use pagination for potentially large administrative listings.
Do not load unbounded datasets into admin screens without reason.
Select sensible page sizes based on feature requirements.
48. Sorting and Filtering
Validate user-provided sort/filter parameters.
Do not dynamically pass arbitrary request field names directly into database ordering logic.
Whitelist allowed sorting/filtering fields where necessary.
49. Performance
Consider performance as part of normal implementation.
Watch for:
- N+1 queries
- unbounded result sets
- repeated expensive queries
- unnecessary asset loading
- oversized images
- repeated external calls
Do not introduce complex caching before there is a demonstrated need.
50. Caching
Use caching only where requirements or measured behavior justify it.
Do not cache security-sensitive or user-specific data carelessly.
Cache invalidation behavior must be understood before adding caching.
51. External Integrations
Do not connect to external services without approval.
If an approved feature requires an external integration:
- isolate integration logic
- use environment-based credentials
- handle failures
- consider timeouts
- consider retries where appropriate
- avoid exposing provider-specific logic throughout the application
52. Environment Variables
Use .env only for environment-specific values and secrets.
Application code should generally access these through Laravel configuration.
Avoid direct env(...) usage outside configuration files.
Do not place administrator-editable website content in .env.
53. Configuration
Use configuration files for environment-independent application configuration when appropriate.
Do not turn runtime CMS content into static config.
Do not introduce config values merely to avoid defining proper domain data.
54. Comments
Write comments when they explain:
- why something non-obvious is necessary
- a meaningful workaround
- an important constraint
- a security decision
Avoid comments that merely restate obvious code.
Good code should explain most of what it does through naming and structure.
55. TODO Comments
Do not leave unexplained TODO comments.
If a TODO is genuinely necessary:
- explain why
- keep it scoped
- associate it with a known future requirement where possible
Do not use TODOs to leave incomplete parts of an approved feature.
56. Dead Code
Do not leave:
- commented-out old implementations
- unused methods
- unused imports
- abandoned experimental code
Remove code that is no longer required by the approved implementation.
Do not remove unrelated existing code merely because it appears unused without understanding its purpose.
57. Duplication
Avoid meaningful duplication.
Small local duplication may sometimes be clearer than premature abstraction.
Refactor when duplication represents the same concept and abstraction improves maintainability.
Do not build a generic abstraction merely to eliminate two small similar blocks.
58. Naming
Use clear names based on domain meaning.
Prefer:
PublishPage
over:
ProcessData
Prefer:
StorePageRequest
over:
PageRequest1
Avoid unexplained abbreviations.
Use terminology consistently with feature specifications.
59. Method Size
Keep methods understandable and cohesive.
If a method handles several unrelated steps, consider extracting meaningful behavior.
Do not split simple readable methods into excessive tiny methods solely to reduce line counts.
60. Class Size
A large class is a signal to review responsibilities, not an automatic violation.
Refactor when a class has unrelated responsibilities or becomes difficult to understand/test.
Do not create numerous tiny classes without functional value.
61. Return Types
Use return types where they improve clarity and are compatible with Laravel conventions.
Do not perform a project-wide return-type refactor during unrelated work.
62. Null Handling
Be explicit about optional values.
Database nullability, validation rules, and application handling should agree.
Avoid using empty strings, zero, and null interchangeably without a defined meaning.
63. Date and Time Handling
Use Laravel/Carbon conventions.
Store timestamps using the application's defined database conventions.
Do not introduce custom date libraries without need.
Consider timezone implications when feature requirements involve user-facing dates.
64. Money and Financial Values
If future modules involve monetary amounts:
- do not use floating-point arithmetic for financial calculations without careful review
- define currency behavior explicitly
- determine storage precision before implementation
No generic financial architecture is required until a feature needs it.
65. SEO Data
Do not duplicate common SEO logic across modules once the SEO architecture is established.
Until then, implement only SEO fields explicitly required by approved features.
Avoid speculative metadata fields.
66. Accessibility
When building UI, consider:
- proper labels
- keyboard accessibility
- semantic elements
- focus behavior
- meaningful button/link text
- heading hierarchy
- image alternative text
Do not sacrifice basic accessibility for visual convenience.
67. Responsive Design
Admin and frontend interfaces should work reasonably on supported viewport sizes.
Do not assume desktop-only behavior unless a feature explicitly permits it.
Use the existing responsive Tailwind approach.
68. Browser Compatibility
Use broadly supported web capabilities suitable for modern browsers.
Do not introduce experimental browser APIs without a concrete requirement.
69. Dependency Rules
Do not install a new package automatically.
Before proposing a dependency:
1. Determine whether Laravel already solves the requirement.
2. Determine whether an existing approved package solves it.
3. Explain why the dependency is needed.
4. Explain maintenance implications.
5. Explain security implications where relevant.
6. Request explicit user approval.
Do not install until approved.
70. Dependency Updates
Do not run broad dependency upgrades during normal feature work.
Avoid:
composer update
or unrestricted:
npm update
unless explicitly approved.
Use lock files as the authoritative dependency set.
71. Package Modification
Never edit code under:
vendor/
or:
node_modules/
Use documented extension mechanisms instead.
72. Generated Assets
Do not manually edit generated build assets under:
public/build/
Modify source assets and rebuild them through Vite.
73. Tests as Part of Implementation
Write or update automated tests for meaningful approved behavior.
Tests should verify:
- success paths
- important failure paths
- validation
- authorization
- persistence where relevant
Do not over-test framework internals.
74. Test Behavior, Not Implementation Details
Prefer tests that verify observable application behavior.
Avoid tests tightly coupled to private implementation details unless necessary.
This makes refactoring safer.
75. Test Isolation
Tests must not depend on execution order.
Tests should prepare their own required state.
Use factories and Laravel testing tools where appropriate.
Do not rely on real production data.
76. Focused Testing First
During implementation, run focused relevant tests.
Do not run the full project test suite after every small change.
Run broader regression validation at the approved final-validation stage.
This supports the low-budget development policy.
77. Test Failures
If a test fails:
- identify whether the implementation or test expectation is wrong
- fix the actual cause
- rerun the focused test
Do not weaken tests merely to obtain a passing result.
78. Code Formatting
Follow existing project formatting.
Use Laravel Pint where appropriate.
Do not reformat large unrelated files during a feature.
Formatting changes should not obscure the actual functional diff.
79. Final Diff Quality
Before presenting changes for review:
check the diff for:
- unrelated edits
- accidental whitespace churn
- debugging statements
- generated-file noise
- secrets
- dependency changes
- unintended route changes
- unintended migration changes
- accidental deleted code
Keep the final diff focused.
80. User's Existing Changes
Never overwrite or revert existing user modifications without explicit permission.
Inspect local Git status/diff when needed to establish a baseline.
If user modifications conflict with feature implementation:
STOP and ask how to proceed.
81. Git and GitHub
Follow the Git and GitHub restrictions defined in AGENTS.md.
Agents may inspect the local repository using permitted read-only Git commands.
Agents must not independently access GitHub or remote Git services.
The user handles remote repository operations manually.
82. Debugging
During debugging:
- reproduce the issue where practical
- inspect relevant code/logs
- make the smallest correction
- remove temporary debugging output afterward
Do not enable excessive global debugging or alter unrelated settings.
83. Debug Output
Remove temporary:
- dd(...)
- dump(...)
- console debugging
- temporary log statements
- test routes
before feature finalization unless they are intentional project functionality.
84. Security Fixes
If a serious security issue is discovered while implementing an unrelated feature:
STOP if the issue creates immediate risk.
Report:
- what was found
- severity
- affected area
- whether current work should pause
Do not silently perform a large unrelated security refactor.
85. Technical Debt
Do not automatically fix unrelated technical debt.
If relevant technical debt is discovered:
- note it briefly
- explain whether it affects the current feature
- suggest a future task if appropriate
Keep the approved feature scope intact.
86. Backward Compatibility
When modifying existing behavior:
- inspect current usage
- consider routes
- consider existing data
- consider views
- consider tests
Do not break existing functionality unnecessarily.
If a breaking change is required, identify it during planning and obtain approval.
87. Data Integrity
Prefer database constraints where they provide meaningful protection.
Application validation and database integrity should complement each other.
Do not rely exclusively on frontend validation to protect stored data.
88. Auditability
For security-sensitive administrative actions, consider whether activity logging is required.
Do not implement ad-hoc logging before the Activity Log feature establishes the common approach unless an immediate security requirement demands it.
89. Feature Boundaries
Only implement behavior defined in the active approved feature.
If implementation reveals another needed capability:
do not automatically build it.
Report it as:
- blocker
- dependency
- future enhancement
as appropriate.
90. Requirement Ambiguity
If requirements materially affect:
- security
- data model
- user behavior
- architecture
- destructive behavior
and are unclear:
STOP and ask the user.
Do not invent a business rule.
91. Minor Implementation Decisions
Agents do not need user approval for every trivial implementation choice inside an already approved plan.
Examples:
- variable naming
- ordinary Laravel method choice
- small test-data setup
- correcting a syntax error
- rerunning a focused failed test
Use professional judgment while staying inside the approved scope.
This avoids unnecessary approval turns and AI cost.
92. Approval Required for Significant Changes
Separate approval is required for:
- new dependencies
- architecture changes
- destructive database operations
- major schema changes with data risk
- external-service access
- remote Git/GitHub access
- significant scope expansion
- unusually expensive analysis
- major dependency upgrades
Approval of normal feature implementation does not imply approval of these actions.
93. Documentation Updates
Update documentation only when the approved feature changes information that should remain accurate.
Do not rewrite unchanged documentation after every feature.
Avoid documentation churn.
Feature documentation should record required completion information.
94. Low-Budget Engineering Rule
This project uses a limited AI budget.
To minimize unnecessary processing:
- inspect targeted files
- avoid full-repository scans
- avoid repeated large outputs
- avoid redundant analysis
- use focused tests
- reuse already-established project rules
- do not investigate future features
- do not produce unnecessary alternatives
- do not conduct web research unless required
Do not reduce testing, security, or correctness merely to save tokens.
95. AI Usage Reporting
Follow the usage-reporting requirements in:
AGENTS.md
and:
docs/08_COST_CONTROL.md
Do not perform extra processing solely to determine token or cost usage.
If usage information is unavailable, report it as unavailable.
Never fabricate usage.
96. Engineering Review Checklist
Before presenting implementation for user review, verify applicable items:
- approved scope implemented
- validation present
- authorization present
- persistence safe
- queries reasonable
- no obvious N+1 behavior
- no sensitive information exposed
- focused tests pass
- no debugging code remains
- no unrelated refactoring
- existing user changes preserved
- diff remains focused
97. Final Engineering Principle
Prefer code that another experienced Laravel developer can understand without extensive explanation.
The desired codebase is:
- conventional
- secure
- readable
- focused
- tested
- minimally coupled
- incrementally extensible
Do not optimize for sophistication.
Optimize for reliable, understandable software that satisfies approved requirements.