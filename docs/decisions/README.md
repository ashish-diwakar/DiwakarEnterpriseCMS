Diwakar Enterprise CMS — Architecture Decision Records
1. Purpose
This directory stores Architecture Decision Records (ADRs) for significant technical decisions that may affect multiple features or the long-term maintainability of Diwakar Enterprise CMS.
ADRs should document important decisions, not routine implementation details.
Do not create an ADR for every feature.
2. When an ADR Is Required
Consider creating an ADR when a decision:
- changes the approved application architecture
- introduces a major dependency
- introduces a new architectural layer
- changes authentication architecture
- changes authorization architecture
- changes database technology
- changes frontend framework
- introduces a queue system
- introduces external cache infrastructure
- introduces cloud storage
- introduces an external service with long-term impact
- changes deployment architecture
- establishes a pattern that several future features will depend on
- creates a significant long-term technical constraint
3. When an ADR Is Not Required
Do not create an ADR for ordinary implementation choices such as:
- controller naming
- Form Request naming
- routine CRUD implementation
- normal Laravel routing
- simple validation rules
- Blade markup changes
- small refactoring
- ordinary bug fixes
- adding a migration required by an approved feature
- routine use of an already-approved package
These decisions belong in the feature implementation or feature specification where necessary.
4. User Approval
Agents must not independently make a major architectural decision and then document it afterward.
The correct process is:
Identify architectural decision
→ explain the issue
→ present recommended approach
→ discuss meaningful alternatives
→ explain risks and impact
→ request user approval
→ create ADR if appropriate
→ implement only after approval
An ADR records an approved decision.
It does not grant approval by itself.
5. ADR File Naming
Use:
ADR-XXX-SHORT-DESCRIPTION.md
Examples:
ADR-001-AUTHORIZATION-STRATEGY.md
ADR-002-MEDIA-STORAGE-ARCHITECTURE.md
ADR-003-THEME-ARCHITECTURE.md
Numbers should normally be sequential.
Do not renumber existing ADRs.
6. ADR Status
Recommended statuses:
Proposed
Accepted
Superseded
Rejected
An agent may prepare:
Proposed
only when the user has authorized preparation of the architectural proposal.
Set:
Accepted
only after explicit user approval.
7. ADR Template
Use the following structure when an ADR is required.
────────────────────────────────────
ADR-XXX — Decision Title
Status:
Proposed
1. Context
Describe the problem or architectural decision that must be made.
Explain only the context needed to understand the decision.
2. Decision Drivers
List important factors.
Examples:
- maintainability
- security
- performance
- simplicity
- Laravel compatibility
- operating cost
- existing architecture
- future extensibility
3. Options Considered
Option A — Name
Brief description.
Advantages:
- advantage
Disadvantages:
- disadvantage
Option B — Name
Brief description.
Advantages:
- advantage
Disadvantages:
- disadvantage
Only document serious alternatives.
Do not create artificial options merely to make the ADR look complete.
4. Decision
Describe the selected approach.
If the decision is still awaiting approval:
Pending user approval.
5. Rationale
Explain why the selected option is preferred.
Keep this focused on meaningful trade-offs.
6. Consequences
Positive
- consequence
Negative / Trade-offs
- consequence
7. Security Impact
Describe relevant security implications.
If none:
No material security impact identified.
8. Database Impact
Describe database implications.
If none:
None.
9. Dependency Impact
Describe new or changed dependencies.
If none:
None.
10. Cost / Operational Impact
Describe meaningful development, infrastructure, licensing, or operational cost implications.
If none:
No material additional cost identified.
11. Migration / Adoption
Describe how the decision will be introduced into the existing application.
If no migration is necessary:
No migration required.
12. Approval
User Approval:
Pending
Date:
Pending
────────────────────────────────────
8. ADR Finalization
After user approval:
Update:
Status:
Accepted
and:
User Approval:
Approved
Record the date only if the project convention requires it.
Do not rewrite the reasoning merely because implementation has started.
9. Superseding an ADR
Do not delete an accepted ADR merely because the architecture later changes.
Instead:
1. Create a new ADR.
2. Explain the new decision.
3. Reference the previous ADR.
4. Mark the previous ADR:
Superseded
5. Reference the new ADR from the old record where useful.
This preserves architectural history.
10. Rejected Proposals
If a formally documented proposal is rejected and retaining the decision history is useful:
mark it:
Rejected
Do not implement it.
If the proposal was trivial and no lasting record is useful, an ADR may not be necessary.
11. ADR and Feature Specifications
Feature specifications answer:
“What must this feature do?”
ADRs answer:
“What significant architectural decision did we make, and why?”
Do not duplicate complete feature requirements inside ADRs.
Reference the relevant feature where appropriate.
12. ADR and Architecture Documentation
After an ADR is accepted and implemented:
update ARCHITECTURE.md only if the decision changes the project's durable architecture.
ARCHITECTURE.md should describe the current architecture.
The ADR should preserve the reasoning behind the decision.
13. Dependency Decisions
Installing a normal small package does not automatically require an ADR.
An ADR may be appropriate when the package:
- becomes foundational architecture
- affects many future features
- creates strong vendor/library coupling
- changes application design substantially
Package installation still requires approval under AGENTS.md.
14. Cost-Control Rule
Do not create ADRs for minor decisions.
Do not produce lengthy technology comparisons unless the decision genuinely requires them.
Normally compare only:
- the recommended option
- one or two serious alternatives
Follow:
docs/08_COST_CONTROL.md
15. GitHub Restriction
ADR research and creation must not require access to the project's GitHub repository.
Use the local project as the source of truth.
If external documentation research is required, follow the project's web-research rules.
Do not use GitHub connectors or remote repository operations.
16. Final Principle
Create an ADR only when future developers or agents are likely to ask:
“Why did we choose this architecture?”
If the answer matters beyond the current implementation task, an ADR may be justified.
If the decision is routine and obvious from standard Laravel conventions, an ADR is probably unnecessary.