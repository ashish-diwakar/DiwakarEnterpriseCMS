Diwakar Enterprise CMS — Agent Instructions
1. Project Mission
Build a reusable, secure, maintainable, enterprise-grade CMS using Laravel.
The first website powered by this CMS will be Dewatering India.
However, the CMS core must remain reusable for future websites, businesses, industries, and themes.
Do not introduce Dewatering India-specific business logic into the CMS core unless an approved feature specification explicitly requires it.

2. Primary Development Principle
Work on only one approved feature at a time.
Do not:
- implement future features
- perform unrelated refactoring
- install unnecessary packages
- make speculative architectural changes
- continue automatically to the next feature
Every feature requires explicit user approval at defined checkpoints.

3. Project Documentation
AGENTS.md is the primary control document.
For each feature, begin by reading:
1. AGENTS.md
2. The active feature specification under docs/features/
Read additional project documents only when they are relevant to the current task or decision.
Supporting documentation includes:
- docs/00_PLATFORM.md
- docs/01_PRODUCT_SCOPE.md
- ARCHITECTURE.md
- docs/02_ENGINEERING_RULES.md
- docs/03_DEVELOPMENT_WORKFLOW.md
- docs/04_TESTING_STRATEGY.md
- docs/05_DEFINITION_OF_DONE.md
- docs/06_SECURITY_RULES.md
- docs/07_FEATURE_ROADMAP.md
- docs/08_COST_CONTROL.md
Do not automatically read every project document for every task.
Do not repeatedly read documentation already understood during the current feature unless:
- the file changed
- a specific rule needs verification
- the current decision depends on it
The active feature specification defines the immediate implementation scope.

4. Human-in-the-Loop Development
This project requires explicit user approval between major stages.
The normal lifecycle is:
Feature Specification
→ Analysis
→ Implementation Plan
→ USER APPROVAL
→ Implementation
→ Focused Testing
→ USER CODE REVIEW
→ Corrections if required
→ USER APPROVAL
→ Final Validation
→ Finalization Report
→ USER FINAL APPROVAL
→ Feature Complete
→ PERMISSION REQUIRED FOR NEXT FEATURE
Never skip these approval gates.

4A. Approval Scope
Every approval applies only to the specific stage or action presented to the user.
For example:
- approval of an implementation plan authorizes implementation of that approved plan only
- implementation approval does not authorize package installation
- implementation approval does not authorize architectural changes
- implementation approval does not authorize destructive database operations
- implementation approval does not authorize final feature completion
- feature completion approval does not authorize work on the next feature
Never interpret a previous approval as blanket authorization.
If an action requires separate approval elsewhere in these instructions, obtain that approval before performing it.
Silence is not approval.


5. Stage 1 — Analysis and Planning
Before modifying application code:
- read the active feature specification
- inspect only relevant existing files
- inspect directly related routes
- inspect directly related models
- inspect directly related controllers
- inspect directly related migrations
- inspect directly related views
- inspect directly related tests
- identify dependencies
- identify security implications
- identify database implications
- identify potential regression risks
Do not modify application code during this stage.
Prepare a concise implementation plan.
The plan should normally contain approximately 3–7 meaningful steps.
At the end of analysis report:
- proposed implementation
- expected files to change
- database impact
- package/dependency impact
- security considerations
- tests expected
- complexity: Small, Medium, or Large
- available AI usage information
Then ask:
“May I proceed with implementation?”
STOP.
Do not implement anything until explicit approval is received.
6. Stage 2 — Implementation
Implementation begins only after explicit user approval.
Implement only the approved feature scope.
During implementation:
- follow Laravel conventions
- keep controllers thin
- use Form Requests for meaningful validation
- use server-side authorization
- use Eloquent relationships appropriately
- use Actions or Services for meaningful business logic
- use database transactions for related multi-write operations
- use Blade components for reusable UI
- use named routes
- write or update directly relevant automated tests
Do not:
- implement the next feature
- perform unrelated refactoring
- change major architecture without approval
- install packages without approval
- upgrade dependencies without approval
- make speculative improvements
7. Focused Testing
After implementation, run only tests directly relevant to the feature first.
Examples may include:
php artisan test --filter=AuthenticationTest
or another directly relevant test class/filter.
Do not repeatedly run the complete test suite after every small change.
Fix failures related to the approved implementation.
Once focused testing succeeds, provide a review report.
8. User Code Review Gate
After implementation and focused testing, report:
- summary of functionality implemented
- files changed
- migrations added or changed
- tests added or changed
- focused tests executed
- test results
- assumptions
- known issues
- potential risks
- available AI usage information
Then ask:
“Please review the changes. May I proceed with final validation?”
STOP.
Wait for the user's review.
Do not assume approval.
9. Review Corrections
If the user requests changes:
- modify only the requested areas
- avoid unrelated improvements
- rerun only necessary focused tests
- summarize the corrections
- present the changes again for review
Then STOP.
Do not perform final validation until the user approves the implementation.
10. Final Validation
Final validation begins only after user approval.
Run applicable checks once.
Possible checks include:
php artisan test
./vendor/bin/pint --test
npm run build
php artisan route:list
Only run commands applicable to the current feature.
Also perform:
- security review
- validation review
- authorization review
- migration review
- final diff review
- regression review
Do not execute unnecessary expensive checks.
11. Final Diff Review
Before declaring a feature ready:
Review all changed files.
Verify:
- no unrelated changes
- no temporary debugging code
- no accidental generated files
- no secrets
- no unnecessary dependencies
- no commented-out experimental code
- no unexplained TODO items
- no duplicate logic
- no accidental route changes
- no accidental migration changes
12. Feature Finalization
After final validation:
- update the feature specification
- update acceptance criteria
- update architecture documentation only if required
- create/update an ADR only when an architectural decision changed
- prepare a concise completion report
Then ask:
“Feature FXXX is ready for your final approval. May I mark it Complete?”
STOP.
The agent cannot approve its own work.
13. Feature Completion
Only after explicit user approval:
Change the feature status to:
Complete
Then identify the next roadmap feature.
Ask:
“Would you like me to begin analysis of FXXX?”
STOP.
Do not analyze or implement the next feature until explicit permission is received.
Completion of one feature does not authorize work on another feature.
14. Package Installation Rules
Do not install a package automatically.
Before installing any new dependency:
- explain why it is needed
- explain whether Laravel already provides equivalent functionality
- explain maintenance implications
- explain security implications if relevant
- identify the package
- request approval
STOP.
Install only after explicit approval.
15. Architecture Changes
Do not make major architecture changes automatically.
Examples include:
- introducing a new architectural layer
- changing authentication architecture
- replacing database technology
- changing frontend framework
- adopting a repository abstraction throughout the application
- changing deployment architecture
- introducing queues
- introducing caching infrastructure
- introducing a new storage provider
Explain the proposed decision first.
If significant, propose an ADR.
Wait for approval.

15A. Governance File Protection
Project governance documents control agent behavior and must not be modified as part of normal feature development.
Do not modify the following unless the user explicitly requests changes to project governance:
- AGENTS.md
- ARCHITECTURE.md
- docs/00_PLATFORM.md
- docs/01_PRODUCT_SCOPE.md
- docs/02_ENGINEERING_RULES.md
- docs/03_DEVELOPMENT_WORKFLOW.md
- docs/04_TESTING_STRATEGY.md
- docs/05_DEFINITION_OF_DONE.md
- docs/06_SECURITY_RULES.md
- docs/07_FEATURE_ROADMAP.md
- docs/08_COST_CONTROL.md
Feature implementation may update the active feature specification as required by the approved workflow.
Architecture documentation or ADRs may be updated only when:
- the approved feature genuinely changes architecture, and
- the applicable workflow permits the update
Do not weaken, remove, bypass, or reinterpret governance rules in order to complete a feature.
If a governance rule conflicts with implementation requirements:
STOP and report the conflict to the user.


16. Laravel Engineering Rules
Prefer Laravel conventions over unnecessary abstractions.
Controllers should coordinate requests and responses.
Controllers should not contain substantial business logic.
Use Form Requests for non-trivial validation.
Use Policies, Gates, or approved permission mechanisms for authorization.
Use Eloquent relationships where appropriate.
Prevent N+1 query problems.
Use eager loading when appropriate.
Use dependency injection.
Use named routes.
Use Blade components for reusable UI.
Use database transactions when multiple related writes must succeed atomically.
Use Laravel migrations for schema changes.
17. Repository Pattern Rule
Do not create Repository classes automatically for every model.
Eloquent already provides a strong data-access abstraction.
Introduce a Repository only when there is a concrete need for a separate persistence abstraction.
Avoid architecture for architecture's sake.
18. Routing Rules
Frontend routes belong in:
routes/frontend.php
Admin routes belong in:
routes/admin.php
Administrative URLs use:
/admin/...
Administrative route names use:
admin.*
Examples:
admin.dashboard
admin.users.index
admin.pages.edit
Admin functionality must be protected by authentication and authorization once those features are available.
19. Database Safety
Never execute destructive database commands without explicit approval.
Do not run commands such as:
php artisan migrate:fresh
unless explicitly authorized.
Do not:
- drop tables
- truncate production-like data
- delete large datasets
- reset databases
- rewrite migration history
without permission.
Migrations should be reversible wherever reasonably practical.
Consider:
- foreign keys
- indexes
- uniqueness constraints
- nullable behavior
- rollback behavior
- data migration implications
20. Security Rules
Never:
- commit .env
- expose passwords
- expose API keys
- expose tokens
- expose private keys
- disable security controls simply to make something work
Do not trust client-side validation.
Administrative operations require server-side authorization.
Use Blade escaping by default.
Do not use unescaped output unless content has been explicitly sanitized.
Do not disable CSRF protection for standard web forms.
Validate file uploads carefully when media functionality is implemented.
21. Testing Integrity
Never:
- delete a failing test simply to obtain a green build
- weaken an assertion without a valid reason
- disable security tests to make implementation pass
- claim functionality was tested when tests were not executed
If testing cannot be completed, clearly state why.

22A. Existing Local Work Protection
Before modifying code for an approved feature, inspect the local working tree using read-only local Git commands where useful.
Examples:
git status
git diff
The purpose is to identify changes that existed before the agent started working.
Existing user changes must be treated as protected work.
Never:
- discard unrelated user changes
- overwrite unrelated modified files
- revert user changes
- reset the working tree
- restore files merely because they differ from HEAD
- remove untracked files unless the user explicitly requests it
If an existing user modification conflicts with the approved feature:
STOP and explain the conflict.
Ask the user how to proceed.
Before final validation, distinguish:
- changes created for the current feature
- changes that already existed before the feature
Do not claim pre-existing changes as agent-created work.

22. Git and GitHub Rules
The user manages GitHub and all remote Git operations manually.
Agents must work only with the local project repository.
Allowed Local Git Operations
Agents may use read-only local Git commands when useful for development and review.
Examples:
git status
git diff
git diff --stat
git log
git show
These commands may be used to:
- understand the existing working tree
- identify pre-existing changes
- review implementation changes
- perform final diff review
GitHub and Remote Access Prohibited
Agents must NEVER independently connect to GitHub or another Git remote.
Do not:
- push
- pull
- fetch
- clone
- synchronize
- create pull requests
- merge pull requests
- inspect GitHub issues
- inspect GitHub pull requests
- create GitHub issues
- modify GitHub repository settings
- create releases
- create tags on a remote
- access GitHub Actions
- trigger remote workflows
- access GitHub Secrets
- access GitHub APIs
- authenticate with GitHub
- use GitHub tokens
- use SSH to access GitHub
- connect to another Git hosting provider
Do not use:
git push
git pull
git fetch
git clone
gh
GitHub APIs
GitHub MCP/connectors
GitHub plugins
or equivalent remote-repository tools.
Do not open or inspect the remote GitHub repository through web browsing unless the user explicitly requests external GitHub research.
Git Remote Configuration
Do not:
- add a Git remote
- remove a Git remote
- change a remote URL
- rename a remote
- modify remote tracking configuration
Examples of prohibited commands include:
git remote add
git remote remove
git remote set-url
Remote repository configuration belongs to the user.
Local Commits
Do not create local Git commits automatically.
Do not:
- commit
- amend commits
- rebase
- squash
- cherry-pick
- reset
- create or delete branches
- create tags
unless the user explicitly requests that specific local Git operation.
Code implementation does not imply permission to commit it.
The default workflow is:
Agent modifies local files
→ Agent tests changes
→ Agent presents changes
→ User reviews changes
→ User handles Git/GitHub manually
Remote State
Do not attempt to determine whether the local repository is ahead of, behind, or synchronized with GitHub by contacting the remote server.
If remote state is required, ask the user to check it manually.
GitHub Responsibility
The user is solely responsible for:
- GitHub authentication
- repository creation
- remote configuration
- pushing code
- pulling code
- fetching remote changes
- branch publishing
- pull requests
- merges
- releases
- remote tags
- repository permissions
- repository secrets
- GitHub Actions configuration and execution
Agents must not perform these operations unless this policy is explicitly changed by the user.

23. Cost-Control Policy
This project has a limited AI-processing budget.
Always follow:
docs/08_COST_CONTROL.md
Operate in LOW-COST MODE by default.
Minimize unnecessary:
- token consumption
- repository scanning
- repeated file reads
- repeated test execution
- verbose explanations
- web searches
- package research
- subagents
- speculative analysis
- unrelated code generation
Cost reduction must not compromise correctness, security, or data integrity.
24. Repository Reading Efficiency
Inspect only what is needed.
Prefer targeted searches before opening many files.
Do not dump the complete repository.
Do not repeatedly read files already understood during the current feature unless they changed or are required again.
Do not recursively inspect every migration, controller, model, or view without a specific reason.
25. Web Research
Do not use web research for routine implementation when:
- the installed project source is sufficient
- Laravel behavior is already clear
- repository documentation provides the answer
Use external research only when:
- current official documentation is required
- compatibility must be verified
- a package/version issue requires investigation
- the user explicitly requests research
Prefer official sources.
26. Subagents
Do not use multiple agents or subagents by default.
Use additional agents only when:
- explicitly approved by the user, or
- a complex task clearly benefits enough to justify the additional cost
For normal CMS feature development, use a single-agent workflow.

25A. External Services and Account Access
Do not connect to external accounts, repositories, cloud services, deployment platforms, databases, or third-party administrative systems unless the current approved task explicitly requires it and the user has granted permission.
In particular, never independently connect to:
- GitHub
- GitLab
- Bitbucket
- production hosting
- production databases
- cloud consoles
- domain registrars
- DNS providers
- email accounts
- payment systems
- analytics accounts
Local project development should remain local by default.
Web research of public documentation is separate from account access and may only be used according to the Web Research rules.


27. AI Usage Reporting
Every substantial project-processing response must end with a compact AI Usage section.
If exact usage information is available, report:
AI Usage
Input tokens: actual value
Cached input tokens: actual value if available
Output tokens: actual value
Total tokens: actual value
Estimated API cost: actual calculated estimate if available
Usage source: runtime/API
Cost status: estimate, not final billing
If exact usage is unavailable, report:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
Never invent token counts.
Never invent dollar costs.
Never present a guess as measured usage.
If usage is subscription-based rather than API-billed, do not claim that an estimated API cost represents money deducted from the user's subscription.
Do not perform additional tool calls, API calls, repository scans, web searches, or other processing solely to discover token usage or monetary cost.
If usage information is not already available from the current execution environment, report:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
Then continue without attempting to retrieve usage information elsewhere.
Usage reporting itself must remain low-cost.

28. Expensive Operations
Before performing an unusually expensive operation, request approval.
Examples:
- repository-wide refactoring
- reading a very large number of files
- analyzing huge generated logs
- extensive web research
- many test permutations
- multiple complete test-suite runs
- dependency upgrades
- use of multiple agents
- large-scale migration work
Explain why the operation is necessary.
Then ask permission.
STOP.
29. Scope Control
If a requirement is materially unclear:
STOP and ask.
Do not invent business requirements.
Do not implement what “might be useful later.”
Do not expand scope because related functionality seems convenient.
Record potential future improvements separately rather than implementing them.
30. Definition of Completion
A feature is not complete merely because code compiles or tests pass.
A feature is complete only when:
- approved requirements are satisfied
- acceptance criteria are satisfied
- focused tests pass
- applicable regression tests pass
- applicable quality checks pass
- security has been reviewed
- database impact has been reviewed
- final diff has been reviewed
- documentation has been updated where required
- user has reviewed the implementation
- user has explicitly approved completion
The user is the final approval authority.