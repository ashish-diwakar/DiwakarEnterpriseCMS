Create:
C:\Ashish\Projects\DiwakarEnterpriseCMS\dss-enterprise-cms\docs\05_DEFINITION_OF_DONE.md
Everything between COPY START and COPY END should go into the file.
────────────────────────────────────
COPY START — docs/05_DEFINITION_OF_DONE.md
────────────────────────────────────
Diwakar Enterprise CMS — Definition of Done
1. Purpose
This document defines the conditions that must be satisfied before a feature may be considered complete.
Passing tests alone is not sufficient.
A feature is complete only when:
- the approved requirement is implemented
- the implementation has been reviewed
- required testing has passed
- security has been considered
- database impact has been reviewed
- documentation is accurate
- the user has explicitly approved completion
The agent cannot self-approve a feature.
2. Completion Principle
A feature is not Done because:
- code was written
- the page loads
- a happy-path test passes
- the agent believes the implementation is correct
- the implementation looks complete
A feature is Done only when all applicable completion criteria in this document are satisfied and the user explicitly approves completion.
3. Approved Scope
Before a feature can be marked Complete:
- the active feature specification must exist
- the approved implementation scope must be clear
- all required in-scope behavior must be implemented
- no unapproved out-of-scope functionality should be included
- no speculative future functionality should be added
If scope changed during implementation, the user must approve the change.
4. Acceptance Criteria
Every applicable acceptance criterion in the active feature specification must be reviewed.
Each criterion should be:
- satisfied
- tested where practical
- manually verified where appropriate
- or explicitly documented as not currently verifiable
Do not mark acceptance criteria complete merely because unrelated tests pass.
5. User Review Requirement
The implementation must be presented to the user before finalization.
The user must have an opportunity to:
- inspect the changes
- request corrections
- ask questions
- reject implementation decisions
- approve the implementation for final validation
The agent must not skip this review gate.
6. Requested Corrections
If the user requests corrections:
- all approved corrections must be implemented
- relevant focused tests must be rerun
- the corrected implementation must be presented again for review
A feature cannot proceed to final completion while requested changes remain unresolved.
7. Implementation Quality
The implementation should:
- follow the approved architecture
- follow engineering rules
- follow Laravel conventions
- remain understandable
- remain maintainable
- avoid unnecessary complexity
- avoid unrelated refactoring
- preserve existing user changes
8. Code Scope
The final code diff must remain focused on the approved feature.
There should be no unrelated:
- feature additions
- refactors
- formatting churn
- dependency changes
- route changes
- migration changes
- configuration changes
unless they were explicitly required and approved.
9. Controllers
Where applicable:
- controllers remain reasonably thin
- validation is not duplicated unnecessarily
- authorization is enforced appropriately
- meaningful business logic is not buried in controllers
10. Validation
All relevant external input must be validated server-side.
Validation rules should reflect approved requirements.
Applicable validation should cover:
- required values
- formats
- allowed values
- length
- numeric ranges
- foreign keys
- uniqueness
- uploaded files
Client-side validation alone is insufficient.
11. Authorization
All sensitive administrative functionality must use server-side authorization.
Applicable features must verify:
- authorized users can perform the action
- unauthorized users cannot perform the action
- direct URL/request access does not bypass authorization
Navigation visibility alone does not satisfy authorization requirements.
12. Authentication
Administrative functionality requiring authentication must be protected appropriately.
Guests must not be able to access protected functionality.
Authentication behavior must remain consistent with the approved authentication architecture.
13. Mass Assignment
Persistence must not accept arbitrary request data.
Use:
- validated data
- explicitly selected fields
- properly protected model attributes
Sensitive fields must not become writable merely for implementation convenience.
14. Database Integrity
If the feature modifies stored data, review:
- required fields
- nullable fields
- defaults
- unique constraints
- foreign keys
- indexes
- deletion behavior
- relationship integrity
Application validation and database constraints should complement each other where appropriate.
15. Migrations
If the feature introduces schema changes:
- required migration exists
- migration is focused
- forward migration is reasonable
- rollback is reasonable where practical
- existing data impact has been considered
- indexes have been considered
- foreign keys have been considered
- destructive behavior has been approved where applicable
Do not mark database work complete while migration safety remains unclear.
16. Destructive Changes
Any destructive database or data operation must have explicit user approval.
Examples:
- dropping tables
- dropping columns
- destructive type conversions
- bulk deletion
- resetting databases
If such approval was not granted, the operation must not be performed.
17. Query Quality
Where applicable:
- obvious N+1 query issues are avoided
- large datasets are paginated or constrained
- related data is loaded appropriately
- queries are not duplicated unnecessarily
- indexes are considered for important lookups
Do not require speculative optimization without evidence.
18. Blade and UI
For applicable Blade/UI changes:
- page renders successfully
- required content is visible
- form fields are correctly labeled
- validation errors are shown appropriately
- links/actions point to correct named routes
- authorization-related controls are consistent with backend authorization
- no obvious Blade syntax errors remain
19. Accessibility
Where UI is involved, reasonable accessibility checks should include:
- meaningful form labels
- semantic elements
- heading hierarchy
- meaningful action text
- keyboard-accessible controls where applicable
- image alternative text where applicable
Do not postpone all basic accessibility concerns to a future cleanup.
20. Frontend Assets
If CSS, JavaScript, Alpine.js, Tailwind, or Vite-related source changes are involved:
- source files are correct
- production build succeeds
- generated assets are not manually edited
Applicable command:
npm run build
21. Security Review
Every feature must receive a security review proportional to its risk.
Applicable considerations include:
- authentication
- authorization
- input validation
- mass assignment
- CSRF
- XSS
- SQL injection
- file-upload safety
- privilege escalation
- sensitive data exposure
- open redirects
- destructive actions
Only relevant areas need detailed review.
22. Sensitive Information
The final change set must not expose:
- passwords
- API keys
- access tokens
- private keys
- real secrets
- protected credentials
- sensitive production data
Secrets must not appear in:
- source files
- Blade templates
- JavaScript
- tests
- logs
- documentation
- .env.example
23. Error Handling
Where failure paths exist:
- errors should not be silently ignored
- users should receive safe behavior/messages
- sensitive technical details should not be intentionally exposed
- unexpected failures should use appropriate Laravel handling/logging
24. File Uploads
If the feature handles files:
- type validation exists
- size validation exists
- storage destination is safe
- executable content is not accepted improperly
- filenames are handled safely
- authorization is enforced
- tests exist where practical
25. Focused Tests
Before user code review:
- required focused tests must exist or be updated
- focused tests must pass
- relevant failures must be resolved
Do not proceed while known feature-specific failures remain.
26. Regression Testing
After the user approves final validation:
- the applicable regression suite must pass
Typical command:
php artisan test
Normally perform the full suite once during final validation.
If unrelated pre-existing failures exist, clearly distinguish them from current-feature regressions.
27. Negative Tests
Security-sensitive and validation-sensitive features should include relevant negative-path tests.
Examples:
- guest cannot access protected page
- unauthorized role cannot perform action
- invalid input is rejected
- protected field cannot be changed
- invalid upload is rejected
Happy-path testing alone may be insufficient.
28. Bug Fix Regression Test
For meaningful bug fixes, add a regression test where practical.
The test should demonstrate that the previously failing behavior is now handled correctly.
29. Formatting
When PHP source code changes, Laravel Pint validation should pass where applicable.
Preferred command:
./vendor/bin/pint --test
Do not reformat unrelated files unnecessarily.
30. Route Validation
If routes changed:
- route URI is correct
- HTTP method is correct
- route name is correct
- required middleware is present
- admin prefix is correct where applicable
Use:
php artisan route:list
or a targeted equivalent when useful.
31. Build Validation
If frontend assets changed:
npm run build
must complete successfully before the feature is considered ready for final approval.
32. No Debugging Artifacts
Remove temporary development artifacts such as:
- dd(...)
- dump(...)
- temporary console output
- temporary log statements
- test routes
- experimental UI
- debug-only code
unless intentionally required by the approved feature.
33. No Dead Experimental Code
The final implementation should not contain:
- commented-out alternative implementations
- abandoned experimental methods
- unused imports
- unused classes introduced by the feature
- temporary compatibility hacks without explanation
34. TODO Review
No unexplained TODO items should remain in the approved feature.
If a TODO must remain:
- it must be intentional
- its reason must be clear
- it must not represent incomplete required functionality
35. Dependency Review
If dependencies changed:
- change was explicitly approved
- dependency is necessary
- lock file changes are expected
- no unrelated dependency upgrades occurred
- package impact has been reviewed
Do not accept broad dependency churn as a normal side effect.
36. Configuration Review
If configuration changed:
- change is required
- defaults are safe
- secrets are not committed
- .env.example is updated only when necessary
- environment-specific values remain outside source control
37. Existing User Work
The feature must preserve unrelated existing user modifications.
Before final approval:
- agent-created changes should be distinguishable from pre-existing work
- no unrelated user changes should have been reverted
- conflicts should have been resolved with user direction
38. GitHub Restriction
Completion does not require GitHub access.
Agents must not:
- push
- pull
- fetch
- create pull requests
- inspect remote repository status
- use GitHub APIs
- use GitHub connectors
The user handles GitHub manually.
39. Git Commit Restriction
A feature may be Complete without the agent creating a Git commit.
Agents must not automatically:
- commit
- amend
- create branches
- merge
- rebase
- tag
The user handles normal Git/GitHub finalization manually unless a specific local Git action is explicitly requested.
40. Documentation
Update only documentation affected by the feature.
Possible required updates include:
- active feature specification
- acceptance criteria
- completion record
- architecture documentation when genuinely changed
- ADR when an approved architectural decision changed
Do not rewrite unrelated documentation.
41. Feature Completion Record
The active feature document should contain a concise completion record including applicable information:
- implementation summary
- files created
- files modified
- migrations
- tests added
- tests executed
- validation results
- known limitations
- remaining non-blocking items
Do not generate a separate large report unless requested.
42. Architecture Consistency
Verify that the implementation does not unintentionally introduce:
- unnecessary repositories
- unnecessary interfaces
- duplicate architectural layers
- new frameworks
- unapproved packages
- unapproved external services
Any approved architectural exception should be documented.
43. Product Scope Consistency
Verify that implementation remains inside:
- docs/01_PRODUCT_SCOPE.md
- active feature specification
- approved implementation plan
Do not include future roadmap functionality simply because it is related.
44. Performance
Where applicable, review obvious performance concerns.
Examples:
- N+1 database access
- unbounded queries
- repeated expensive operations
- oversized asset handling
- unnecessary external calls
Do not introduce complex performance infrastructure without need.
45. Manual Review
Where automated testing cannot reasonably verify visual or interaction details:
provide the user with concise manual verification instructions.
Example:
Page:
/admin/pages/create
Verify:
- form renders
- validation appears
- save redirects correctly
Do not claim that the user completed manual validation until they confirm it.
46. Known Limitations
Any known limitation should be disclosed before final approval.
Do not hide:
- test gaps
- environment limitations
- incomplete optional behavior
- compatibility concerns
- unresolved non-blocking issues
The user should be able to approve with full awareness of relevant limitations.
47. No Known Blocking Issues
A feature cannot be marked Complete while a known blocker remains.
A blocker includes a problem that prevents:
- required behavior
- required security
- required testing
- required data integrity
Resolve it or obtain an explicit scope/requirement decision from the user.
48. AI Cost Control
Development must comply with:
docs/08_COST_CONTROL.md
Completion should not require unnecessary:
- repository scans
- repeated tests
- repeated builds
- web research
- large reports
- redundant documentation
Required correctness and security checks must still be performed.
49. AI Usage Reporting
The finalization report must include compact AI usage information when available.
If usage is unavailable:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
Do not make additional calls solely to discover usage.
50. User Approval Before Completion
After all applicable technical checks succeed, the agent must present the finalization report.
Then ask:
“Feature FXXX is ready for your final approval. May I mark it Complete?”
STOP.
Do not change the status to Complete until the user explicitly approves.
51. Completion Authorization
Only explicit user approval authorizes changing the feature status to:
Complete
Approval for final validation does not automatically authorize completion.
Passing tests does not automatically authorize completion.
The agent cannot approve itself.
52. Next Feature Restriction
After marking a feature Complete:
identify the next roadmap feature and its objective briefly.
Then ask:
“Would you like me to begin analysis of FXXX?”
STOP.
Do not:
- inspect next-feature files
- analyze next-feature implementation
- prepare its plan
- write its code
until the user gives permission.
53. Minimum Completion Checklist
Before asking for final approval, confirm all applicable items:
- [ ] approved scope implemented
- [ ] acceptance criteria reviewed
- [ ] user code review completed
- [ ] requested corrections completed
- [ ] server-side validation implemented
- [ ] authorization implemented where required
- [ ] database integrity reviewed
- [ ] migrations reviewed
- [ ] focused tests pass
- [ ] regression tests pass
- [ ] negative/security tests pass where applicable
- [ ] Pint passes where applicable
- [ ] frontend build passes where applicable
- [ ] routes validated where applicable
- [ ] security review completed
- [ ] performance concerns reviewed where applicable
- [ ] no debugging artifacts remain
- [ ] no secrets exposed
- [ ] final diff reviewed
- [ ] unrelated user work preserved
- [ ] required documentation updated
- [ ] known limitations disclosed
Only applicable items need to be satisfied.
54. Feature Finalization Report Format
Keep the final report concise.
Recommended structure:
Feature:
FXXX — Feature Name
Status:
Ready for Final Approval
Acceptance Criteria:
X/X satisfied
Validation:
- focused tests: passed
- regression suite: passed
- Pint: passed / not applicable
- frontend build: passed / not applicable
- routes: verified / not applicable
- migrations: verified / not applicable
- security review: passed
Files:
X created
Y modified
Known Issues:
None
or a concise list.
AI Usage:
actual available information
or:
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
Then ask for final approval.
55. Definition of Done Rule
The complete Definition of Done is:
Requirements satisfied
AND
Implementation reviewed
AND
Required corrections resolved
AND
Applicable tests passed
AND
Applicable quality checks passed
AND
Security reviewed
AND
Database impact reviewed
AND
Final diff reviewed
AND
Required documentation updated
AND
User explicitly approves completion
Only then is the feature:
Complete
56. Final Principle
“Done” is a user-approved state, not an agent-generated conclusion.
The agent's responsibility is to demonstrate that the feature is ready.
The user's responsibility is to decide whether the feature is complete.