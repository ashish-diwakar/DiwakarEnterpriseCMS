Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\04_TESTING_STRATEGY.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/04_TESTING_STRATEGY.md
────────────────────────────────────
Diwakar Enterprise CMS — Testing Strategy
1. Purpose
This document defines the testing strategy for Diwakar Enterprise CMS.
The goals are:
- verify approved behavior
- prevent regressions
- validate security-sensitive functionality
- protect database integrity
- support safe refactoring
- keep testing proportionate to feature risk
- avoid unnecessary test execution and AI-processing cost
Testing is part of implementation, not an optional final activity.
2. Testing Principles
Tests should verify meaningful application behavior.
Prefer tests that answer questions such as:
- can the user perform the allowed action?
- is unauthorized access blocked?
- is invalid input rejected?
- is valid data persisted correctly?
- is the expected response returned?
- does the feature behave correctly after a change?
Avoid testing Laravel internals that the framework already guarantees.
3. Primary Test Framework
Primary backend testing framework:
PHPUnit through Laravel's testing facilities.
Primary command:
php artisan test
Use Laravel's built-in test helpers where appropriate.
Do not introduce another test framework without explicit approval.
4. Test Types
The project primarily uses:
- Feature Tests
- Unit Tests
Other test categories may be introduced later only when they provide clear value.
5. Feature Tests
Feature Tests are the preferred test type for most CMS behavior.
Use them for:
- HTTP routes
- controllers
- authentication
- authorization
- validation
- database persistence
- redirects
- form submissions
- middleware behavior
- page rendering
- admin workflows
Feature tests should verify observable application behavior.
6. Unit Tests
Use Unit Tests for isolated business logic that can be tested without Laravel's full request/database stack.
Examples may include:
- calculation logic
- value transformations
- small domain rules
- pure utility behavior
- isolated service behavior
Do not create Unit Tests for simple framework calls merely to increase test count.
7. Test Location
Follow Laravel conventions.
Feature tests:
tests/Feature/
Unit tests:
tests/Unit/
Feature-specific tests may be grouped into subdirectories.
Examples:
tests/Feature/Auth/
tests/Feature/Admin/Pages/
tests/Feature/Admin/Users/
Use organization that keeps tests easy to locate.
8. Naming Tests
Test names should describe behavior clearly.
Examples:
test_authenticated_user_can_view_admin_dashboard
test_guest_cannot_access_admin_dashboard
test_page_title_is_required
test_editor_cannot_delete_super_admin
Avoid vague names such as:
test_page
test_case_1
test_success
9. Arrange, Act, Assert
Tests should generally follow a clear structure:
Arrange
- prepare required state
Act
- perform the request or operation
Assert
- verify the expected result
Keep tests readable.
Comments are optional when the structure is already clear.
10. Test Isolation
Tests must be independent.
Do not rely on:
- test execution order
- data created by another test
- manual database state
- a developer already being logged in
- remote services
Each test should create or prepare the state it requires.
11. Test Database Safety
Automated tests must use a test environment/database.
Never intentionally run destructive test operations against:
- production database
- staging database
- important development data
Testing configuration should remain isolated from normal development data.
12. Laravel Database Testing
Use Laravel database testing facilities where appropriate.
Potential tools include:
RefreshDatabase
factories
seeders specifically intended for tests
database assertions
Do not manually reset the normal development database merely to run automated tests.
13. RefreshDatabase
Use RefreshDatabase where database-backed feature tests need clean state.
Before adopting it for a test suite, verify that the configured testing database is safe.
Do not assume the normal development database can be safely refreshed.
14. Database Configuration
Testing database configuration should be defined through Laravel testing environment mechanisms.
Possible options may include:
- dedicated test MariaDB database
- SQLite where fully compatible with tested behavior
The specific choice should be made based on actual project requirements.
Do not silently switch database engines if behavior depends on MariaDB-specific features.
15. Database Fidelity
Prefer a test database that provides sufficient compatibility with production behavior.
If SQLite is used for convenience, be alert to differences involving:
- foreign keys
- indexes
- JSON
- data types
- SQL functions
- constraints
- migrations
If fidelity matters for a feature, use MariaDB-compatible testing.
16. Factories
Use model factories to prepare test data where they improve readability.
Factories should generate valid default models.
Tests may override fields to exercise specific cases.
Avoid large setup blocks duplicated across many tests.
17. Seeders in Tests
Use seeders only when the feature genuinely depends on baseline seeded data.
Do not automatically run the entire application seeder for every test.
Prefer targeted setup.
18. Authentication Testing
Authentication-related features should test applicable behavior such as:
- registration
- successful login
- failed login
- logout
- password reset request
- password reset
- password confirmation
- email verification where applicable
- guest access restrictions
- authenticated access
Test only behavior that is part of the approved feature.
19. Authorization Testing
Every protected administrative feature should include both positive and negative authorization tests where meaningful.
Examples:
Authorized:
- Admin can edit a page.
Unauthorized:
- Viewer cannot edit a page.
Also consider:
- guest receives authentication redirect
- authenticated but unauthorized user receives appropriate denial
- protected action cannot be performed by direct URL/request access
Hiding navigation is not sufficient.
20. Validation Testing
Validation tests should cover important input rules.
Consider:
- required fields
- maximum lengths
- invalid formats
- invalid enum/status values
- invalid foreign keys
- uniqueness
- file types
- file size
- numeric limits
Do not generate a separate test for every trivial validation rule if grouped behavior remains clear and maintainable.
21. Valid Input Testing
Do not test only invalid requests.
Verify that valid input succeeds and produces the expected result.
A validation suite is incomplete if it proves rejection but never proves successful submission.
22. Persistence Testing
When a feature changes database state, verify important persisted values.
Possible assertions include:
- record exists
- expected fields are stored
- related records are created
- unauthorized fields are not changed
- deleted/soft-deleted state is correct
Use Laravel database assertions where suitable.
23. Relationship Testing
Test relationships when behavior depends on them.
Do not write tests solely to prove that belongsTo() returns a relationship object.
Instead, test meaningful behavior involving related records.
24. Route Testing
When routes are important to a feature, test:
- route availability
- authentication requirements
- authorization requirements
- expected responses
- redirects
Named route generation may be used in tests instead of hard-coded URLs.
25. Response Testing
Use Laravel response assertions where appropriate.
Examples may include:
- assertOk()
- assertRedirect()
- assertForbidden()
- assertNotFound()
- assertViewIs()
- assertViewHas()
- assertSessionHasErrors()
Choose assertions that describe expected behavior clearly.
26. HTTP Status Expectations
Use appropriate HTTP behavior.
Examples:
- successful page: 200
- redirect after form submission: redirect response
- unauthenticated access: authentication redirect
- unauthorized access: 403 where appropriate
- missing resource: 404
Do not alter application behavior merely to make a test easier.
27. Security Testing
Security-sensitive features should include relevant negative tests.
Possible areas include:
- unauthorized access
- mass assignment
- invalid role assignment
- CSRF-sensitive workflows where meaningful
- XSS-sensitive rendering
- file upload restrictions
- privilege escalation
- protected resource manipulation
Testing should be proportionate to actual risk.
28. Mass Assignment Testing
For sensitive models, consider tests that confirm protected fields cannot be altered through normal user-submitted input.
Do not expose privilege-related properties just to simplify tests.
29. File Upload Testing
When file uploads are implemented, test applicable cases:
- accepted file type
- rejected file type
- maximum size
- storage
- database metadata
- authorization
- deletion behavior
Use Laravel fake storage where suitable.
Do not use real production storage during tests.
30. Email Testing
When features send emails, use Laravel's testing facilities such as mail fakes where appropriate.
Verify:
- expected mail is triggered
- recipient is correct
- unauthorized flows do not send mail
Do not send real external email during automated tests.
31. Notifications Testing
Use Laravel notification fakes for approved notification behavior.
Do not contact real external notification providers during normal automated tests.
32. External Integrations
External integrations should be mocked, faked, or isolated in automated tests where practical.
Tests should not depend on:
- live APIs
- GitHub
- payment providers
- cloud storage
- production mail providers
- external AI services
unless a separately approved integration-testing process requires it.
33. GitHub Is Not Part of Testing
Automated tests must not require connection to GitHub.
Do not:
- fetch remote repository data
- invoke GitHub APIs
- run GitHub Actions
- inspect remote branches
The user handles GitHub manually.
34. Frontend Blade Testing
For server-rendered Blade functionality, Feature Tests may verify:
- page renders
- expected text exists
- expected component/view is used
- authorization affects page access
Avoid fragile tests that depend excessively on exact HTML formatting.
35. JavaScript Testing
A dedicated JavaScript test framework is not currently required.
Do not introduce one automatically.
If future frontend behavior becomes complex enough to justify dedicated JavaScript tests, propose it separately.
Simple Alpine interactions should not automatically trigger a large testing-tool dependency.
36. Browser / End-to-End Testing
A browser automation framework is not currently required.
Do not introduce:
- Laravel Dusk
- Playwright
- Cypress
- Selenium
unless a future feature demonstrates clear value and the user approves it.
Current functionality can generally be validated through Laravel Feature Tests plus focused manual review.
37. Manual Verification
Some UI behavior may require manual verification during development.
When manual verification is applicable, clearly identify:
- URL/page
- action to perform
- expected result
Do not claim manual verification was performed if the agent did not actually perform it.
The user may perform visual/browser review.
38. Focused Tests During Implementation
During implementation, run only relevant tests first.
Examples:
php artisan test --filter=AuthenticationTest
php artisan test tests/Feature/Auth
php artisan test tests/Feature/Admin/PageTest.php
Choose the narrowest practical command.
This is the default low-cost testing approach.
39. Rerunning Focused Tests
When a focused test fails:
- fix the issue
- rerun the affected focused tests
Do not immediately rerun the entire test suite unless the failure indicates broad impact.
40. Full Regression Testing
Run the complete applicable test suite during the approved final-validation phase.
Typical command:
php artisan test
Normally run it once after the feature implementation is approved for final validation.
Run it again only if:
- fixes were required after a failure
- late changes could affect broader behavior
- the user requests it
41. Regression Scope
As the application grows, a feature may also run a relevant module-level suite before the complete suite.
Example:
- focused test
- module tests
- full suite during final validation
Do not create unnecessary testing layers for small features.
42. Laravel Pint
When PHP source code changes, final validation should include Laravel Pint where applicable.
Preferred command:
./vendor/bin/pint --test
On Windows, use the correct local invocation supported by the environment.
If formatting fails:
- correct relevant files
- avoid formatting unrelated files unnecessarily
- rerun the check
43. Frontend Build
When a feature affects frontend assets or UI source that relies on the Vite pipeline, final validation should include:
npm run build
Examples include changes to:
- CSS
- JavaScript
- Alpine behavior
- Vite configuration
- Tailwind configuration
Do not repeatedly run production builds for server-only PHP changes.
44. Route Validation
When routes change, validate the relevant routes.
Possible command:
php artisan route:list
Prefer filtered output where possible.
Verify:
- URI
- HTTP method
- route name
- middleware
- admin prefix
Do not dump large route lists into reports unnecessarily.
45. Migration Testing
When a feature adds migrations:
At minimum review:
- migration runs forward
- rollback logic is reasonable
- schema matches requirements
- indexes/constraints are appropriate
Do not run destructive reset commands against the development database.
46. Migration Rollback
Where practical, migrations should support rollback.
If rollback is intentionally unsafe or impossible, this must be identified during planning/review.
Do not pretend rollback exists when it has not been validated.
47. Testing Destructive Operations
Features involving deletion or destructive behavior should test:
- authorization
- confirmation behavior where part of the requirement
- affected records
- related data
- soft-delete behavior if applicable
Tests must use safe test data.
48. Test Coverage Philosophy
The project does not currently require a specific numerical code-coverage percentage.
Do not generate artificial tests merely to raise coverage metrics.
Focus on high-value behavior and risk.
If formal coverage targets become necessary later, they require an approved project decision.
49. High-Risk Features
High-risk functionality should receive stronger testing.
Examples include:
- authentication
- authorization
- user administration
- role/permission changes
- destructive actions
- file uploads
- security settings
- payment/integration features if introduced later
Test depth should correspond to risk.
50. Low-Risk Features
Simple display-only changes may require fewer automated tests where existing coverage already verifies rendering.
Do not create excessive test infrastructure for trivial markup changes.
Still verify that the change does not break relevant rendering/build behavior.
51. Bug Regression Tests
When fixing a meaningful bug, add a regression test where practical.
The test should demonstrate:
- the old failure condition
- the corrected expected behavior
This prevents the bug from returning.
52. Test Data Quality
Use realistic enough test data to exercise behavior.
Avoid:
- real credentials
- personal production data
- sensitive information
- live customer information
Synthetic test data should be sufficient.
53. Time-Dependent Testing
For behavior involving dates or times, use Laravel/Carbon testing facilities to control time where appropriate.
Avoid tests dependent on the actual current clock when deterministic time can be used.
54. Randomness
Tests should be deterministic.
Avoid uncontrolled randomness that causes intermittent failures.
If random values are generated by factories, assertions should not depend on unpredictable values unless captured during setup.
55. Flaky Tests
Do not accept flaky tests as normal.
If a test intermittently fails:
- identify the underlying nondeterminism
- fix it where practical
- do not simply rerun until it passes and ignore the issue
Report unresolved flakiness.
56. Performance of Test Suite
Keep tests reasonably efficient.
Avoid:
- unnecessary external calls
- repeated expensive setup
- loading huge datasets
- excessive seeders
Do not compromise meaningful test coverage solely for speed.
57. Failed Test Reporting
When reporting a failure, summarize:
- failing test
- relevant failure message
- likely cause
- correction made or blocker
Do not paste enormous logs unless the user requests them or the detail is necessary.
58. Test Output in Agent Reports
Reports should be concise.
Preferred style:
Focused tests:
AuthenticationTest — 8 passed
Final suite:
php artisan test — 42 passed
Do not paste all successful assertion output.
Include detailed output only when troubleshooting a failure.
59. Test Modification Rules
Do not modify an existing test merely because new code fails it.
First determine whether:
- the test represents correct expected behavior
- the requirement changed
- the implementation is wrong
Only change the test when the expected behavior legitimately changed.
60. No Test Deletion for Convenience
Never delete, skip, or disable a valid failing test simply to make the suite green.
If an obsolete test must be removed because requirements changed:
explain why during review.
61. Skipped Tests
Avoid skipped tests without a documented reason.
If a test must remain skipped:
- explain the blocker
- identify what would allow it to run
- do not report the entire suite as fully validated without mentioning the skip
62. Existing Failing Tests
If unrelated tests already fail before feature implementation:
- identify them during baseline/final validation
- distinguish them from agent-created regressions
- do not fix unrelated failures automatically unless requested
If they prevent safe validation, report the blocker.
63. Baseline Awareness
When practical, understand whether relevant tests passed before modifying the feature.
Do not spend excessive tokens running a complete baseline suite for every small task.
For risky features, a targeted baseline may be appropriate.
64. User Review Before Final Tests
Focused implementation tests occur before user code review.
Full regression/final quality testing occurs only after the user approves proceeding to final validation.
This prevents spending time and resources fully validating code the user may request to change.
65. Acceptance Criteria Mapping
Each feature's final testing should map back to its acceptance criteria.
Before finalization, verify that each applicable criterion has:
- automated validation
- manual validation
- or a documented reason why it cannot be directly tested
Do not mark an acceptance criterion complete merely because unrelated tests pass.
66. Security Acceptance Criteria
Security-sensitive acceptance criteria must include negative-path validation.
Example:
If the requirement says:
“Only Admin can delete pages”
testing should verify both:
- Admin can delete
- unauthorized role cannot delete
67. Test Documentation
Do not create separate large testing reports for every feature.
The active feature specification's completion record should contain concise test information.
This prevents unnecessary documentation and token usage.
68. Testing Dependencies
Do not install additional testing libraries automatically.
If existing PHPUnit/Laravel facilities are insufficient:
- explain the gap
- propose the dependency
- explain value/cost
- request approval
Do not install until approved.
69. Test Environment Secrets
Testing must not require committed secrets.
Use safe testing configuration.
Do not place real credentials in:
- test files
- .env.example
- factories
- fixtures
- documentation
70. Production Systems
Automated development tests must not modify production systems.
Do not test by writing to:
- production database
- live email accounts
- real external storage
- live payment services
- production APIs
unless a separately approved controlled integration test explicitly requires it.
71. Test Review Checklist
Before presenting implementation for user review, verify:
- relevant focused tests exist
- focused tests pass
- authorization negative paths are covered where applicable
- validation is covered where applicable
- persistence is covered where applicable
- no real external systems were contacted
- no development data was destructively reset
72. Final Validation Checklist
During approved final validation, perform applicable checks:
- focused tests still pass
- full test suite passes
- Laravel Pint passes
- frontend build passes where required
- relevant routes are correct
- migrations are reviewed
- security behavior is tested
- no new regression is identified
Only run applicable checks.
73. Testing and Cost Control
Testing must follow LOW-COST MODE.
Use this sequence:
1. smallest relevant test
2. fix if required
3. user review
4. one final regression cycle after approval
Avoid:
- running the complete test suite after every edit
- repeated builds
- unnecessary browser automation
- external integration tests without need
- huge test-output dumps
Do not skip required security or regression testing merely to save cost.
74. AI Processing During Testing
Do not ask the model to analyze thousands of lines of successful test output.
When tests pass, retain only concise result information.
When tests fail, inspect only the relevant failure section first.
Expand log analysis only if needed.
75. Test Completion Rule
A feature is not considered tested merely because one happy-path test passes.
Testing should be proportionate to the feature and cover the important approved behavior.
A feature is ready for final approval only when required tests and applicable quality checks succeed, or any exceptions are clearly disclosed to the user.
76. Final Testing Principle
Testing should provide confidence, not test-count inflation.
Prefer:
few meaningful tests
over:
many low-value tests.
Test:
behavior
security
validation
data integrity
regressions
in proportion to the risk of the approved feature.