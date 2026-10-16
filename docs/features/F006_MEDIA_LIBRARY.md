F006 — Media Library
Status:
Planned
1. Objective
Add reusable CMS media management for uploading, browsing, organizing, selecting, and safely deleting website assets such as images and documents.
The Media Library should provide a reusable asset foundation for later CMS features without becoming a full Digital Asset Management system.
2. Background
Completed foundation:
F001 — Authentication Integration
Provides secure CMS authentication.
F002 — Roles, Permissions and Super Admin
Provides centralized RBAC and Super Admin authorization bypass.
F003 — Admin Application Shell
Provides reusable admin navigation and responsive layout.
F004 — Admin Dashboard
Provides the current CMS dashboard.
F005 — Website Settings
Provides reusable site-level configuration.
F005 intentionally deferred:
- logo
- favicon
- media selection
until F006 establishes the Media Library architecture.
3. Purpose
The Media Library should provide one reusable mechanism for website assets needed by future features such as:
- Website Settings
- Pages
- Page Sections
- Services
- Projects / Portfolio
- Blog
- Testimonials
- Clients
- Gallery
- frontend themes
Future CMS modules should select existing media rather than creating separate upload systems.
4. Core Principles
The Media Library should be:
- reusable
- business-neutral
- secure
- simple for administrators
- compatible with Laravel storage
- independent of any specific frontend theme
- suitable for future media pickers
- extensible without premature abstraction
Do not create separate upload logic inside individual CMS modules when F006 can provide the shared foundation.
5. In Scope
F006 should evaluate and implement the minimum practical reusable Media Library.
Expected capabilities include:
- upload approved files
- persist media metadata
- browse uploaded assets
- image/file distinction
- search/filter where justified
- view media details
- safely delete unneeded assets
- generate reusable media URLs/references
- provide a foundation for future media selection
- responsive admin UI
- server-side authorization
- secure storage and validation
Exact capabilities must be finalized during Analysis.
6. Out of Scope
F006 does not automatically include:
- full Digital Asset Management
- cloud media CDN integration
- S3 migration
- Azure Blob Storage
- image AI
- automatic background removal
- image editing
- cropping UI
- image annotation
- advanced transformations
- Photoshop-style tools
- video transcoding
- audio transcoding
- large-video hosting
- drag-and-drop page builder
- rich text editor integration
- media versioning
- duplicate-image detection
- facial recognition
- OCR
- image tagging AI
- EXIF management system
- bulk import from external providers
- stock image search
- multi-site media partitioning
- multilingual media metadata
- arbitrary executable file uploads
Do not implement these without separate approval.
7. Dependencies
Required completed features:
F001 — Authentication Integration
Status:
Complete
F002 — Roles, Permissions and Super Admin
Status:
Complete
F003 — Admin Application Shell
Status:
Complete
F004 — Admin Dashboard
Status:
Complete
F005 — Website Settings
Status:
Complete
8. User Roles / Actors
Super Admin
May manage media through the existing centralized Super Admin authorization bypass.
Authorized Admin/User
May manage Media Library only when granted the approved media permission.
Unauthorized Authenticated User
Must not access or mutate Media Library administration.
Guest
Must not access Media Library administration.
9. Permission Model
F006 Analysis must determine the minimum useful permission model.
Potential options:
Option A
Single permission:
media.manage
Option B
Separate permissions:
- media.view
- media.upload
- media.delete
Preferred direction for the initial CMS:
use the minimum number of permissions unless meaningful business needs justify granular separation.
A single:
media.manage
permission is likely sufficient initially.
Do not introduce unnecessary permission granularity.
10. Admin Default Permission
Analysis must recommend whether normal Admin should receive the approved media permission by default.
Consider that Media Library management can:
- upload public assets
- remove assets
- affect future website content
Preferred conservative direction:
do not automatically grant new permissions to Admin unless clearly justified.
Super Admin remains authorized through centralized Gate::before.
Any default Admin-role change requires user approval.
11. Storage Architecture
F006 must use Laravel-native storage unless Analysis identifies a compelling reason otherwise.
Analysis should evaluate:
- Storage facade
- configured filesystem disk
- public disk
- public storage symlink requirements
- URL generation
- storage path conventions
Preferred initial direction:
Laravel public disk with files stored under a dedicated CMS media directory.
Potential conceptual path:
media/YYYY/MM/...
or another predictable safe structure.
Do not hard-code absolute Windows paths into application code.
12. Storage Disk
The Media Library should not assume that all future installations use the same physical storage provider.
Prefer:
Laravel filesystem configuration
over:
direct filesystem APIs.
The implementation should remain reasonably portable to:
- local/public disk
- S3
- other Laravel-supported disks
without implementing those providers now.
13. Storage Link
If the public Laravel disk is used, Analysis must verify the project's current:
public/storage
link situation.
Do not silently create or modify operating-system-level links without reporting the requirement.
If:
php artisan storage:link
is needed for local development, state that explicitly.
14. Package Decision
Analysis must determine whether a media package is actually necessary.
Potential option:
Spatie Laravel Media Library
However, no package should be installed merely because Media Library functionality is being added.
Preferred direction:
use Laravel-native filesystem functionality and a simple Media model if requirements remain small.
Any new package requires explicit user approval.
15. Media Model
Analysis should recommend a clear model name.
Preferred:
Media
Alternative:
MediaAsset
Choose a name that remains clear across future modules.
Avoid ambiguous or theme-specific naming.
16. Media Table
The Media Library should persist metadata separately from physical storage.
Potential fields may include:
- id
- original_name
- file_name
- disk
- path
- mime_type
- extension
- size
- width
- height
- title
- timestamps
Analysis must determine the minimum required set.
Do not store unnecessary metadata merely because it is technically available.
17. Original Filename
The original uploaded filename may be retained for display.
However:
the physical stored filename should not blindly reuse user-supplied filenames.
Use a safe generated filename strategy.
Potential approaches:
- UUID
- ULID
- Laravel-generated unique name
Analysis should recommend one.
18. Stored Filename Security
Physical filenames must:
- avoid path traversal
- avoid executable naming tricks
- avoid user-controlled directory paths
- prevent accidental overwrite
- remain stable after upload
Do not trust the client-provided filename for storage-path construction.
19. Allowed Media Types
F006 Analysis must recommend the initial approved file types.
Potential image types:
- JPEG
- PNG
- WebP
- GIF
Potential document types:
- PDF
Do not automatically permit arbitrary files.
Potentially dangerous formats such as:
- PHP
- executable files
- shell scripts
- HTML uploads
- JavaScript files
must not be accepted.
20. SVG Security
SVG can contain active content and introduces additional XSS/security considerations.
Preferred initial direction:
do not allow SVG uploads in F006 unless there is a demonstrated requirement and safe handling strategy.
SVG support may be evaluated later.
21. File-Type Validation
Validation must not rely only on the filename extension.
Analysis should recommend Laravel validation using:
- MIME/content-aware validation
- approved extensions/types
- maximum size
where appropriate.
Reject files whose actual content does not match approved media types.
22. File Size
Analysis must recommend a reasonable initial maximum upload size.
Avoid unnecessarily large limits.
Consider:
- website images
- PDFs
- normal hosting limits
- PHP upload limits
If images and documents need different limits, Analysis may recommend separate rules.
Do not silently change PHP/XAMPP server configuration.
23. Image Dimensions
For images, storing:
- width
- height
may be useful for:
- media browsing
- frontend selection
- future layout decisions
Analysis should determine whether this metadata is worth recording now.
Do not implement image resizing solely to obtain dimensions.
24. Image Processing
Do not add automatic image processing unless approved.
F006 should not automatically implement:
- resizing
- thumbnail pipelines
- cropping
- compression
- format conversion
Analysis may recommend a minimal thumbnail approach only if the admin Media Library cannot reasonably function without it.
Any image-processing dependency requires approval.
25. Media Preview
Admin browsing should provide an appropriate preview.
For images:
display image thumbnail/preview where practical.
For non-image documents:
display a neutral file/document representation.
Do not attempt to render arbitrary documents inline unnecessarily.
26. Media Library Index
Expected admin route concept:
GET /admin/media
The Media Library index should provide a clear list/grid of uploaded assets.
Analysis should determine whether:
- grid
- list
- responsive hybrid
best fits the initial implementation.
Preferred direction for images:
responsive grid.
27. Upload Interface
Expected upload behavior:
- choose file
- validate
- upload
- persist metadata
- display success feedback
- newly uploaded media becomes available in library
Do not introduce JavaScript-heavy upload frameworks unless justified.
Standard multipart form upload is acceptable initially.
28. Single vs Multiple Upload
Analysis should determine whether F006 initially supports:
- one file per upload
- multiple files per request
Preferred low-complexity direction:
single file upload unless multiple upload provides substantial administrator value without meaningful complexity.
Do not implement complex batch processing prematurely.
29. Drag and Drop
Drag-and-drop upload may improve usability but is not mandatory.
Do not add custom JavaScript solely for visual polish unless the standard upload experience is inadequate.
Analysis should recommend whether it is worth including.
30. Media Details
A media item detail view or panel may show:
- preview
- original filename
- MIME/type
- dimensions where applicable
- file size
- upload date
- title if supported
- public URL/reference
Analysis should determine whether a dedicated detail page is necessary or whether the initial index can provide enough functionality.
31. Media Title
A user-editable title may be useful for administration.
Analysis should determine whether the initial model should include:
title
Potential behavior:
- optional
- defaults conceptually to original filename for display
- does not rename physical file
Avoid unnecessary metadata editing.
32. Alt Text
Image alt text is often contextual to where the image appears.
The same image may require different alt text in different page contexts.
Therefore:
do not automatically make a single Media Library alt_text value the authoritative accessibility text for every usage.
Analysis may determine whether optional library-level descriptive text has value, but accessibility text should generally remain usage-specific in later content modules.
33. Organization
F006 objective includes reasonable media organization.
Analysis should determine whether initial organization should use:
- search
- media-type filtering
- sorting
rather than:
- folders
- nested folders
- categories
- tags
Preferred direction:
start with search/filter/sort if adequate.
Do not build a complex folder hierarchy without demonstrated need.
34. Search
Potential search targets:
- original filename
- title
Search should remain simple and efficient.
Do not introduce external search infrastructure.
35. Filtering
Potential filters:
- all
- images
- documents
Additional MIME-specific filtering should only be added if useful.
36. Sorting
Potential useful sorting:
- newest first
- oldest first
- filename/title
Preferred initial default:
newest first.
Do not implement elaborate sorting controls without value.
37. Pagination
Media Library should use server-side pagination when browsing database records.
Do not load an unlimited media collection into one admin page.
Analysis should recommend a reasonable page size.
38. Deletion
Authorized users should be able to delete media safely.
Deletion should coordinate:
- database record
- physical stored file
Analysis must define failure handling.
Avoid leaving unnecessary orphan files or orphan database rows.
39. Referenced Media Safety
Future CMS features may reference Media Library records.
Deleting referenced media could break frontend content.
F006 Analysis must determine the appropriate initial deletion strategy before those references exist.
Potential future strategies include:
- prevent deletion when referenced
- soft delete
- usage tracking
- confirmation with reference checks
Do not invent a complex usage-reference system before consuming modules exist.
40. Current Deletion Strategy
Because later features do not yet reference Media Library assets, F006 may initially allow permanent deletion with clear confirmation.
However, the architecture must not make future reference protection unnecessarily difficult.
Analysis should document this explicitly.
41. Soft Deletes
Do not add soft deletes automatically.
Analysis should determine whether they provide real value for the initial library.
Preferred direction:
no soft deletes initially unless recovery requirements justify them.
42. Database Transaction / Storage Consistency
Uploads involve both:
- filesystem state
- database state
Analysis should define safe sequencing so failures do not leave inconsistent records/files where reasonably preventable.
Likewise deletion should handle filesystem/database consistency carefully.
Do not introduce distributed transaction abstractions.
43. URL Generation
Media URLs should be generated through Laravel filesystem/storage conventions.
Do not manually concatenate public URLs when the storage system can generate them.
The database should preferably store:
- disk
- relative path
rather than an absolute environment-specific URL.
44. Environment Portability
Do not persist values such as:
http://127.0.0.1:8000/storage/...
as the canonical media identity.
Persist storage metadata and derive URLs at runtime.
This keeps media records portable across:
- local development
- staging
- production
- domain changes
45. Website Settings Integration
F005 deferred:
- logo
- favicon
until F006.
F006 Analysis must explicitly recommend whether F006 should also integrate Media Library selection into Website Settings now.
Potential options:
Option A
F006 only builds Media Library foundation.
Logo/favicon integration remains a later enhancement.
Option B
F006 adds minimal Website Settings media selection after the reusable Media Library/picker exists.
No F005 change should occur automatically.
If Website Settings integration is recommended, it must be explicitly approved as F006 scope before implementation.
46. Media Picker
Future modules will need to select existing assets.
F006 should consider a reusable media-selection mechanism.
Potential approaches:
- dedicated picker page/modal
- reusable Blade component
- simple media selection route/partial
Do not build an elaborate JavaScript media picker prematurely.
Analysis must distinguish:
- what must exist in F006
- what can wait until the first consuming module requires selection
47. Reuse by Future Features
Future content modules should ideally reference media records rather than store duplicate file paths independently.
Analysis should recommend the appropriate future relationship pattern.
Potential examples:
- nullable foreign key to media.id
- context-specific pivot/reference
Do not create future module schema during F006.
48. Database Relationships
F006 should not create speculative relationships to:
- pages
- services
- projects
- blog
- gallery
- settings
unless explicitly approved as part of F006.
Keep the Media Library foundation independent.
49. Admin Navigation
After implementation, the existing Administration sidebar may include:
Media
or:
Media Library
Exact label should be recommended during Analysis.
Visibility should follow the approved media permission.
Do not add future module links.
50. Routes
Potential conceptual routes:
GET /admin/media
POST /admin/media
GET /admin/media/{media}
DELETE /admin/media/{media}
Optional metadata update route only if title/editing is approved.
Exact routes and names must be finalized during Analysis.
51. Controller
Potential controller:
MediaController
or:
MediaLibraryController
Analysis should recommend one based on current project naming conventions.
Keep controllers thin.
Avoid service/repository abstraction unless file/database coordination becomes sufficiently cohesive to justify a focused service/action.
52. Form Request
Upload validation should use a dedicated Form Request where appropriate.
Potential:
StoreMediaRequest
Deletion authorization may use middleware/controller authorization unless a dedicated request has real value.
Do not place large upload validation logic directly in the controller.
53. Service / Action Layer
Unlike basic CRUD, file upload/delete coordinates physical storage and database metadata.
Analysis should determine whether a focused Action or service is justified.
Potential examples:
StoreMedia
DeleteMedia
However:
do not introduce interfaces, repositories, or service hierarchies merely for architecture ceremony.
Follow Laravel conventions first.
54. Authorization
All Media Library administration routes must require:
- authentication
- admin.access
- approved media permission
Sidebar visibility is not security.
Direct URL/action access must remain server-side protected.
55. CSRF
All mutating forms/actions must use normal Laravel CSRF protection.
This includes:
- upload
- delete
- metadata update if implemented
56. Upload Security
Uploads must be treated as untrusted input.
Controls should include:
- server-side authorization
- server-side validation
- content/MIME validation
- file-size limits
- controlled storage location
- generated physical filenames
- blocked executable/script file types
- no client-controlled storage paths
Do not rely solely on browser accept attributes.
57. Filename Display Security
Original filenames are user-supplied input.
When displayed:
- escape output
- do not render as HTML
- do not inject into raw JavaScript
58. Public Exposure
Public website assets may be publicly accessible.
However:
public accessibility of an uploaded file must not imply upload/manage permission.
Upload/delete operations remain authenticated and authorized.
59. File Download Behavior
If PDFs/documents are supported, Analysis should determine whether:
- public storage URLs are sufficient
- a controlled download endpoint is needed
Preferred direction for public website assets:
public storage URL unless access control is required.
F006 is not a private document-management system.
60. Private Media
Private/protected document storage is out of scope unless separately approved.
Do not mix public website assets with a document-security system.
61. Image Orientation / EXIF
Do not add EXIF processing or orientation correction automatically.
Analysis may identify it as a future enhancement if needed.
62. Duplicate Uploads
F006 does not need content hashing or duplicate detection initially.
Uploading the same file twice may create two media records/files unless Analysis identifies a simple justified prevention strategy.
Do not build a deduplication system prematurely.
63. File Replacement
Replacing the physical file behind an existing media record can affect future usages.
Preferred initial direction:
do not implement file replacement unless clearly needed.
Users can upload a new asset and later update references.
64. Bulk Operations
Do not add bulk delete, bulk tagging, or bulk editing unless explicitly approved.
Initial implementation should prioritize correctness and safety.
65. Audit Logging
F019 — Activity Logs is planned later.
Do not build a separate F006 audit-log subsystem.
F006 actions may become auditable after F019.
66. Database Impact
F006 is expected to add one Media Library table.
Exact schema must be proposed during Analysis.
Do not create migration until implementation is approved.
Do not modify unrelated tables without explicit approval.
67. Indexes
Analysis should recommend only useful indexes.
Potential:
- created timestamp sorting may rely on existing timestamp column
- MIME/type filtering may or may not justify an index
Do not over-index a small CMS table.
68. Foreign Keys
The Media Library foundation itself may not require foreign keys beyond its own primary key.
Do not create speculative foreign keys to future modules.
69. Dependency Impact
Expected:
None
unless image manipulation or another capability genuinely requires a package.
Any new dependency requires explicit approval before installation.
70. Website Settings Dependency Boundary
F005 remains complete.
Do not reopen or modify F005 merely because F006 now exists.
If logo/favicon integration is proposed:
- treat it as an explicit F006 decision
- list exact F005 files/schema changes
- obtain approval before implementation
71. Validation Requirements
Analysis must recommend concrete validation rules for each allowed type.
At minimum consider:
- required upload
- file
- allowed types/MIME
- max size
- optional title
- safe filename handling
Do not trust extension alone.
72. Error Handling
Upload failures should:
- show useful validation/error feedback
- not create invalid DB records
- avoid leaving unnecessary files where practical
Deletion failures should:
- avoid pretending success
- report a meaningful failure
- preserve consistency as much as reasonably possible
Do not expose stack traces or filesystem internals to users.
73. Success Feedback
Admin actions should use existing flash-message conventions.
Potential feedback:
- media uploaded successfully
- media deleted successfully
- media updated successfully if metadata editing exists
Do not add a separate notification framework.
74. Responsive UI
Media Library must work on:
- desktop
- narrow/mobile screens
Expected review should consider:
- grid/list wrapping
- preview sizing
- long filenames
- upload control
- delete action
- pagination
- no horizontal overflow
75. Accessibility
Admin UI should provide:
- labels for upload controls
- meaningful buttons
- visible focus states through existing styles
- non-color-only status/meaning where applicable
Image previews should not use meaningless accessibility text.
76. Testing Strategy
Focused F006 tests should cover meaningful behavior without excessive test multiplication.
Expected categories include:
Authorization
- guest cannot access Media Library
- authenticated unauthorized user receives 403
- Super Admin can access through Gate bypass
- explicitly authorized user can access
Navigation
- Media Library link visible only when authorized
Upload
- valid approved image uploads
- valid approved document uploads if documents are included
- database metadata created
- physical file stored
- generated filename/path safe
- original filename retained appropriately
Validation
- unsupported file type rejected
- oversized file rejected
- executable/script content/type rejected where practical
- SVG rejected if not approved
Persistence
- metadata correct
- no DB record on failed upload
Browsing
- media list renders
- pagination/filtering/search behavior if implemented
Deletion
- authorized deletion removes expected media record/file
- unauthorized deletion denied
- missing media handled correctly
- deletion confirmation exists in UI
Security
- original filenames escaped
- no arbitrary storage path accepted
- no user-controlled physical filename/path traversal
Use Laravel storage fakes in automated tests where appropriate.
Do not depend on real local filesystems for focused tests unless necessary.
77. Storage Testing
Prefer:
Storage::fake(...)
for upload/deletion tests.
Focused tests should verify:
- file stored at expected relative path
- file removed when media is deleted
- test suite does not pollute actual public storage
78. Manual UI Verification
Expected later manual review:
Desktop
- Media navigation visible
- index/grid usable
- upload form clear
- image preview works
- document representation works if supported
- metadata readable
- delete confirmation clear
- pagination/search/filter usable if implemented
Mobile
- grid/list adapts
- preview cards fit
- long filenames do not break layout
- upload button usable
- delete action accessible
- no horizontal overflow
79. Existing Known Issues
Known unrelated project issues remain outside F006.
Pint
- app/View/Components/FrontendLayout.php
- routes/frontend.php
Composer Advisories
Previously documented Composer security advisories remain unresolved.
Do not remediate these within F006 unless separately approved.
80. Git / GitHub
Follow:
AGENTS.md
Local read-only Git inspection may be used.
Do not:
- access GitHub
- fetch
- pull
- push
- clone
- commit automatically
- create branches automatically
- use GitHub APIs/connectors
The user handles Git/GitHub manually.
81. Analysis Questions
F006 Analysis must explicitly answer:
1. Should F006 use Laravel-native storage or a media package?
2. What should the model be named?
3. What exact metadata columns are required?
4. Which file types should be allowed initially?
5. Should PDF/document uploads be included?
6. Should SVG remain prohibited?
7. What upload size limits should apply?
8. Should width/height be stored for images?
9. Is image processing or thumbnail generation necessary now?
10. Should upload support one or multiple files per request?
11. Is drag-and-drop worth implementing?
12. Should the library use grid, list, or both?
13. What search/filter/sort capabilities are justified?
14. Is folder/category organization needed now?
15. What pagination size should be used?
16. Should title be editable?
17. Should alt_text exist at library level?
18. What safe physical filename strategy should be used?
19. What relative directory structure should be used?
20. Should F006 use the Laravel public disk?
21. Is storage:link currently required?
22. What should deletion behavior be?
23. Are soft deletes necessary?
24. Is a focused upload/delete Action or service justified?
25. What exact media permission(s) should exist?
26. Should Admin receive media permission by default?
27. What routes/controller/request classes are appropriate?
28. Should F006 provide a reusable media picker now?
29. Should F006 integrate logo/favicon selection into Website Settings now or defer it?
30. What focused tests are required?
31. Is any new package required?
82. Expected Direction
Unless Analysis identifies a compelling reason otherwise, preferred direction:
- Laravel-native filesystem
- public disk
- dedicated relative media/... storage path
- structured Media model/table
- generated unique stored filenames
- retain original filename as metadata
- images plus PDF only initially
- no SVG
- no executable/script file types
- server-side MIME/type/size validation
- optional image width/height metadata
- no image editing
- no automatic transformations
- no new package
- simple responsive grid
- basic search/type filter if useful
- server-side pagination
- no folders initially
- no soft deletes initially
- one media.manage permission unless analysis justifies more
- default Admin permission unchanged unless explicitly approved
- safe permanent deletion with confirmation while no references exist
- no generic DAM architecture
- no frontend-theme integration
- media picker only if needed now
- F005 logo/favicon integration only if explicitly approved
83. Decisions Requiring User Approval
Before implementation, the user should approve:
- native Laravel storage vs package
- media schema
- allowed file types
- upload-size limits
- image metadata strategy
- stored filename/path strategy
- permission model
- Admin default permission decision
- upload UX
- organization/search/filter strategy
- delete behavior
- whether a focused service/action is needed
- reusable media picker scope
- Website Settings logo/favicon integration decision
- any new package
84. Implementation Plan
Status:
Pending Analysis
Expected implementation plan size:
approximately 6–8 meaningful steps.
Do not implement until approved.
85. Implementation Record
Files Created
Pending.
Files Modified
Pending.
Files Removed
Pending.
Migration
Pending.
Model
Pending.
Storage
Pending.
Permission
Pending.
Dependency Changes
Expected:
None
Final:
Pending.
Tests Added / Updated
Pending.
86. Validation Record
Focused Tests
Pending.
Regression Suite
Pending.
Laravel Pint
Pending.
Frontend Build
Pending.
Migration Review
Pending.
Storage Review
Pending.
Authorization Review
Pending.
Upload Security Review
Pending.
Manual UI Review
Pending.
Final Diff Review
Pending.
87. Known Limitations
Expected initial limitations may include:
- no advanced image editing
- no automatic transformations
- no external cloud/CDN storage
- no folders/tags unless analysis justifies them
- no media usage/reference tracking until consuming modules exist
- no private media/document-management system
- no activity logging until F019
- logo/favicon selection may remain deferred unless explicitly included in F006
Final limitations:
Pending implementation.
88. Completion Record
Implementation:
Pending
Acceptance Criteria:
Pending
Focused Testing:
Pending
User Code Review:
Pending
Manual UI Review:
Pending
Final Validation:
Pending
Final User Approval:
Pending
Feature Status:
Planned
89. Acceptance Criteria
Persistence
- [ ] Approved Media model/table is implemented.
- [ ] Physical files and database metadata remain coordinated.
- [ ] Relative storage paths are persisted instead of environment-specific absolute URLs.
- [ ] Original filenames are retained safely where approved.
- [ ] Stored filenames are generated safely.
Authorization
- [ ] Media administration requires authentication.
- [ ] Media routes require approved authorization.
- [ ] Unauthorized authenticated users receive 403.
- [ ] Super Admin access continues through centralized Gate bypass.
- [ ] Media sidebar visibility follows authorization.
Upload
- [ ] Approved media types upload successfully.
- [ ] Unsupported media types are rejected.
- [ ] File-size limits are enforced.
- [ ] Server-side file validation is used.
- [ ] User-controlled storage paths are not accepted.
- [ ] Client-provided filenames are not blindly used as physical storage filenames.
- [ ] Failed uploads do not create invalid database records.
Security
- [ ] Executable/script uploads are prohibited.
- [ ] SVG is prohibited unless explicitly approved.
- [ ] Original filenames are escaped when displayed.
- [ ] No arbitrary HTML/script execution is introduced.
- [ ] CSRF protection applies to mutating actions.
- [ ] No infrastructure secrets are exposed.
- [ ] Public media access does not bypass CMS upload/delete authorization.
Browsing
- [ ] Authorized users can browse uploaded media.
- [ ] Media previews are usable.
- [ ] Non-image files have an appropriate representation if supported.
- [ ] Pagination exists when needed.
- [ ] Approved search/filter behavior works.
- [ ] UI remains responsive.
Deletion
- [ ] Authorized users can delete media according to the approved strategy.
- [ ] Deletion has clear confirmation.
- [ ] Database and physical-file cleanup are coordinated.
- [ ] Unauthorized users cannot delete media.
- [ ] Future reference-safety considerations are documented.
Architecture
- [ ] Laravel filesystem abstraction is used.
- [ ] No absolute environment-specific storage URLs are persisted.
- [ ] No unnecessary repository layer is introduced.
- [ ] No generic DAM platform is introduced.
- [ ] No media package is installed without approval.
- [ ] No speculative future-module foreign keys are added.
- [ ] F006 remains business-neutral.
Scope Boundaries
- [ ] No advanced image editor is introduced.
- [ ] No automatic video/audio transcoding is introduced.
- [ ] No private document-management system is introduced.
- [ ] No AI media functionality is introduced.
- [ ] No F007+ content modules are implemented prematurely.
- [ ] Website Settings logo/favicon integration occurs only if explicitly approved.
Quality
- [ ] Focused F006 tests pass.
- [ ] Storage tests use safe/fake test storage where appropriate.
- [ ] F006-modified PHP files pass Pint.
- [ ] Frontend build passes when applicable.
- [ ] Manual UI review passes.
- [ ] Full regression suite passes during final validation.
- [ ] Final diff remains scoped to F006.
90. Required Workflow
F006 must follow:
Feature Specification
→ USER AUTHORIZES ANALYSIS
→ Targeted Analysis
→ Architecture / Security / Storage Recommendations
→ USER APPROVES SIGNIFICANT DECISIONS
→ Implementation
→ Focused Tests
→ USER CODE/UI REVIEW
→ Corrections if required
→ USER APPROVES FINAL VALIDATION
→ Final Validation
→ USER FINAL APPROVAL
→ Mark F006 Complete
→ Identify F007 only
→ ASK BEFORE F007 ANALYSIS
Do not automatically proceed to F007.
91. Low-Cost Requirement
Follow:
docs/08_COST_CONTROL.md
For F006:
- inspect only storage/RBAC/admin/media-relevant files
- avoid researching packages unless native Laravel storage proves insufficient
- do not inspect future content modules
- avoid DAM over-engineering
- avoid image-processing libraries without demonstrated need
- avoid cloud-storage research unless required
- run focused tests first
- use Storage::fake() for automated storage tests
- perform full regression only after user review
- keep reports concise
- preserve explicit approval gates
92. F006 Completion Rule
F006 may be considered ready for final approval only when:
- approved media architecture is implemented
- approved upload types work
- unsafe uploads are rejected
- storage metadata is correct
- files are stored safely
- browsing is usable
- deletion behaves according to approved rules
- authorization is correct
- Media Library navigation is permission-aware
- no generic DAM overreach exists
- no unnecessary dependency is introduced
- F003 shell remains intact
- F002 authorization remains intact
- migration is safe
- focused tests pass
- manual UI review passes
- final regression validation passes
- final diff remains F006-only
Only explicit user approval may change:
Status:
from:
Ready for Final Approval
to:
Complete