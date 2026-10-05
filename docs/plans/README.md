Diwakar Enterprise CMS — Execution Plans
1. Purpose
This directory is used for temporary execution plans for sufficiently large or complex development tasks.
Execution plans help agents break substantial work into controlled implementation stages without expanding the permanent governance documentation.
Execution plans are not required for every feature.
Most Small and normal Medium features should use the Implementation Plan section already available in their feature specification.
2. When an Execution Plan Is Appropriate
Consider creating a separate execution plan when a feature or technical task:
- is classified as Large
- requires many coordinated file changes
- spans several implementation milestones
- involves significant database migration work
- involves a major architectural transition
- requires careful sequencing
- cannot safely be completed as one normal implementation stage
- requires several independently testable sub-stages
- may take multiple development sessions
Do not create an execution plan merely because a feature has several files.
3. When an Execution Plan Is Not Required
A separate plan is normally unnecessary for:
- small bug fixes
- routine CRUD functionality
- simple Blade changes
- ordinary validation
- small route changes
- small controller changes
- normal authentication adjustments
- simple migrations
- minor UI enhancements
For these tasks, use the feature specification's:
Implementation Plan
section.
4. User Approval
Agents must not create a large execution plan automatically unless:
- the current feature genuinely requires it
- or the user requests one
If an agent believes a separate execution plan would be useful:
explain why.
Then ask the user for permission before spending significant effort creating it.
5. Directory Structure
Execution plans are stored under:
docs/plans/active/
Completed plans are moved to:
docs/plans/completed/
Do not create plans for future roadmap features before those features are approved for analysis.
6. File Naming
Use a clear name associated with the feature or technical task.
Preferred format:
FXXX_SHORT_DESCRIPTION_PLAN.md
Examples:
F020_THEME_ARCHITECTURE_PLAN.md
F024_DEPLOYMENT_PREPARATION_PLAN.md
For a non-feature technical task:
SECURITY_HARDENING_PLAN.md
Use simple, descriptive names.
7. Execution Plan Status
Recommended statuses:
Draft
Approved
In Progress
Blocked
Ready for Review
Complete
Do not mark the plan Complete until its approved work has been completed and reviewed.
8. Relationship to Feature Specifications
The feature specification remains the authority for:
- requirements
- scope
- out of scope
- acceptance criteria
- security requirements
- testing requirements
The execution plan defines:
- implementation sequence
- implementation milestones
- dependencies between implementation steps
- validation checkpoints
The execution plan must not silently expand feature scope.
9. Scope Changes
If execution planning reveals that the approved feature scope should change:
STOP.
Explain:
- why the scope change appears necessary
- proposed change
- impact
- alternatives
Request user approval.
Do not modify the feature scope merely through the execution plan.
10. Recommended Plan Structure
Use the following structure when a separate execution plan is required.
────────────────────────────────────
FXXX — Execution Plan Title
Status:
Draft
1. Objective
Briefly describe what this execution plan coordinates.
2. Related Feature
Feature:
FXXX — Feature Name
Specification:
docs/features/FXXX_FEATURE_NAME.md
3. Reason for Separate Plan
Explain why the normal feature implementation plan is insufficient.
Keep this concise.
4. Preconditions
List requirements that must already be satisfied before implementation begins.
Examples:
- prerequisite feature complete
- package approved
- migration strategy approved
5. Constraints
List important implementation constraints.
Examples:
- preserve existing database data
- no GitHub access
- no new package without approval
- no production infrastructure changes
6. Implementation Stages
Stage 1 — Name
Objective:
Short description.
Expected Files:
- file
- file
Validation:
- focused check
User Review Required:
Yes or No
Stage 2 — Name
Objective:
Short description.
Expected Files:
- file
Validation:
- focused check
User Review Required:
Yes or No
Add only genuinely required stages.
7. Database Impact
Describe schema/data impact.
If none:
None
8. Dependency Impact
Describe dependencies.
If none:
None
9. Security Considerations
List only task-specific security considerations.
10. Testing Strategy
Describe how each meaningful stage will be tested.
Reference:
docs/04_TESTING_STRATEGY.md
rather than duplicating global testing rules.
11. Rollback / Recovery Considerations
Describe how incomplete or failed implementation can be safely recovered when relevant.
If not relevant:
Standard local source rollback/review process applies.
Do not perform Git reset or destructive operations automatically.
12. Risks
List significant implementation risks only.
13. Completion Criteria
List what must be true before the execution plan is considered complete.
14. Progress Record
Keep progress concise.
Stage 1
Status:
Pending
Result:
Pending.
Stage 2
Status:
Pending
Result:
Pending.
15. Final Result
Status:
Pending
Validation:
Pending.
Known Issues:
Pending.
────────────────────────────────────
11. Keep Plans Small
An execution plan should contain only enough information to safely coordinate implementation.
Do not create:
- lengthy architectural essays
- complete code samples
- copied source files
- repeated governance rules
- duplicated feature requirements
Reference existing project documentation instead.
12. Implementation Stages
Stages should represent meaningful units of work.
Good examples:
- establish database foundation
- implement backend behavior
- implement admin UI
- add tests
- integrate frontend
Avoid creating stages for trivial actions such as:
- open file
- add import
- rename variable
- run formatter
13. Stage Approval
The normal project approval workflow still applies.
An execution plan does not grant blanket authorization to execute every stage automatically.
If the user approved the entire implementation plan, ordinary implementation stages may proceed within that approved scope.
However, separate approval is still required for:
- new dependencies
- architecture changes
- destructive database actions
- external services
- expensive operations
- scope expansion
Follow:
AGENTS.md
and:
docs/03_DEVELOPMENT_WORKFLOW.md
14. Review Checkpoints
For a large implementation, the plan may define intermediate review checkpoints.
Use them only when reviewing the work incrementally materially reduces risk.
Do not add unnecessary approval turns for every small stage.
Balance:
- user control
- review value
- AI-processing cost
15. Testing During Plans
Use focused validation after meaningful implementation stages.
Do not run the complete regression suite after every stage.
Run full applicable regression validation during the approved final-validation phase unless a specific risk justifies an earlier full run.
16. Plan Changes
If implementation requires changing the execution plan materially:
- explain why
- update only the affected sections
- request approval if the change alters scope, architecture, cost, or risk significantly
Do not rewrite the entire plan after every implementation detail changes.
17. Progress Updates
Progress records should remain concise.
Example:
Stage 1:
Complete
Result:
Database migration and model created.
Focused Tests:
6 passed
Do not copy full command logs into the plan.
18. Blocked Plans
If implementation becomes blocked:
Set status:
Blocked
Record:
- blocker
- affected stage
- minimum decision/action needed
Then STOP and ask the user.
Do not continue into unrelated stages simply to remain productive.
19. Completed Plans
After:
- implementation finishes
- required validation passes
- user approves the associated feature
- the plan no longer represents active work
the plan may be moved from:
docs/plans/active/
to:
docs/plans/completed/
Do not move it before feature completion.
20. Historical Plans
Completed plans are retained only when they provide useful implementation history.
Do not accumulate low-value execution plans indefinitely.
For routine features, the feature specification completion record should be sufficient.
21. GitHub Restriction
Execution plans must not depend on GitHub operations.
Agents must not:
- create GitHub issues from plans
- create pull requests
- push branches
- inspect remote branches
- synchronize with GitHub
- use GitHub connectors
The user handles GitHub manually.
22. Local Git
Permitted local read-only Git commands may be used to:
- inspect working state
- identify existing user changes
- review final diff
Follow AGENTS.md.
Do not automatically commit execution-plan changes.
23. Cost-Control Requirements
Execution plans must follow:
docs/08_COST_CONTROL.md
Do not create a plan when the cost of planning exceeds the value it provides.
For most features:
use the feature specification's short implementation plan instead.
Separate plans should be the exception.
24. Large Feature Rule
If a feature is classified:
Large
first consider whether:
- it should use an execution plan
- it should be divided into smaller approved features
Do not automatically split it.
Present the recommendation to the user.
25. Plan vs Feature Split
Use an execution plan when:
the feature remains one cohesive requirement but requires several implementation stages.
Split into multiple features when:
the work contains independently valuable functionality that should have separate:
- requirements
- acceptance criteria
- review
- completion decisions
The user must approve feature splitting.
26. AI Usage Reporting
Execution-plan processing follows the same AI usage rules as other project work.
Do not perform extra processing solely to calculate usage.
If unavailable, report:
AI Usage
Tokens: unavailable in this environment
Cost: unavailable
No estimate fabricated
27. Final Principle
Execution plans exist to reduce implementation risk for genuinely complex work.
They must not become additional bureaucracy.
Default:
Small feature
→ use feature specification plan
Medium feature
→ usually use feature specification plan
Large feature
→ consider a separate execution plan
Use the lightest planning structure that safely supports the work.