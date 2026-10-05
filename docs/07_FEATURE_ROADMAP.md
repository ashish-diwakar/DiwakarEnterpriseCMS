Diwakar Enterprise CMS — Feature Roadmap
1. Purpose
This document defines the planned implementation sequence for Diwakar Enterprise CMS.
The roadmap provides:
- feature order
- feature identifiers
- feature dependencies
- development phases
- high-level objectives
The roadmap is a planning document.
It is NOT authorization to implement any feature.
Every feature still requires:
- its own feature specification
- analysis
- implementation plan
- user approval
- implementation
- focused testing
- user review
- final validation
- final user approval
Follow:
docs/03_DEVELOPMENT_WORKFLOW.md
2. Roadmap Authority
The user is the final authority for:
- feature priority
- feature order
- feature removal
- feature addition
- scope changes
Agents must not reorder the roadmap automatically.
If technical dependencies suggest a change in order:
- explain the reason
- identify the impact
- request user approval
Do not change roadmap order without approval.
3. Feature Statuses
Use the following statuses:
Planned
Analysis
Approved for Implementation
In Progress
Ready for Review
Changes Requested
Approved for Final Validation
Ready for Final Approval
Complete
A feature may only be marked:
Complete
after explicit user approval.
Phase 1 — CMS Foundation
F001 — Authentication Integration
Status:
Planned
Objective:
Integrate Laravel Breeze authentication into the Diwakar Enterprise CMS view and administration architecture.
Primary scope:
- login
- logout
- registration during development
- forgot password
- reset password
- password confirmation
- email verification where applicable
- CMS authentication layout integration
Important:
Do not redesign authentication architecture during this feature.
Public registration remains temporary until administrative user provisioning is defined.
Dependencies:
Laravel Breeze already installed.
F002 — Roles, Permissions and Super Admin
Status:
Planned
Objective:
Introduce role-based access control for CMS administration.
Planned scope:
- evaluate and request approval for Spatie Laravel Permission
- roles
- permissions
- role assignment
- permission assignment
- Super Admin behavior
- administrative access control
- initial administrator provisioning
- public registration decision
- authorization middleware/policies where appropriate
Important:
Spatie Laravel Permission is planned but must not be installed until the user explicitly approves the dependency.
Security testing is mandatory.
Dependencies:
F001
F003 — Admin Application Shell
Status:
Planned
Objective:
Create the reusable CMS administrative interface structure.
Planned scope:
- admin layout
- header
- sidebar
- footer
- responsive navigation
- account/user menu
- reusable navigation structure
- permission-aware navigation visibility where appropriate
Important:
Navigation visibility must not replace server-side authorization.
Dependencies:
F001
F002
F004 — Admin Dashboard
Status:
Planned
Objective:
Create the first functional CMS administration dashboard.
Potential scope:
- dashboard page
- reusable dashboard cards
- high-value content summaries
- administrative shortcuts
- appropriate empty states
Do not create speculative analytics.
Only show information backed by implemented CMS functionality.
Dependencies:
F003
Phase 2 — CMS Core Services
F005 — Website Settings
Status:
Planned
Objective:
Provide central administration of reusable website-level settings.
Potential scope:
- website name
- logo
- favicon
- business contact information
- business address
- public email
- telephone
- social links
- footer information
- selected branding information
- default website metadata where appropriate
Security-sensitive infrastructure credentials must not be stored as ordinary website settings.
Dependencies:
F002
F003
F006 — Media Library
Status:
Planned
Objective:
Provide centralized media management for CMS content.
Potential scope:
- image upload
- media listing
- media selection
- metadata
- safe file naming
- validation
- authorization
- deletion behavior
- reusable media references
Important security areas:
- MIME/type validation
- extension validation
- size limits
- safe storage
- executable-file prevention
Do not introduce cloud storage unless separately approved.
Dependencies:
F002
F003
F007 — Page Management
Status:
Planned
Objective:
Allow administrators to manage standard website pages.
Potential scope:
- create page
- edit page
- page title
- slug
- content
- publication state
- display/order behavior where required
- page listing
- authorization
Exact page data model must be defined by the feature specification.
Dependencies:
F002
F003
F006 where page media is required
F008 — Page Sections
Status:
Planned
Objective:
Support structured reusable content sections within pages.
Potential section concepts may include:
- hero
- text
- image/text
- feature blocks
- CTA
- statistics
- gallery
- FAQ
- testimonials
Important:
Do not build a complex drag-and-drop visual page builder unless separately approved.
Prefer structured, maintainable page sections.
Dependencies:
F007
F006
F009 — Menu Management
Status:
Planned
Objective:
Allow administrators to manage public website navigation.
Potential scope:
- menus
- menu items
- hierarchy
- ordering
- internal links
- external links
- menu locations
Dependencies:
F007
F003
F010 — SEO Management
Status:
Planned
Objective:
Provide reusable SEO capabilities across CMS-managed content.
Potential scope:
- SEO title
- meta description
- canonical information where needed
- indexing directives
- Open Graph metadata
- structured-data foundation
- sitemap strategy
- breadcrumb support
Avoid duplicating SEO logic independently across modules.
Dependencies:
F005
F007
Phase 3 — Content Modules
F011 — Services Module
Status:
Planned
Objective:
Manage reusable business-service content.
Potential scope:
- title
- slug
- summary
- description
- image
- publication status
- ordering
- SEO integration
The data model must remain reusable across industries where practical.
Dependencies:
F006
F010 where SEO integration is required
F012 — Projects / Portfolio Module
Status:
Planned
Objective:
Manage project, portfolio, or case-study style content.
Potential scope:
- title
- slug
- description
- images
- category where required
- project information
- publication status
- ordering
- SEO integration
Do not hard-code Dewatering India-specific project terminology unless approved.
Dependencies:
F006
F010
F013 — Blog Module
Status:
Planned
Objective:
Provide CMS-managed article/blog publishing.
Potential scope:
- posts
- title
- slug
- content
- featured image
- author
- publication date
- publication state
- categories where required
- SEO integration
Avoid unnecessary publishing-workflow complexity.
Dependencies:
F006
F010
F014 — Testimonials Module
Status:
Planned
Objective:
Manage customer/client testimonials.
Potential scope:
- testimonial text
- person name
- organization
- designation
- image
- publication state
- ordering
Dependencies:
F006 where images are used
F015 — Clients Module
Status:
Planned
Objective:
Manage client/customer/partner presentation content.
Potential scope:
- client name
- logo
- website URL where appropriate
- publication state
- ordering
Dependencies:
F006
F016 — Gallery Module
Status:
Planned
Objective:
Manage reusable image galleries.
Potential scope:
- gallery groups
- media selection
- captions
- ordering
- publication state
Dependencies:
F006
F017 — FAQ Module
Status:
Planned
Objective:
Manage frequently asked questions.
Potential scope:
- question
- answer
- category where required
- ordering
- publication state
- SEO/structured-data compatibility
Dependencies:
F010 where structured data integration is required
F018 — Contact Messages
Status:
Planned
Objective:
Provide secure handling of public contact/enquiry submissions.
Potential scope:
- public contact form
- server-side validation
- administrative message listing
- message details
- message status
- rate limiting/spam protection
- safe email integration where later configured
Important:
Do not automatically add paid CAPTCHA or external services.
Dependencies:
F002
F003
F019 — Activity Logs
Status:
Planned
Objective:
Provide useful administrative auditing.
Potential scope:
- content changes
- user administration actions
- settings changes
- selected security-relevant administrative activity
Important:
Do not log secrets or sensitive values unnecessarily.
Any proposed activity-log dependency requires explicit package approval.
Dependencies:
F002
Core modules sufficiently implemented to provide meaningful activity events
Phase 4 — Theme and Website Delivery
F020 — Theme Architecture
Status:
Planned
Objective:
Create a maintainable frontend theme architecture that remains separate from CMS core functionality.
Potential scope:
- theme layout structure
- reusable frontend components
- template organization
- branding integration
- CMS content consumption
- theme configuration where needed
Important:
Do not create an unnecessarily complex plugin/theme engine.
Build only what is required to support actual website implementations.
Dependencies:
Core CMS functionality required by the first website
F021 — Dewatering India Frontend Theme
Status:
Planned
Objective:
Build the first production-facing website implementation using Diwakar Enterprise CMS.
Potential scope:
- homepage
- company/about content
- services
- projects
- gallery
- clients
- testimonials
- FAQs
- contact
- approved SEO implementation
- responsive design
- accessibility
- performance considerations
Exact website information architecture and visual requirements must be specified before implementation.
Dependencies:
F020
Required content modules
Required SEO functionality
Required settings/media functionality
Phase 5 — Production Readiness
F022 — Production Security Hardening
Status:
Planned
Objective:
Perform production-oriented application security review before launch.
Potential areas:
- authentication configuration
- authorization review
- production debug configuration
- session/cookie settings
- HTTPS expectations
- security headers where appropriate
- public registration state
- administrator provisioning
- sensitive data exposure
- upload security
- dependency security review
This is not a substitute for secure implementation throughout earlier features.
Dependencies:
Major CMS functionality completed
F023 — Performance and Production Optimization
Status:
Planned
Objective:
Review actual application behavior before production release and address justified performance issues.
Potential areas:
- database indexes
- N+1 queries
- image optimization
- asset build
- caching where evidence justifies it
- pagination
- frontend payload
- production configuration
Do not introduce external caching infrastructure automatically.
Dependencies:
Major application features completed
F024 — Deployment Preparation
Status:
Planned
Objective:
Prepare the application for the production environment selected by the user.
Potential scope depends on the hosting decision.
May include:
- environment requirements
- deployment checklist
- filesystem requirements
- queue requirements if any
- scheduler requirements if any
- web-server requirements
- database migration process
- backup considerations
- environment variables
Important:
Production hosting has not yet been selected.
Do not assume:
- AWS
- Azure
- Docker
- Kubernetes
- shared hosting
- VPS
- another provider
until the user makes that decision.
4. Deferred / Future Capabilities
The following capabilities are not currently scheduled for implementation.
They may be considered later.
Do not implement them without explicit roadmap approval.
Potential future capabilities include:
- multi-site CMS
- multi-language content
- customer portal
- dealer portal
- CRM
- quotation management
- rental management
- e-commerce
- subscriptions
- advanced workflow system
- public API
- headless CMS
- AI content generation
- AI SEO generation
- chatbot
- visual drag-and-drop page builder
- advanced search engine
- external CDN
- cloud storage
- complex analytics
Their existence in this section does not imply future approval.
5. Feature Dependency Rule
A feature should not begin if a required dependency is incomplete.
If an agent believes a dependency can safely be removed or deferred:
explain the reasoning and request approval.
Do not work around missing dependencies by creating temporary architecture that will immediately require replacement.
6. Parallel Feature Development
The default development model is sequential.
Do not work on multiple major features simultaneously.
Parallel development may occur only if the user explicitly approves it.
This rule supports:
- simpler review
- reduced regressions
- lower AI cost
- easier troubleshooting
7. Feature Specification Rule
Before analyzing a roadmap feature in detail, create or confirm its dedicated specification under:
docs/features/
Example:
docs/features/F001_AUTHENTICATION.md
The specification defines actual implementation scope.
The roadmap only defines the high-level objective.
8. Roadmap Does Not Authorize Implementation
A feature appearing in this roadmap does not authorize:
- analysis
- repository inspection
- package installation
- code modification
- migration creation
- testing work
The previous feature must first be completed and the user must explicitly authorize starting analysis of the next feature.
9. Next Feature Workflow
After the current feature is marked Complete:
1. Read this roadmap only as necessary.
2. Identify the next planned feature.
3. State:
   - feature ID
   - feature name
   - one-sentence objective
4. Ask the user:
“Would you like me to begin analysis of FXXX?”
Then STOP.
Do not inspect implementation files for the next feature until permission is received.
10. Roadmap Changes
Do not modify this roadmap automatically.
If implementation reveals a need for:
- new feature
- reordered feature
- removed feature
- dependency change
- feature split
- feature merge
propose the roadmap change to the user.
Explain:
- reason
- impact
- recommended change
Wait for approval before modifying this document.
11. Feature Splitting
If a planned feature becomes too large:
propose splitting it into smaller independently reviewable features.
Example:
Instead of one large feature:
Media Management
it may become:
- media upload
- media browser
- media selection
- media cleanup
only if such splitting improves safety, clarity, or cost.
Do not split features unnecessarily.
12. Large Feature Rule
If a feature is classified as Large during analysis:
consider whether it should be divided into smaller milestones before implementation.
Before doing so:
- explain the benefit
- explain dependencies
- request approval
This helps keep code review manageable.
13. Package-Dependent Features
A roadmap feature may mention a planned package.
This does not grant installation permission.
Example:
F002 may use:
Spatie Laravel Permission
The feature analysis must still request package approval before installation.
14. Budget Control
Roadmap processing must follow:
docs/08_COST_CONTROL.md
To reduce AI usage:
- do not analyze future features early
- do not pre-read future feature implementation files
- do not generate detailed specifications for all roadmap items at once
- do not research dependencies before the relevant feature begins
- do not design future database schemas prematurely
Work only on the current approved roadmap item.
15. Current Active Feature
Current expected first controlled feature:
F001 — Authentication Integration
Its detailed requirements should be maintained in:
docs/features/F001_AUTHENTICATION.md
Do not begin F002 until:
- F001 implementation is reviewed
- final validation succeeds
- user explicitly approves F001 completion
- user explicitly authorizes F002 analysis
16. Roadmap Summary
Current planned order:
F001 — Authentication Integration
F002 — Roles, Permissions and Super Admin
F003 — Admin Application Shell
F004 — Admin Dashboard
F005 — Website Settings
F006 — Media Library
F007 — Page Management
F008 — Page Sections
F009 — Menu Management
F010 — SEO Management
F011 — Services Module
F012 — Projects / Portfolio Module
F013 — Blog Module
F014 — Testimonials Module
F015 — Clients Module
F016 — Gallery Module
F017 — FAQ Module
F018 — Contact Messages
F019 — Activity Logs
F020 — Theme Architecture
F021 — Dewatering India Frontend Theme
F022 — Production Security Hardening
F023 — Performance and Production Optimization
F024 — Deployment Preparation
17. Final Roadmap Principle
The roadmap defines direction.
The active feature specification defines requirements.
The approved implementation plan defines immediate work.
The user controls progression.
Agents must never treat future roadmap items as permission to implement them.