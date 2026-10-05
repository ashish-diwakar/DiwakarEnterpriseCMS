Diwakar Enterprise CMS — Feature Specification Guide
1. Purpose
This document defines the standard format and rules for feature specifications under:
docs/features/
Each approved feature must have its own specification before implementation begins.
The feature specification is the immediate source of truth for:
- scope
- expected behavior
- acceptance criteria
- security requirements
- database impact
- test requirements
- completion status
The roadmap defines direction.
The feature specification defines what must actually be implemented.
2. Feature File Naming
Use this format:
FXXX_FEATURE_NAME.md
Examples:
F001_AUTHENTICATION.md
F002_RBAC.md
F003_ADMIN_SHELL.md
Use the feature ID from:
docs/07_FEATURE_ROADMAP.md
Do not invent a new feature ID without user approval.
3. One Feature per File
Each feature specification should describe one cohesive feature.
Do not combine unrelated functionality merely to reduce the number of files.
If a feature becomes too large:
- explain why
- propose a split
- request user approval
Do not split or merge features automatically.
4. Feature Status
Each feature document must contain a Status field.
Allowed statuses:
Planned
Analysis
Approved for Implementation
In Progress
Ready for Review
Changes Requested
Approved for Final Validation
Ready for Final Approval
Complete
The agent must not mark a feature Complete without explicit user approval.
5. Required Feature Structure
Every feature specification should use the following sections where applicable.
FXXX — Feature Name
Status:
Planned
1. Objective
Describe the business or product outcome.
Keep this concise.
Explain:
- what the feature provides
- why it exists
- who benefits
Do not describe implementation details here unless necessary.
2. Background
Use this section only when existing implementation context is important.
Examples:
- Laravel Breeze is already installed
- an existing admin route exists
- a previous feature introduced required behavior
Do not repeat the entire project history.
3. In Scope
List exactly what the feature must implement.
Use clear, testable statements.
Example:
- login page uses CMS authentication layout
- authenticated user can log out
- admin dashboard requires authentication
Do not include optional future ideas unless they are approved requirements.
4. Out of Scope
List functionality that is intentionally excluded.
This is important for preventing scope creep.
Example:
- social login
- two-factor authentication
- RBAC
- final visual design
- user management
If an item belongs to a later roadmap feature, mention the feature ID where useful.
5. Dependencies
List required completed functionality or approved dependencies.
Examples:
- Laravel Breeze installed
- F001 Authentication complete
- F006 Media Library complete
Do not list speculative dependencies.
6. User Roles / Actors
Identify the actors relevant to the feature.
Examples:
- Guest
- Authenticated User
- Super Admin
- Admin
- Editor
Only include roles relevant to the feature.
If RBAC is not yet implemented, do not invent permission behavior.
7. User / System Behavior
Describe expected behavior from the user's or system's perspective.
Use concrete statements.
Example:
When an unauthenticated user attempts to access an admin-protected page, the application redirects to the login page.
Avoid implementation-specific wording unless implementation behavior itself is required.
8. Business Rules
Document explicit business rules.
Examples:
- public registration is temporarily allowed during development
- only authorized users may publish pages
- slug must be unique
Do not invent business rules.
If a rule is unclear:
STOP and ask the user.
9. Acceptance Criteria
Acceptance criteria must be specific enough to verify.
Use checklist format:
- [ ] Criterion 1
- [ ] Criterion 2
- [ ] Criterion 3
Good example:
- [ ] Guest cannot access /admin/dashboard.
Weak example:
- [ ] Security works.
Acceptance criteria should describe observable outcomes.
10. Security Requirements
Document feature-specific security requirements.
Potential areas include:
- authentication
- authorization
- validation
- mass assignment
- CSRF
- XSS
- file upload security
- privilege escalation
- sensitive information
- destructive operations
Do not duplicate the entire global security policy.
Reference:
docs/06_SECURITY_RULES.md
and record only feature-specific requirements.
11. Validation Requirements
Define meaningful input-validation requirements.
Examples:
- name required
- email must be valid
- slug unique
- upload must be an approved image type
- maximum file size
Do not define arbitrary validation constraints without product justification.
12. Database Impact
Describe expected database changes.
Use one of:
None
or describe:
- new table
- new columns
- changed relationship
- new index
- foreign key
- seed data
If a migration is potentially destructive, highlight it clearly.
Do not implement destructive changes without approval.
13. Route Impact
Describe expected route changes if relevant.
Examples:
Admin routes:
GET /admin/pages
POST /admin/pages
Route names:
admin.pages.index
admin.pages.store
Do not list routes that are not required by the feature.
14. UI / View Impact
Describe relevant presentation changes.
Examples:
- new admin view
- authentication guest layout
- sidebar item
- form
- list page
- validation errors
Do not define final visual styling unless the feature includes that requirement.
15. Package / Dependency Impact
Use:
None
unless the feature requires a dependency.
If a new dependency appears necessary, document:
- package name
- purpose
- why Laravel/existing dependencies are insufficient
- maintenance implications
- security implications where relevant
Installation requires separate user approval.
16. Configuration Impact
Describe any required configuration changes.
Examples:
- new config value
- new environment variable
- middleware registration
- route registration
Do not expose secrets.
If no change:
None
17. Testing Requirements
Define only tests relevant to the feature.
Potential tests include:
- happy path
- validation failure
- authentication
- authorization
- persistence
- redirects
- negative security paths
- regression case
Follow:
docs/04_TESTING_STRATEGY.md
Do not require broad unrelated tests in the feature specification.
18. Manual Verification
Use this section only when some behavior is best reviewed manually.
Example:
Page:
/admin/dashboard
Verify:
- page renders
- correct layout displays
- responsive navigation works
Do not claim manual verification occurred until the user confirms it where applicable.
19. Risks / Notes
Record only meaningful feature-specific risks.
Examples:
- public registration is temporary
- migration affects existing data
- file upload requires careful validation
Avoid generic engineering advice.
20. Implementation Plan
This section must not be pre-filled with speculative implementation.
During the Analysis stage, the agent may add the approved concise plan after inspecting relevant code.
Normal plan size:
3–7 meaningful steps.
The implementation plan requires user approval before coding.
21. Implementation Record
Complete this section after implementation.
Include concise information such as:
Files Created
List only feature-created files.
Files Modified
List only feature-modified files.
Migrations
List migrations created or modified.
Tests Added / Updated
List relevant tests.
Do not paste full code or large diffs here.
22. Validation Record
Complete during final validation.
Record applicable results:
Focused tests:
Result
Regression suite:
Result
Pint:
Result / Not applicable
Frontend build:
Result / Not applicable
Routes:
Verified / Not applicable
Migration:
Verified / Not applicable
Security review:
Result
Keep the record concise.
23. Known Limitations
Record any known non-blocking limitation.
If none:
None
A blocking limitation prevents completion.
Do not hide known issues.
24. Completion Record
Complete only after final validation.
Recommended format:
Implementation:
Complete
Acceptance Criteria:
X/X satisfied
Final Validation:
Passed
User Review:
Approved
Final User Approval:
Pending
Do not change Status to Complete until final user approval is explicitly received.
25. Final Status Update
Only after the user approves feature completion:
Set:
Status:
Complete
Then update:
Final User Approval:
Approved
Do not begin the next feature automatically.
Feature Specification Template
Use the following as the base for each new feature.
────────────────────────────────────
FXXX — Feature Name
Status:
Planned
1. Objective
Describe the intended outcome.
2. Background
Relevant existing context only.
3. In Scope
- requirement
- requirement
4. Out of Scope
- excluded functionality
- excluded functionality
5. Dependencies
- dependency
or:
None
6. User Roles / Actors
- actor
7. User / System Behavior
Describe expected behavior.
8. Business Rules
- rule
9. Acceptance Criteria
- [ ] criterion
- [ ] criterion
10. Security Requirements
Describe feature-specific security requirements.
11. Validation Requirements
Describe required validation.
12. Database Impact
None
or describe required schema changes.
13. Route Impact
None
or list relevant routes.
14. UI / View Impact
Describe relevant UI changes.
15. Package / Dependency Impact
None
or describe the proposed dependency.
16. Configuration Impact
None
or describe configuration changes.
17. Testing Requirements
- test
- test
18. Manual Verification
None
or describe verification steps.
19. Risks / Notes
None
or describe feature-specific risks.
20. Implementation Plan
To be completed during Analysis.
Do not implement until user approval.
21. Implementation Record
Files Created
Pending.
Files Modified
Pending.
Migrations
Pending.
Tests Added / Updated
Pending.
22. Validation Record
Focused Tests:
Pending.
Regression Suite:
Pending.
Pint:
Pending.
Frontend Build:
Pending.
Routes:
Pending.
Migration:
Pending.
Security Review:
Pending.
23. Known Limitations
Pending.
24. Completion Record
Implementation:
Pending.
Acceptance Criteria:
Pending.
Final Validation:
Pending.
User Review:
Pending.
Final User Approval:
Pending.
────────────────────────────────────
26. Template Usage Rule
Do not create specifications for every future roadmap feature upfront.
Create the specification only when:
- the feature is about to enter the controlled workflow
- or the user explicitly requests it
This avoids premature design work and unnecessary AI usage.
27. Keep Feature Files Focused
Do not use feature files as:
- conversation transcripts
- large implementation tutorials
- complete code dumps
- generic architecture documentation
Keep durable feature decisions and results only.
28. Feature Document Updates
Update the feature file only at meaningful workflow points.
Examples:
- analysis/plan completed
- implementation completed
- final validation completed
- final approval received
Do not rewrite the complete feature document after every minor code edit.
29. User Approval Gates
The feature specification does not override the approval process.
The mandatory gates remain:
Plan
→ USER APPROVAL
Implementation
→ USER REVIEW
Final Validation
→ USER FINAL APPROVAL
Next Feature
→ USER PERMISSION
Follow:
docs/03_DEVELOPMENT_WORKFLOW.md
30. Final Principle
A feature specification should answer:
What are we building?
What are we not building?
How will we know it works?
What security/data/testing obligations apply?
What was actually changed?
Has the user approved completion?
It should not authorize autonomous development beyond the user's current approval.