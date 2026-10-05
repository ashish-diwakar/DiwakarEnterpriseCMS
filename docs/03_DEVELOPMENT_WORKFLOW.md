Diwakar Enterprise CMS — Development Workflow
1. Purpose
This document defines the mandatory development workflow for every feature, bug fix, enhancement, and significant technical change in Diwakar Enterprise CMS.
The goals are:
- one feature at a time
- explicit user control
- predictable implementation
- review before finalization
- testing before completion
- minimal unnecessary AI usage
- no automatic progression to future work
The user remains the final approval authority.
2. Core Workflow
Every feature follows this lifecycle:
Feature Specification
→ Analysis
→ Implementation Plan
→ USER APPROVAL
→ Implementation
→ Focused Testing
→ USER CODE REVIEW
→ Corrections if required
→ USER APPROVAL FOR FINAL VALIDATION
→ Final Validation
→ Finalization Report
→ USER FINAL APPROVAL
→ Feature Complete
→ PERMISSION REQUIRED FOR NEXT FEATURE
Never automatically skip or combine approval stages.
3. One Feature at a Time
Only one approved feature should be actively implemented at a time unless the user explicitly authorizes otherwise.
Do not:
- begin the next roadmap item early
- partially implement future functionality
- bundle unrelated improvements
- prepare future modules unnecessarily
- refactor unrelated areas
If another requirement is discovered, record it briefly and continue only with the approved feature unless it is a blocker.
4. Feature Specification Required
Before implementation begins, the feature should have a specification under:
docs/features/
Example:
docs/features/F001_AUTHENTICATION.md
A feature specification should define as applicable:
- feature ID
- title
- status
- objective
- in scope
- out of scope
- dependencies
- user/system behavior
- acceptance criteria
- security requirements
- database impact
- test requirements
If the specification is materially incomplete, ask the user before implementation.
Do not invent significant business requirements.
5. Feature Statuses
Recommended feature statuses are:
Planned
Analysis
Approved for Implementation
In Progress
Ready for Review
Changes Requested
Approved for Final Validation
Ready for Final Approval
Complete
Agents must not mark a feature Complete without explicit user approval.
6. Phase 1 — Initial Analysis
Before modifying application code:
1. Read AGENTS.md.
2. Read the active feature specification.
3. Read only additional project documents relevant to the task.
4. Inspect only the relevant existing code.
5. Check the local working tree when useful.
6. Identify dependencies.
7. Identify security implications.
8. Identify database implications.
9. Identify likely affected tests.
10. Identify potential regression risks.
The goal is to understand enough to make a safe plan without performing unnecessary repository-wide analysis.
7. Local Working Tree Baseline
Before editing code, inspect the local working tree where useful using permitted local read-only Git commands.
Examples:
git status
git diff
Identify:
- existing user modifications
- untracked files
- files already changed before agent work
Existing user changes are protected.
Do not:
- reset them
- revert them
- overwrite them
- claim them as agent-created work
If they materially conflict with the feature:
STOP and ask the user how to proceed.
Do not connect to GitHub or any remote repository.
8. Analysis Scope
Analysis should be targeted.
Inspect only files likely to affect the current feature.
Examples may include:
- relevant route files
- relevant controllers
- relevant Form Requests
- relevant models
- relevant migrations
- relevant Blade views/components
- relevant middleware
- relevant policies
- relevant tests
- directly relevant configuration
Do not scan the entire repository merely to produce a plan.
9. Phase 2 — Implementation Plan
After analysis, prepare a concise implementation plan.
A normal plan should usually contain approximately 3–7 meaningful steps.
The plan should state:
- proposed approach
- files expected to change
- files expected to be created
- database impact
- dependency/package impact
- security considerations
- test strategy
- known risks
- complexity level
Complexity should be classified as:
Small
Medium
or:
Large
10. Dependency Check During Planning
If implementation appears to require a new package:
Do not install it.
During planning:
- identify the package
- explain why it is needed
- determine whether Laravel or an existing dependency already provides the capability
- explain maintenance impact
- explain security implications where relevant
Then request separate approval.
Package approval is not implied by normal feature approval.
11. Architecture Check During Planning
If implementation requires a meaningful architecture change:
Do not perform it during ordinary feature implementation.
Explain:
- existing limitation
- proposed change
- alternatives
- benefits
- risks
- maintenance impact
- migration impact
If appropriate, propose an ADR.
Request explicit user approval.
12. Analysis Completion Report
At the end of planning, provide a concise report containing:
- feature being analyzed
- summary of current implementation
- proposed plan
- expected changed files
- expected new files
- database impact
- package impact
- security considerations
- expected tests
- complexity
- blockers or assumptions
- AI usage information if available
Then ask:
“May I proceed with implementation?”
STOP.
13. Approval Gate 1 — Implementation Permission
Do not modify application code until the user explicitly approves implementation.
Examples of valid approval include:
- “Proceed”
- “Approved”
- “Go ahead”
- “Implement it”
Approval applies only to the presented implementation scope.
It does not authorize:
- package installation unless separately approved
- architecture changes unless separately approved
- destructive database operations
- GitHub access
- remote Git operations
- implementation of the next feature
14. Phase 3 — Implementation
After approval:
Implement only the approved scope.
During implementation:
- follow project architecture
- follow engineering rules
- preserve existing user changes
- keep changes focused
- add/update necessary tests
- avoid unrelated refactoring
- avoid speculative functionality
If implementation reveals a materially different requirement than what was approved:
STOP and ask before continuing.
15. Minor Implementation Decisions
The agent may make ordinary low-risk decisions inside the approved implementation without asking permission for every small action.
Examples include:
- naming a local variable
- choosing a normal Laravel method
- creating required test data
- correcting syntax errors
- adjusting imports
- rerunning a focused test
Do not create unnecessary approval turns for routine implementation work.
This reduces time and AI cost.
16. Unexpected Scope Expansion
If implementation reveals another feature would be useful:
Do not build it.
Classify it as:
- blocker
- dependency
- technical debt
- future enhancement
Only blockers should interrupt current implementation.
Non-blocking items should be reported briefly.
17. Database Changes During Implementation
If the approved feature requires migrations:
- create focused migrations
- consider existing data
- consider indexes
- consider foreign keys
- consider nullability
- consider rollback
Do not run destructive commands.
Do not use:
php artisan migrate:fresh
unless explicitly approved.
Potentially destructive schema changes require separate permission.
18. Phase 4 — Focused Testing
After implementation, run tests directly related to the feature first.
Examples:
php artisan test --filter=AuthenticationTest
or:
php artisan test tests/Feature/Auth
depending on the existing test structure.
Use the narrowest reasonable test command.
Do not immediately run the entire test suite after each edit.
19. Focused Test Failures
If focused tests fail:
1. Identify the cause.
2. Determine whether the implementation or test expectation is incorrect.
3. Make the smallest required correction.
4. Rerun the affected focused tests.
Do not weaken tests simply to obtain a passing result.
Do not remove failing tests without justification and approval where appropriate.
20. Implementation-Time Quality Checks
During implementation, run only lightweight checks required to safely continue.
Examples:
- PHP syntax validation where needed
- focused tests
- directly relevant route inspection
- directly relevant migration check
Do not repeatedly run expensive full-project validation.
21. Phase 5 — Code Review Preparation
Once implementation and focused tests are complete:
Review the agent-created changes before presenting them to the user.
Check for:
- unintended changes
- debug code
- unused imports
- temporary routes
- accidental generated files
- secrets
- unnecessary refactoring
- unrelated formatting
- unexpected dependency changes
Do not perform final regression validation yet unless explicitly required.
22. Implementation Review Report
Present a concise review report containing:
- feature ID/name
- implementation summary
- files created
- files modified
- migrations added/changed
- tests added/changed
- focused tests executed
- focused test results
- assumptions
- known limitations
- potential concerns
- pre-existing user changes preserved
- AI usage information if available
Then ask:
“Please review the changes. May I proceed with final validation?”
STOP.
23. Approval Gate 2 — User Code Review
Wait for the user to review the implementation.
Do not assume that passing focused tests means the implementation is accepted.
The user may:
- approve
- request changes
- reject part of the implementation
- ask questions
- request additional checks
Do not perform final validation until approved.
24. Phase 6 — Review Corrections
If the user requests changes:
- modify only requested areas
- preserve already-approved behavior
- avoid unrelated improvements
- rerun relevant focused tests
- review the resulting diff
Then report:
- requested changes completed
- files affected
- tests rerun
- results
- remaining concerns
- AI usage information if available
Ask the user to review again.
STOP.
Repeat this review cycle until approved.
25. Approval Gate 3 — Final Validation Permission
When the user approves implementation, obtain or recognize explicit permission to proceed with final validation.
Approval for final validation allows:
- applicable regression testing
- applicable formatting checks
- applicable frontend build
- applicable route verification
- migration review
- security review
- final diff review
It does not authorize the next feature.
26. Phase 7 — Final Validation
Run only checks relevant to the feature.
Potential commands include:
php artisan test
./vendor/bin/pint --test
npm run build
php artisan route:list
Not every feature requires every command.
Use professional judgment and the active feature specification.
27. Full Test Suite
The complete Laravel test suite should generally be run once during final validation for meaningful application changes.
Command:
php artisan test
Avoid repeated full-suite runs unless:
- a failure required correction
- a late change affects broader functionality
- the user requests another run
This reduces unnecessary processing.
28. PHP Formatting Validation
When PHP application code changes, run Laravel Pint validation where applicable.
Preferred command:
./vendor/bin/pint --test
If formatting fails:
- fix relevant formatting
- avoid unnecessary formatting of unrelated files
- rerun the relevant check
29. Frontend Build Validation
If the feature changes:
- Blade structure affecting assets
- CSS
- JavaScript
- Alpine.js
- Tailwind usage
- Vite configuration
run:
npm run build
Do not run repeated production builds during every small frontend edit.
30. Route Validation
If routes are created or modified, inspect relevant route registration.
Possible command:
php artisan route:list
Prefer filtering output where practical rather than producing very large route listings.
Verify:
- URI
- HTTP method
- route name
- middleware
- admin prefix where applicable
31. Migration Validation
When migrations are added or changed:
Review:
- migration direction
- rollback
- constraints
- indexes
- data risk
- existing data impact
Run safe migration-related validation appropriate to the current environment.
Do not reset the development database merely for convenience.
32. Security Review
For applicable features, check:
- authentication
- authorization
- input validation
- mass assignment
- CSRF
- XSS
- SQL injection risk
- file uploads
- sensitive information exposure
- open redirects
- privilege escalation
- destructive actions
Only review categories relevant to the feature.
Do not perform an expensive whole-application security audit for every small change.
33. Performance Review
For applicable features, check:
- N+1 queries
- unbounded lists
- unnecessary database calls
- excessive eager loading
- inefficient loops
- oversized asset handling
Avoid speculative optimization without evidence.
34. Phase 8 — Final Diff Review
Review local changes using permitted local tools.
The final review should distinguish:
- changes created for this feature
- pre-existing user changes
Verify there are no:
- unrelated edits
- debug statements
- temporary routes
- accidental secrets
- unwanted generated files
- unauthorized dependency changes
- unintended schema changes
- unnecessary TODOs
- commented experimental code
Do not connect to GitHub.
35. Phase 9 — Documentation Finalization
Update only documentation affected by the feature.
Possible updates include:
- active feature specification
- acceptance criteria
- completion record
- architecture documentation if the approved change requires it
- ADR if a significant architectural decision was approved
Do not rewrite unrelated documents.
36. Feature Completion Record
The active feature document should record, as applicable:
- implementation summary
- files changed
- migrations
- tests added
- tests executed
- validation commands
- final result
- known limitations
- unresolved non-blocking items
Keep the record concise.
37. Finalization Report
After final validation, report:
- feature ID/name
- acceptance criteria status
- implementation status
- tests run
- test results
- build/format status where applicable
- security review result
- migration/database result
- final diff status
- documentation updated
- remaining issues
- AI usage information if available
Then ask:
“Feature FXXX is ready for your final approval. May I mark it Complete?”
STOP.
38. Approval Gate 4 — Final Feature Approval
The feature remains incomplete until the user explicitly approves completion.
Do not mark it complete because:
- code works
- tests pass
- final validation passes
- the agent believes it is ready
The user is the final approval authority.
39. Phase 10 — Mark Complete
After explicit approval:
Update the feature status to:
Complete
Update the completion date only if the project convention requires it.
Do not perform additional unrelated changes.
40. Identify Next Feature
After marking the current feature complete:
Consult:
docs/07_FEATURE_ROADMAP.md
Identify the next planned feature.
Provide only:
- feature ID
- feature name
- one short statement of its objective
Then ask:
“Would you like me to begin analysis of FXXX?”
STOP.
41. Approval Gate 5 — Permission for Next Feature
Do not:
- analyze the next feature
- inspect its implementation files
- prepare its implementation plan
- modify code for it
until explicit user permission is received.
This prevents unnecessary token consumption and scope creep.
42. Bug Fix Workflow
A small bug fix may use the same workflow with a shorter plan.
For a small, clearly isolated issue:
Analysis
→ permission
→ fix
→ focused test
→ user review
→ final validation where applicable
→ final approval
Do not bypass user review merely because the change is small.
43. Emergency Security Issue
If a serious security vulnerability is discovered unexpectedly:
STOP normal feature work if continuing would create material risk.
Report:
- issue
- likely severity
- affected area
- immediate risk
- recommended next action
Do not perform a broad security rewrite without approval.
A narrowly necessary containment measure may be proposed for immediate approval.
44. Blocking Issue
If implementation cannot proceed because of:
- missing requirement
- conflicting architecture
- incompatible dependency
- missing configuration
- unsafe migration
- user modification conflict
- environment problem
STOP.
Report:
- blocker
- why it blocks the feature
- minimal options
- recommended option
Wait for user direction.
45. Expensive Operation Gate
Before performing an operation likely to consume significant AI/tool resources, request permission.
Examples:
- full repository analysis
- extensive web research
- large log analysis
- many test combinations
- large refactor
- multiple agents
- broad dependency analysis
- large migration/data transformation
State:
- why it is needed
- expected benefit
- cheaper alternative if one exists
Then wait for approval.
46. Web Research
Do not use web research by default for ordinary Laravel development.
Use it when:
- current official documentation is necessary
- compatibility needs verification
- a dependency/version issue requires current information
- the user explicitly asks for research
Prefer official sources.
Avoid browsing unrelated articles or comparisons.
47. GitHub Restriction
Agents must not connect to GitHub or another remote Git provider as part of this workflow.
The user handles remote operations manually.
Do not:
- push
- pull
- fetch
- clone
- create pull requests
- access GitHub APIs
- inspect remote repository state
- modify remote configuration
Local read-only Git inspection may be used according to AGENTS.md.
48. No Automatic Commit
Completion of a feature does not authorize a Git commit.
The agent must not automatically:
- commit
- amend
- push
- create branches
- create tags
The user will handle Git/GitHub operations manually unless they explicitly request a specific local Git action.
49. Low-Cost Workflow
Use LOW-COST MODE throughout this workflow.
Prefer:
- targeted file reads
- concise plans
- concise reports
- focused tests
- one full validation cycle
- reuse of existing project knowledge
Avoid:
- repeated project summaries
- broad scans
- redundant explanations
- repeated full tests
- unnecessary build commands
- unnecessary external research
- unnecessary alternative implementations
50. AI Usage Reporting
At each major checkpoint, provide the compact usage information required by:
AGENTS.md
and:
docs/08_COST_CONTROL.md
Do not perform additional processing merely to retrieve usage information.
If token/cost information is not directly available, state:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
51. Recommended Agent Prompt — Analysis Stage
A suitable low-cost prompt for starting a feature is:
Read AGENTS.md and the active feature specification.
Follow docs/03_DEVELOPMENT_WORKFLOW.md and docs/08_COST_CONTROL.md.
Perform only the Analysis and Implementation Plan phases.
Keep repository inspection targeted.
Do not modify application code.
Do not access GitHub or any remote repository.
At the end, provide the required concise analysis report, ask for permission to implement, and STOP.
52. Recommended Agent Prompt — Implementation Stage
After approving the plan, a suitable prompt is:
Approved.
Implement only the approved feature scope.
Do not install packages or make architectural changes unless separately approved.
Run focused relevant tests.
Do not perform final regression validation yet.
Do not access GitHub or any remote repository.
When implementation and focused testing are complete, provide the implementation review report, ask me to review the changes, and STOP.
53. Recommended Agent Prompt — Final Validation Stage
After reviewing implementation, a suitable prompt is:
Implementation approved.
Proceed with final validation for this feature only.
Run applicable final tests and quality checks once.
Perform the security review and final local diff review.
Update only required feature documentation.
Do not commit, push, access GitHub, or begin another feature.
Report the result, ask for final feature approval, and STOP.
54. Recommended Agent Prompt — Completion Stage
After final approval, a suitable prompt is:
Feature approved.
Mark this feature Complete.
Identify the next feature from the roadmap and state its objective briefly.
Do not analyze or implement it.
Ask for permission to begin its analysis, then STOP.
55. Final Workflow Principle
Every feature must be:
understood
→ planned
→ approved
→ implemented
→ tested
→ reviewed by the user
→ validated
→ approved again
→ completed
before another feature begins.
Agents must optimize for:
controlled progress
not autonomous progress.