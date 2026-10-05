Diwakar Enterprise CMS — AI Cost and Token Control Policy
1. Purpose
This project operates under a limited AI-processing budget.
Agents must minimize unnecessary:
- token consumption
- model calls
- tool calls
- repository reading
- repeated analysis
- repeated test execution
- web research
- large command output
- documentation generation
- speculative work
- subagent usage
Cost optimization must not compromise:
- correctness
- security
- data integrity
- required testing
- maintainability
2. Default Operating Mode
All agents must operate in:
LOW-COST MODE
unless the user explicitly approves a more expensive operation.
LOW-COST MODE means:
- work only on the approved feature
- inspect only relevant files
- keep plans concise
- keep reports concise
- use focused tests first
- avoid repeated full validation
- avoid unnecessary web access
- avoid unnecessary subagents
- avoid speculative implementation
- avoid future-feature analysis
- reuse already-known project context
3. One Feature at a Time
Do not analyze or implement multiple roadmap features in one processing cycle unless explicitly approved.
Default behavior:
Current Feature
→ complete controlled workflow
→ user approval
→ ask permission for next feature
This prevents paying for analysis of work the user may later change, postpone, or reject.
4. One Stage at a Time
Agents must not automatically process an entire feature lifecycle in one large run.
Use these stages:
Stage A:
Analysis and implementation plan
Stage B:
Implementation and focused testing
Stage C:
User review/corrections
Stage D:
Final validation
Stage E:
Final approval and completion
Stage F:
Permission for next feature
Each major stage must respect the approval gates in:
docs/03_DEVELOPMENT_WORKFLOW.md
5. Analysis Budget
During feature analysis:
Read only enough code and documentation to safely prepare the implementation plan.
Do not attempt to fully understand the entire application.
Start with:
- AGENTS.md
- active feature specification
Then read additional documentation only if relevant.
Inspect only directly related application files.
6. Documentation Reading Efficiency
Do not read every governance file during every feature.
After reading AGENTS.md, follow references only when required.
Examples:
Authentication feature may need:
- active authentication feature specification
- security rules
- testing strategy
- relevant architecture sections
It does not automatically require rereading the entire roadmap, product scope, deployment rules, and unrelated module specifications.
7. Avoid Repeated File Reads
If a file has already been inspected during the current feature and has not changed:
do not repeatedly reload the complete file without a specific reason.
Reuse previously understood context.
Read again only when:
- the file changed
- exact content must be verified
- debugging requires it
- a final diff must be reviewed
8. Targeted Repository Inspection
Prefer targeted discovery.
Good behavior:
- search for the relevant controller
- inspect the relevant route
- inspect the relevant test
- inspect the relevant Blade component
Avoid:
- dumping the whole repository tree
- reading every controller
- reading every migration
- reading every model
- reading every Blade template
unless the feature genuinely requires broad analysis.
9. Directory Listings
Do not recursively print large directory structures merely for orientation.
Use the smallest directory listing needed to locate relevant files.
Once locations are known, inspect those files directly.
10. Search Before Broad Reading
When locating behavior, prefer targeted code search over opening many possible files.
Search for concepts such as:
- route name
- controller class
- model class
- view name
- test class
- specific method
Then read only relevant matches.
11. Keep Analysis Reports Concise
Normal feature analysis should report:
- current relevant behavior
- proposed approach
- affected files
- database impact
- dependency impact
- security considerations
- test approach
- complexity
- blockers
Avoid lengthy explanations of established Laravel concepts unless requested.
12. Implementation Plan Size
A normal implementation plan should usually contain approximately:
3–7 meaningful steps
Do not generate a 20-step plan for a small CRUD feature.
Large features may need more detail, but consider splitting them first.
13. No Speculative Alternatives
Do not generate many alternative implementations unless a meaningful architecture decision requires comparison.
Default behavior:
recommend the simplest appropriate approach.
If alternatives are necessary, limit discussion to the few serious options.
14. No Future-Feature Analysis
Do not inspect or design future roadmap items before the user authorizes them.
For example, while implementing F001 Authentication:
do not design:
- Media Library
- SEO
- Blog
- Theme Architecture
unless F001 genuinely depends on them.
15. No “While We Are Here” Work
Do not perform extra work merely because a file is already open.
Examples to avoid:
- rewriting nearby methods
- fixing unrelated naming
- modernizing unrelated code
- reorganizing folders
- updating unrelated documentation
Record meaningful technical debt separately if necessary.
16. Implementation Efficiency
Once implementation is approved:
make the smallest safe set of changes required by the approved plan.
Avoid repeated rewrites.
Prefer understanding the relevant implementation before editing.
17. Preserve Working Code
Do not rewrite working code simply to make it match the agent's preferred style.
Only change code when:
- required by the feature
- required for correctness
- required for security
- required by approved refactoring
This reduces token usage, regression risk, and review effort.
18. Avoid Large Generated Outputs
Do not output entire files in agent reports unless:
- the user requests them
- the file is newly proposed for manual creation
- showing the complete file is necessary for review
When the agent directly edits local files, report:
- file path
- purpose of change
- concise summary
The user can inspect the local diff.
19. Command Output Efficiency
Do not copy large successful command outputs into the conversation.
Preferred reporting:
AuthenticationTest — 8 passed
instead of hundreds of lines of successful testing output.
For failures:
report the relevant error portion first.
Expand only when needed.
20. Focused Testing First
During implementation, run the narrowest meaningful tests first.
Example:
php artisan test --filter=AuthenticationTest
instead of:
php artisan test
after every change.
Focused testing should catch immediate feature defects at lower processing cost.
21. Full Regression Timing
Run the full applicable regression test suite only after:
- implementation is complete
- focused tests pass
- the user has reviewed implementation
- the user authorizes final validation
Typical command:
php artisan test
Normally perform the complete suite once during final validation.
22. Repeated Full Tests
Do not repeatedly run the full test suite unless:
- a failure required correction
- a late change affects broad functionality
- the user requests another run
Prefer rerunning only affected tests after small corrections.
23. Build Efficiency
Do not run:
npm run build
for server-only PHP changes unless required.
Run the production frontend build when changes affect:
- CSS
- JavaScript
- Alpine.js
- Vite
- frontend asset compilation
- relevant Blade asset usage
Normally run final build once during final validation.
24. Route Inspection Efficiency
Do not print the complete route list unnecessarily.
When practical, inspect only relevant admin/frontend routes.
Use filtering when available.
Report only the relevant route result.
25. Database Efficiency
Do not:
- repeatedly inspect the entire schema
- dump the whole database
- read all migrations
- run destructive rebuilds
Inspect only relevant tables/migrations.
Never use database reset operations merely to simplify testing.
26. Migration Cost Control
When a feature needs schema changes:
analyze only the affected models/tables and directly related constraints.
Do not redesign unrelated schema.
Avoid speculative indexes and relationships not required by actual access patterns.
27. Dependency Research
Do not research packages merely because a package might be useful.
First ask:
Can Laravel already do this?
Then:
Does an existing approved dependency already do this?
Only research a new package when there is a concrete gap.
28. Package Installation
Every new package requires explicit user approval.
Do not spend substantial tokens comparing many packages unless the user approves package evaluation.
For normal package evaluation, compare only serious candidates.
29. Dependency Updates
Do not run broad dependency updates during ordinary feature development.
Avoid:
composer update
npm update
unless explicitly approved.
Broad updates create:
- unnecessary changes
- regression risk
- additional analysis
- additional testing cost
30. Web Research
Web research is not the default development tool.
Do not search the web when:
- project source already answers the question
- installed framework source is sufficient
- project documentation already defines the behavior
- normal Laravel conventions are sufficient
31. When Web Research Is Appropriate
Use web research when:
- current official documentation is required
- version compatibility is unclear
- a package's current status must be verified
- a current security advisory must be checked
- the user explicitly requests research
Prefer official sources.
32. Limit Web Searches
Use the minimum number of web searches necessary.
Do not search repeatedly using minor wording variations unless initial results are insufficient.
Do not browse unrelated tutorials after authoritative documentation answers the question.
33. GitHub Restriction
Do not use GitHub to obtain project information.
The local repository is authoritative for project code.
Do not:
- inspect GitHub repository
- fetch remote files
- inspect pull requests
- inspect issues
- inspect Actions
- use GitHub APIs
- use GitHub connectors
The user handles GitHub manually.
This also avoids unnecessary network/tool cost.
34. Subagents
Do not use subagents by default.
A single agent is preferred for normal CMS features.
Subagents may increase:
- model calls
- context duplication
- coordination overhead
- token usage
Use additional agents only after explicit user approval.
35. When Subagents May Be Proposed
A subagent may be proposed when a task is genuinely large or naturally parallel.
Examples might include:
- very large security review
- major migration analysis
- substantial legacy modernization
- complex independent research areas
Before use:
explain why it is expected to reduce total cost or improve safety.
Request approval.
36. Avoid Repeated Project Summaries
Do not restate:
- product vision
- entire architecture
- entire roadmap
- all security policies
during each feature response.
Reference the existing documents.
Report only information relevant to the current decision.
37. Documentation Efficiency
Do not create new documentation files unless they serve an ongoing project purpose.
Avoid generating:
- repetitive implementation reports
- duplicate architecture descriptions
- separate test reports for every feature
- duplicate requirement files
Use the active feature specification's completion record.
38. Governance Documents
Do not repeatedly rewrite governance documents during feature work.
Files such as:
- AGENTS.md
- ARCHITECTURE.md
- engineering rules
- workflow
- testing strategy
- security rules
- cost-control policy
should remain stable unless the user deliberately changes project governance.
39. Feature Documentation
Feature documentation should be concise but sufficient.
Record:
- requirements
- acceptance criteria
- important decisions
- final files changed
- tests
- completion result
Avoid storing full conversation transcripts.
40. Logs
Do not ask the model to analyze huge logs when only the final error matters.
For failures:
1. inspect the error section
2. inspect surrounding context
3. expand only when necessary
Avoid feeding thousands of successful log lines into model context.
41. Large Files
Do not load entire large files when only a specific section is needed.
Search for:
- class
- method
- configuration key
- heading
- relevant phrase
Then inspect the necessary surrounding section.
42. Large Repository Operations
Before:
- repository-wide refactoring
- broad static analysis
- large schema audit
- large security audit
- mass file transformations
STOP and request user approval.
Explain:
- reason
- expected benefit
- approximate scope
- cheaper alternative if available
43. User Review Saves Cost
Do not perform expensive final validation before the user reviews implementation.
Reason:
the user may request changes.
The preferred sequence is:
Implementation
→ focused tests
→ user review
→ corrections if needed
→ final validation
This avoids repeating expensive tests/builds.
44. User Approval Granularity
Do not ask for approval for every trivial action.
Too many approval turns also consume tokens.
Approval should occur at meaningful boundaries:
- implementation plan
- significant package/architecture/database decision
- implementation review
- final validation
- final completion
- next feature
45. Minor Fixes Within Approved Scope
After implementation approval, agents may independently:
- correct syntax errors
- fix imports
- adjust a small validation rule required by the approved specification
- fix directly related failing focused tests
- make small implementation corrections
Do not request approval for every tiny technical adjustment.
46. Expensive Operation Approval
Before an unusually expensive operation, state:
Operation:
what will be performed
Reason:
why it is required
Expected value:
what it should establish
Cheaper alternative:
if available
Then ask:
“May I proceed?”
STOP.
47. Complexity Classification
During planning classify each feature:
Small
Medium
Large
This classification is qualitative.
Do not spend additional model processing attempting to calculate an exact token budget unless the environment already provides such information.
48. Small Feature Guidance
A Small feature should generally use:
- limited file inspection
- short plan
- few file changes
- focused tests
- concise reporting
Avoid turning Small work into a large architecture exercise.
49. Medium Feature Guidance
A Medium feature may require:
- several related files
- database migration
- multiple tests
- security review
- moderate planning
Still avoid broad unrelated repository analysis.
50. Large Feature Guidance
Before implementing a Large feature:
consider proposing smaller milestones.
Splitting may improve:
- review
- testing
- rollback
- token usage
- risk management
Request approval before changing feature structure.
51. Token Usage Reporting
After every substantial project-processing stage, include a compact:
AI Usage
section.
Substantial stages include:
- analysis/plan
- implementation report
- review correction report
- final validation report
- feature finalization
52. Exact Token Reporting
If the execution environment provides exact token information, report available values such as:
AI Usage
Input tokens: actual value
Cached input tokens: actual value if available
Output tokens: actual value
Total tokens: actual value
Never modify actual reported values.
53. Cost Reporting
If:
- actual token usage is available
- current applicable model pricing is available
- the environment permits reliable calculation
the agent may report an estimated API model cost.
Clearly label it:
Estimated API cost
Do not describe it as final billing.
54. Do Not Hard-Code Pricing
Do not store model pricing in this repository.
AI pricing may change.
Do not calculate cost using remembered or outdated rates.
Use only current reliable pricing information when already available or explicitly requested.
55. Subscription vs API Billing
Do not assume API-style token cost represents money actually charged to the user's ChatGPT subscription.
If access is subscription-based:
do not claim an estimated API cost was deducted from the subscription.
Clearly distinguish:
- usage information
- estimated API-equivalent model cost
- actual billing
when such information is available.
56. Usage Unavailable
If token usage is not exposed by the current environment, report:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
This is an acceptable and correct report.
57. Never Fabricate Usage
Never invent:
- token counts
- cached-token counts
- dollar amounts
- request counts
- runtime charges
Do not guess and present the guess as measurement.
58. Do Not Spend Tokens to Discover Token Usage
Do not perform extra:
- model calls
- web searches
- API calls
- repository scans
- tool operations
solely to discover token usage or cost.
If usage is not already available:
report it as unavailable.
Usage reporting itself must remain low-cost.
59. Rough Estimates
Do not provide rough token/cost estimates by default.
If the user specifically requests an estimate and exact usage is unavailable:
clearly label the result as an estimate.
Explain the assumptions briefly.
Never mix estimates with measured values.
60. Tool/Runtime Costs
If model-token cost is available but tool/runtime charges are not:
state:
Model-token estimate only; additional tool/runtime charges may apply.
Do not invent unknown tool charges.
61. Processing Report Format
Normal stage reports should remain concise.
Recommended structure:
Feature:
FXXX — Name
Stage:
Analysis
or:
Implementation
or:
Final Validation
Result:
short summary
Files:
concise list/count
Tests:
concise result
Issues:
none or short list
AI Usage:
available measured information
or unavailable statement
Then the required approval question.
62. Avoid Cost Reports That Cost More Than the Work
AI usage reporting should normally be only a few lines.
Do not generate large token accounting reports.
Do not analyze historical usage unless the user explicitly requests it.
63. Failed Processing
If a task fails early:
stop once the blocker is understood.
Do not continue consuming resources trying many speculative fixes.
Report:
- failure
- likely cause
- smallest next step
Ask permission where required.
64. Debugging Budget
Use progressive debugging.
Start with:
- exact error
- directly related file
- directly related configuration
Expand only if the issue remains unresolved.
Avoid immediately scanning the whole project.
65. Do Not Repeat Failed Approaches
After an approach is proven invalid:
do not repeatedly retry it without a new reason.
Record the conclusion within the current feature context and try the next justified step.
66. Environment Verification
Do not repeatedly verify versions already defined and locally observable unless compatibility matters to the current feature.
Use local files/commands before web research.
Examples:
composer.lock
package-lock.json
php artisan --version
only when needed.
67. User-Provided Information
When the user has already provided reliable project information:
reuse it unless verification is necessary for correctness.
Do not repeatedly ask the user for information already available in the repository or current task context.
68. Avoid Unrequested Tutorials
When performing agent development work, reports should focus on:
- what was found
- what changed
- what passed
- what needs approval
Do not include long Laravel tutorials unless the user asks for an explanation.
69. Code Review Efficiency
For user review:
provide:
- concise summary
- file names
- important design decisions
- tests
- risks
Do not paste huge diffs unless requested.
The user can inspect local files/diff manually.
70. Final Validation Efficiency
Final validation should execute applicable checks once.
Potential checks include:
php artisan test
./vendor/bin/pint --test
npm run build
php artisan route:list
Only run what is relevant.
Do not run every possible command for every feature.
71. Security Must Not Be Skipped
Low-cost mode does not authorize skipping necessary:
- authorization tests
- validation
- security review
- file upload checks
- privilege checks
- sensitive-data review
Spend processing where risk justifies it.
72. Database Safety Must Not Be Skipped
Do not save cost by avoiding necessary review of:
- migrations
- indexes
- constraints
- rollback
- data-loss risk
Database errors can be more expensive than the AI processing required to prevent them.
73. Cost vs Quality Decision Rule
When choosing between:
A. a small amount of additional processing that materially improves correctness/security
and:
B. saving tokens while creating meaningful risk
choose A.
Low-cost mode means eliminating waste, not eliminating necessary engineering.
74. Cost Escalation Rule
If the current task unexpectedly becomes substantially larger than originally planned:
STOP before performing large additional work.
Explain:
- what changed
- why additional work is required
- whether the feature should be split
- likely complexity increase
Ask the user how to proceed.
75. Context Growth
Avoid allowing one agent conversation/session to accumulate unrelated feature history when a fresh feature context would be more efficient.
Where the tool/workflow permits, a fresh feature session may be preferable after a feature is fully completed.
The repository documentation remains the source of truth.
Do not rely solely on long conversation history.
76. Repository as Source of Truth
Use repository documentation to preserve durable project rules and feature state.
Do not require the user to repeatedly paste:
- architecture
- platform
- workflow
- security rules
- roadmap
into agent prompts.
Prompts should reference the files.
77. Recommended Low-Cost Feature Start
A normal feature-start instruction should be concise.
Example:
Read AGENTS.md and the active feature specification.
Follow the project workflow and cost-control policy.
Perform Analysis and Planning only.
Inspect only relevant files.
Do not modify code.
Do not access GitHub.
Ask permission before implementation and STOP.
This is preferable to repasting project rules into every prompt.
78. Recommended Low-Cost Implementation Instruction
After plan approval:
Approved.
Implement only the approved scope.
Run focused tests only.
Do not perform final regression yet.
Do not access GitHub.
Present the changes for my review and STOP.
79. Recommended Low-Cost Final Validation Instruction
After user review:
Implementation approved.
Perform applicable final validation once.
Review security and local diff.
Update only required feature documentation.
Do not commit or access GitHub.
Ask for final feature approval and STOP.
80. Recommended Completion Instruction
After final approval:
Mark the current feature Complete.
Identify the next roadmap feature and its objective only.
Do not analyze it.
Ask permission to begin and STOP.
81. Cost-Control Violations
Examples of avoidable waste include:
- reading every project file for a small feature
- repeatedly explaining the project architecture
- running the full suite after every minor edit
- researching packages not needed by the feature
- analyzing future features
- using several agents for routine CRUD work
- pasting huge successful command logs
- generating multiple alternative designs without need
- rewriting unchanged documentation
- accessing remote services unnecessarily
Agents should actively avoid these patterns.
82. User Override
The user may explicitly request:
- deeper analysis
- broader testing
- web research
- multiple alternatives
- more detailed explanation
- additional agents
When explicitly requested, follow the user's instruction.
Such approval applies only to the requested operation.
83. Final Cost-Control Principle
Use AI processing where it produces engineering value.
Do not spend AI processing on:
- repetition
- speculation
- unnecessary breadth
- unnecessary verbosity
- premature future work
Default strategy:
Targeted analysis
→ concise plan
→ approval
→ focused implementation
→ focused testing
→ user review
→ one final validation cycle
→ final approval
→ stop
This is the required balance between:
low cost
and
professional software quality.