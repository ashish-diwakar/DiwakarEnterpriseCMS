Diwakar Enterprise CMS — Platform Specification
1. Purpose
This document defines the approved technical platform for Diwakar Enterprise CMS.
Agents must build against this platform unless the user explicitly approves a change.
Do not upgrade, replace, or introduce major platform technologies simply because a newer or alternative technology exists.
The installed project files and lock files are authoritative for exact dependency versions.
2. Application Platform
Backend Language
PHP
Current development version:
PHP 8.2.12
Current PHP executable:
C:\xampp\php\php.exe
Do not change the required PHP version without explicit user approval.
Do not modify Composer platform requirements merely to bypass compatibility problems.
3. Framework
Framework:
Laravel
Current installed version:
Laravel 12.64.0
This project uses Laravel 12 architecture and conventions.
Do not:
- upgrade Laravel automatically
- downgrade Laravel automatically
- migrate to another PHP framework
- introduce framework-specific patterns from older Laravel versions without verifying compatibility
The exact installed Laravel version in composer.lock is authoritative if it differs from this document due to an explicitly approved dependency update.
4. Dependency Management
PHP dependency manager:
Composer
Current development version:
Composer 2.10.2
Important files:
composer.json
composer.lock
composer.lock defines the exact installed PHP package dependency set.
Agents must not run broad dependency updates such as:
composer update
without explicit approval.
Installing a new Composer dependency requires separate user approval according to AGENTS.md.
Do not remove or replace existing dependencies unless required by an approved feature and approved by the user.
Prefer:
composer install
when restoring the dependency set defined by the lock file.
5. Database Platform
Database engine:
MariaDB
Current local development version:
MariaDB 10.4.32
Laravel connection type:
mysql
Current development database:
dss_enterprise_cms
Expected local configuration:
DB connection: mysql
Host: 127.0.0.1
Port: 3306
Database: dss_enterprise_cms
Development database credentials belong in .env.
Never commit database passwords or other credentials.
Do not hard-code credentials into application source code.
6. Database Schema Management
Laravel migrations are the authoritative mechanism for application schema changes.
Do not manually alter the database schema as a substitute for creating an appropriate migration.
Schema changes should consider:
- foreign keys
- indexes
- unique constraints
- nullability
- default values
- rollback behavior
- existing data
- migration safety
Never run destructive database commands without explicit user approval.
In particular, do not automatically run:
php artisan migrate:fresh
php artisan db:wipe
or equivalent destructive operations.
7. Local Database Administration
The current development environment may use XAMPP/phpMyAdmin for manual database administration.
phpMyAdmin is a development convenience and is not part of the application architecture.
Agents must not assume phpMyAdmin exists in production.
Do not build application functionality that depends on phpMyAdmin.
8. Frontend Rendering
Primary server-side presentation technology:
Laravel Blade
Use Blade for application views unless an approved architectural decision changes this.
Do not automatically replace Blade with:
- React
- Vue
- Angular
- Next.js
- Livewire
- Inertia
- another frontend application framework
Adding or replacing a frontend framework is an architectural decision requiring explicit approval.
9. Blade Architecture
The project separates administrative and public website views.
Primary view areas:
resources/views/admin/
resources/views/frontend/
Reusable Blade components are stored under:
resources/views/components/
and corresponding class-based components may be stored under:
app/View/Components/
Prefer Blade components for reusable UI structures.
10. Frontend Styling
The project uses Tailwind CSS through the existing Laravel/Vite frontend toolchain.
The exact installed Tailwind version is defined by:
package.json
and:
package-lock.json
Do not independently upgrade Tailwind.
Do not introduce another CSS framework such as:
- Bootstrap
- Foundation
- Bulma
- Material UI
unless explicitly approved.
Custom CSS may be added where appropriate, but unnecessary duplication of Tailwind functionality should be avoided.
11. Client-Side JavaScript
The project may use Alpine.js for lightweight client-side interaction.
Alpine.js was introduced through the Laravel Breeze Blade scaffolding.
Use Alpine.js for simple UI interactions where appropriate.
Do not introduce a large JavaScript framework merely for functionality that Blade and Alpine.js can reasonably provide.
12. Asset Build System
Asset bundler:
Vite
Current observed Vite version:
7.3.6
Laravel loads application assets using the Vite integration.
Primary assets currently include:
resources/css/app.css
resources/js/app.js
Development command:
npm run dev
Production asset validation/build command:
npm run build
The production build generates the Vite manifest under:
public/build/
Do not manually maintain generated Vite asset filenames.
13. Node.js Environment
Current development Node.js version:
Node.js 24.1.0
Current npm version:
npm 11.3.0
JavaScript dependencies are defined by:
package.json
Exact installed dependency versions are controlled by:
package-lock.json
Do not run dependency upgrades merely because newer versions exist.
Do not remove package-lock.json.
Do not regenerate the dependency tree unnecessarily.
14. Authentication
Authentication foundation:
Laravel Breeze
Scaffolding type:
Blade
Breeze provides the base authentication implementation.
Current/planned authentication functionality includes:
- login
- logout
- registration during development
- password reset
- password confirmation
- email verification where applicable
- profile functionality where applicable
Authentication presentation will be integrated into the custom CMS administration architecture.
Do not replace Breeze authentication automatically.
Changes to the authentication architecture require explicit approval.
15. Public Registration
Public user registration may remain available temporarily during early development.
It is not intended to remain unrestricted for the production CMS administration system.
The planned RBAC/Super Admin feature will define how initial administrative users are provisioned and when public registration should be disabled.
Do not independently decide to permanently expose public administrator registration.
16. Authorization
Planned authorization package:
Spatie Laravel Permission
It has not automatically become an approved dependency merely because it appears in this document.
Installation requires explicit user approval when the RBAC feature is processed.
Planned concepts include:
- Super Admin
- Admin
- Editor
- Author
- Viewer
Exact roles, permissions, and authorization behavior must be defined by the relevant feature specification before implementation.
17. Testing Framework
Primary backend test framework:
PHPUnit through Laravel's testing infrastructure.
Primary test command:
php artisan test
Focused tests should be used during implementation whenever possible.
Example pattern:
php artisan test --filter=RelevantTest
The complete applicable test suite should normally be run during final validation rather than repeatedly after every small code change.
Detailed testing rules are defined in:
docs/04_TESTING_STRATEGY.md
18. Code Formatting and Quality
Laravel Pint should be used where available for PHP formatting validation.
Preferred final validation command:
./vendor/bin/pint --test
On Windows, the exact executable invocation may vary depending on the shell/environment.
Agents should inspect the installed project tooling before assuming a command is unavailable.
Do not introduce a new PHP code formatter without approval.
19. Development Operating System
Primary development operating system:
Windows 11
Primary local development stack:
XAMPP
XAMPP currently provides:
- PHP
- MariaDB
- local supporting tools
Development instructions should remain compatible with Windows unless the relevant feature explicitly requires another environment.
Do not assume Linux-only shell commands are available.
When proposing commands, prefer commands compatible with the user's Windows development environment.
20. Local Application Server
Laravel's development server may be used locally.
Command:
php artisan serve
Typical local application URL:
http://127.0.0.1:8000
This is a local development mechanism only.
It does not define the future production web-server architecture.
21. Local Frontend Development
During frontend development, Vite may be run separately.
Command:
npm run dev
A common local workflow therefore uses:
Terminal 1:
php artisan serve
Terminal 2:
npm run dev
Alternatively, production-style assets may be generated using:
npm run build
Agents should not start unnecessary long-running processes if the task does not require them.
22. Routing Architecture
Public website routes belong in:
routes/frontend.php
CMS administration routes belong in:
routes/admin.php
Laravel bootstrap routing registration is configured through:
bootstrap/app.php
Admin URL prefix:
/admin
Admin route-name prefix:
admin.
Examples:
admin.dashboard
admin.users.index
Do not relocate route architecture without an approved architectural change.
23. API Architecture
An API architecture has not yet been approved as a core CMS requirement.
Do not create REST APIs, GraphQL APIs, public API authentication, or API versioning merely because they might be useful in the future.
Create API functionality only when an approved feature requires it.
24. Queues and Background Processing
No production queue architecture has currently been selected.
Do not automatically introduce:
- Redis queues
- Horizon
- RabbitMQ
- Kafka
- external queue services
If a future feature requires background processing, analyze the requirement and request architectural approval first.
Laravel's existing facilities may be evaluated at that time.
25. Caching
No external caching infrastructure has currently been approved.
Do not automatically introduce Redis, Memcached, or another external cache server.
Use Laravel's existing configured caching capabilities where sufficient.
A future caching architecture must be approved before introducing external infrastructure.
26. File Storage
The production file-storage provider has not yet been selected.
Do not assume:
- Amazon S3
- Azure Blob Storage
- Google Cloud Storage
- another cloud-storage service
Local Laravel storage may be used during development where appropriate.
The Media Library feature will define additional storage requirements.
Changing storage architecture requires approval.
27. Search Infrastructure
No dedicated search engine has currently been approved.
Do not introduce:
- Elasticsearch
- OpenSearch
- Meilisearch
- Algolia
- Typesense
unless a future approved feature demonstrates a requirement.
Use appropriate database querying for normal CMS functionality unless requirements justify dedicated search infrastructure.
28. Email Infrastructure
Production email delivery has not yet been selected.
Do not assume or configure an external email provider automatically.
Possible future providers are outside the current platform scope.
Authentication-related email functionality should use Laravel's configured mail abstraction.
Real provider credentials must never be committed.
29. Deployment Platform
Production hosting has not yet been selected.
Agents must not assume deployment to:
- AWS
- Azure
- Google Cloud
- DigitalOcean
- Laravel Cloud
- shared hosting
- VPS
- Docker
- Kubernetes
- Vercel
- another hosting provider
Deployment architecture will be decided separately.
Do not introduce deployment-specific infrastructure during ordinary feature development.
30. Containers
Docker is not currently required for this Laravel project's local development workflow.
Do not introduce:
- Dockerfiles
- Docker Compose
- container orchestration
- Kubernetes manifests
unless explicitly approved.
The current Windows/XAMPP workflow remains the default development environment.
31. CI/CD
No agent-managed CI/CD platform has currently been approved.
Do not configure or modify:
- GitHub Actions
- GitLab CI
- Bitbucket Pipelines
- Azure DevOps Pipelines
- Jenkins
- other remote CI/CD systems
The user manages GitHub and remote repository operations manually.
Agents must follow the Git/GitHub restrictions in AGENTS.md.
32. GitHub and Remote Repository Access
GitHub is not part of the agent's development execution environment.
Agents must not independently connect to GitHub or another Git hosting provider.
Remote repository activities are handled manually by the user.
Agents may use permitted local read-only Git commands for local change inspection and diff review according to AGENTS.md.
Do not require:
- GitHub credentials
- Personal Access Tokens
- SSH keys
- GitHub CLI authentication
- GitHub connectors
- remote repository access
to implement normal project functionality.
33. External Services
Do not connect the project to external services unless required by an approved feature.
Examples include:
- cloud platforms
- payment gateways
- analytics providers
- external email providers
- CDN services
- AI APIs
- monitoring services
- external storage providers
Every new external dependency must be justified and approved.
34. Environment Configuration
Environment-specific settings belong in:
.env
The repository may provide:
.env.example
.env.example must contain safe placeholders rather than real credentials.
Do not expose secrets in:
- PHP source files
- Blade files
- JavaScript files
- committed configuration
- documentation
- test fixtures
- logs
35. Version Authority
When documentation and installed dependencies differ, use the following precedence:
1. Explicit latest user-approved decision
2. Lock files
3. Installed project configuration
4. This platform document
5. Assumptions
Important authoritative files include:
composer.lock
package-lock.json
Agents must not silently change versions to make documentation match.
If a meaningful discrepancy is found:
report it to the user.
Do not upgrade automatically.
36. Platform Change Process
A platform change includes significant changes such as:
- PHP version
- Laravel major version
- database engine
- frontend framework
- authentication architecture
- authorization architecture
- deployment platform
- queue infrastructure
- cache infrastructure
- storage architecture
Before making such a change:
1. Explain why the change is needed.
2. Explain benefits.
3. Explain risks.
4. Explain migration impact.
5. Explain maintenance impact.
6. Explain dependency impact.
7. Explain expected cost/complexity.
8. Request explicit user approval.
For significant architectural decisions, propose an ADR.
Do not implement the change until approved.
37. Dependency Version Verification
Do not spend AI tokens or perform web research merely to determine dependency versions that are already available locally.
When exact versions are needed, inspect the relevant local file first:
PHP:
composer.lock
JavaScript:
package-lock.json
Project requirements:
composer.json
package.json
Use web research only when current external compatibility information is genuinely required.
38. Low-Budget Platform Rule
Platform analysis must follow the project's low-cost policy.
Do not repeatedly:
- inspect all dependencies
- regenerate dependency reports
- run broad security scans
- search the web for installed versions
- compare technologies that are not under consideration
Only investigate platform details required by the current approved feature.
Detailed cost-control rules are defined in:
docs/08_COST_CONTROL.md
39. Current Approved Platform Summary
Backend:
PHP 8.2.12
Laravel 12.64.0
Composer 2.10.2
Database:
MariaDB 10.4.32
Frontend:
Blade
Tailwind CSS
Alpine.js
Vite 7.3.6
Runtime tooling:
Node.js 24.1.0
npm 11.3.0
Authentication:
Laravel Breeze — Blade scaffolding
Authorization:
Spatie Laravel Permission planned, not yet approved/installed as part of the documented RBAC feature
Testing:
PHPUnit / Laravel test runner
Development OS:
Windows 11
Local stack:
XAMPP
Production hosting:
Not yet selected
External cache:
Not selected
External queue:
Not selected
Cloud storage:
Not selected
CI/CD:
Not selected
Remote Git/GitHub operations:
Handled manually by the user