F006 — Media Library
Status:
Complete

Final approval — 2026-10-17
User granted final approval, including the final presentation corrections. F006 is Complete. Historical review-stage entries below are retained as development history; this approval and the completion record supersede their pending statuses. Documented limitations and known unrelated Pint findings/Composer advisories remain unchanged. No validation commands, application/data/storage changes or Git operations were performed for this completion update.

Display Name action styling — 2026-10-16
- Added generous top/side/bottom spacing around the editor actions, a dark primary Save button and a muted secondary Cancel button. Cancel discards the unsaved draft/errors and closes the section without a request; both actions are disabled while saving. Status remains Ready for Review pending user verification.

Additional card-spacing correction — 2026-10-16
- Reopened user review after final validation for a presentation-only refinement: a compact minimum metadata height keeps untitled/PDF cards aligned with titled image cards, and a reserved success-feedback line avoids a height jump after saving. Metadata can still grow for long names.
- Display Name Save now has a separated action row with deliberate top margin/padding and wider button padding. Backend, asynchronous behavior, previews and storage are unchanged.
- Previous final-validation results remain recorded below; this correction requires renewed user UI approval. Feature is not Complete.

Display Name completion correction — 2026-10-16
- The existing asynchronous editor now collapses its native details section only after a successful response updates the heading/draft and success feedback. This also applies when clearing the title. Error paths retain the open editor and entered value. No navigation, explicit scrolling, backend, storage or dependency changes.
- JavaScript assets rebuilt for manual review. No backend/view change required; focused PHP tests were not rerun. Status remains Ready for Review; user verification pending.

Inline Display Name save correction — 2026-10-16
- Display Name uses the existing Alpine installation plus native fetch to submit PATCH admin.media.update with JSON content negotiation and the form's existing CSRF token. No new endpoint or dependency was added.
- The existing UpdateMediaRequest, media.manage checks and title-only persistence remain authoritative. JSON success returns title and display_name; ordinary form submissions retain the existing redirect. Laravel returns normal JSON validation errors for asynchronous requests.
- Successful saves update the card heading and editor value using text binding, without navigation/reload or explicit scrolling. Clearing title shows original_name. The editor remains open, with inline success feedback. Validation, authorization/session and unexpected failures retain the draft and show safe inline errors; duplicate saves are disabled while pending.
- Disk/path/public URL, immutable original_name, preview/upload/search/filter/delete behavior and schema remain unchanged.
- Focused command: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php — 19 passed, 311 assertions. Existing update tests retained; JSON success/clear/error/authorization/storage-identity and escaped rendering coverage added. Browser interaction remains for user manual review; no browser E2E infrastructure introduced.
- npm run build compiled the JavaScript correction successfully for review. No full regression or final validation performed. Status remains Ready for Review.

Preview-height review correction — 2026-10-16
- All image and PDF cards now share one fixed 12rem (192px) preview wrapper, independent of file aspect ratio. Images fill that region using object-contain; PDF content remains centered. Explicit presentation sizing avoids relying on a newly compiled utility class.
- Cards align to the start of each grid row rather than stretching shorter cards with excess blank space. Metadata, controls, runtime URLs and processing behavior remain unchanged.
- Focused command: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php — 18 passed, 288 assertions. No other tests, build or final validation run for this correction; hard-refresh/manual grid review remains pending.

Preview URL diagnosis/correction — 2026-10-16
- Existing JPEG id 1 uses disk public and relative path media/2026/10/4XiutJ2sxP4NIzuxBpinKQBWL244b0FSciITf4f2.jpg; the physical file exists. After storage-link setup, its generated http://localhost/storage/... URL returned 404, while the identical path on the running Laravel origin http://127.0.0.1:8000 returned 200 image/jpeg. Open asset therefore also targeted the wrong server.
- Media::url() now returns the path component of Laravel Storage's generated URL for the local public disk, so both image previews and Open asset use /storage/... on the active origin/port. Other disks retain their generated URL behavior. No .env/filesystem configuration, stored path, file or schema changes.
- Existing approved MIME branches, constrained preview region/fallback, PDF representation, title behavior and upload behavior are unchanged.
- Focused command: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php — 18 passed, 285 assertions. Includes origin mismatch regression coverage and existing preview/title/escaping tests.
- Status remains Ready for Review. Hard-refresh/manual review is pending; no full regression or final validation performed.

Image-preview review correction — 2026-10-16
- Approved JPEG/PNG/WebP cards render the original runtime Media URL inside a fixed-height, overflow-constrained region using object-contain and lazy loading. Existing Alpine handles a simple image-load failure message without changing card height.
- PDF cards use the same-height neutral region with an inline document icon and PDF label; no PDF image/thumbnail is generated.
- Display Name editing, stored paths/URLs, metadata, upload limits and responsive grid remain unchanged. No processing package or alternate asset version was added.
- Focused command: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php — 17 passed, 279 assertions. Coverage includes runtime preview URLs for all approved image MIME types, PDF exclusion, escaped names, and unchanged preview URLs after title changes.
- Tailwind assets compiled for review. public/storage was inspected and remains absent; this prevents normal local public-disk serving until separately approved setup. No storage link was created. Manual UI review and final validation remain pending; status remains Ready for Review.

Approved review corrections — 2026-10-16 (supersedes the initial decisions below)
- Status remains Ready for Review; user manual UI approval and final validation are pending.
- Standard multiple-file upload accepts 1–10 files per request. The complete array is validated before processing, retaining independent image (5 MiB) and PDF (10 MiB) limits and all existing format/security rules. PHP/web-server total request limits still apply; configuration is unchanged.
- The controller invokes StoreMedia for each validated file. A later operational failure stops processing, reports the number already uploaded safely, and retains earlier successful files. The failing operation retains its cleanup guarantees.
- Added nullable string title (maximum 255) through 2026_10_16_000002_add_title_to_media_table; the previously executed create migration is unchanged. The focused follow-up migration was applied locally.
- PATCH admin.media.update uses UpdateMediaRequest and the existing media.manage authorization to change title only. Original upload name, generated physical filename, disk/path, public URL and other storage metadata are unchanged by display-name updates.
- Cards display title when present, otherwise original_name; original filename remains secondary metadata when a title exists. Compact native details/forms allow editing or clearing Display name. Untrusted titles are validated and escaped, without HTML rewriting.
- Search now matches title OR original_name in a grouped parameterized query, retaining type filtering and pagination.
- UI correction: matching primary Upload/Apply buttons, outlined Open asset, low-emphasis Reset, destructive Delete, styled multiple-file chooser, and an aligned responsive search/filter toolbar. Existing deletion confirmation remains.
- Focused tests: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php tests/Feature/Rbac — 32 passed, 311 assertions, using fake storage/in-memory test data. Includes mixed batches, 10-file bound, all-before-storage validation, partial operational failure, immutable storage metadata, title validation/escaping/search and existing authorization/deletion/cleanup coverage.
- Scoped PHP formatting applied. npm run build compiled the corrected Tailwind assets successfully for manual review; no full regression or final validation performed. Manual UI review has not been claimed as passed.
- No new dependency, permission, default Admin grant, Website Settings integration, media picker or future-module work. No storage-link command was run during these corrections.

Initial implementation record — 2026-10-16
- Approved implementation uses Laravel-native public-disk storage and Media/table media; no dependency changes.
- Schema: id, original_name, disk, path, mime_type, size, nullable width/height, timestamps; ordering index on created_at/id.
- Uploads: one JPEG/PNG/WebP (5 MiB maximum) or PDF (10 MiB maximum) per request. GIF, SVG, scripts and other formats are rejected. Content-aware validation, extension agreement and native image dimensions are enforced.
- Storage: media/YYYY/MM/{hashName}; server-owned metadata; runtime Storage URL generation; no persisted absolute URLs.
- StoreMedia and DeleteMedia coordinate storage/persistence, surface safe failures and report operational errors. Failed persistence attempts upload cleanup. Failed file deletion retains the record; an already-missing file permits metadata cleanup. Database deletion failure after file removal leaves a record that can be retried.
- Routes: admin.media.index (GET), admin.media.store (POST), admin.media.destroy (DELETE), requiring auth/admin.access/media.manage. Central Super Admin Gate bypass is unchanged.
- media.manage registered through the existing additive seeder. Admin default permissions and the seeder implementation remain unchanged.
- Responsive index provides upload, original-image previews, PDF cards, metadata, public links, explicit delete confirmation, original-name search, All/Images/Documents filtering, newest-first deterministic ordering and 24-item server pagination.
- No title/alt_text, transformations, folders, soft deletes, picker, Website Settings changes or future-module relationships.
- Focused command: php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php tests/Feature/Rbac — 27 passed, 218 assertions. Initial sandbox execution was blocked by PHP file readability; successful focused execution used approved escalation. One MIME-mismatch fake fixture was corrected before the successful run; a real UploadedFile spoofing test also passes.
- Scoped Pint formatting applied only to F006 PHP files. No full regression, frontend build, manual UI review or final validation performed.
- Approved local create_media_table migration and additive RolesAndPermissionsSeeder executed successfully.
- public/storage remains absent; storage:link was not run. Public-preview/manual review requires separate approval/setup.
- Limitations: public assets only; no malware/PDF sanitization pipeline. No filesystem/database atomic transaction; cleanup is best effort. Original-image previews download original files. Later consuming features must add reference protection before referenced media can safely be deleted, normally using an appropriately named nullable foreign key to media.id for a single asset.
- Known unrelated Pint findings and Composer advisories remain outside this implementation.
- Pending: user code review, separately approved storage-link setup/manual UI review, final validation, and final completion approval. F006 is not Complete.

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
Approved and implemented, including the separately approved multi-file upload, Display Name, preview and asynchronous-save refinements recorded above.
85. Implementation Record
Files Created
- app/Models/Media.php
- app/Actions/Media/StoreMedia.php
- app/Actions/Media/DeleteMedia.php
- app/Http/Controllers/Admin/MediaController.php
- app/Http/Requests/Admin/StoreMediaRequest.php
- app/Http/Requests/Admin/UpdateMediaRequest.php
- database/migrations/2026_10_16_000001_create_media_table.php
- database/migrations/2026_10_16_000002_add_title_to_media_table.php
- resources/views/admin/media/index.blade.php
- resources/js/media-display-name.js
- tests/Feature/Admin/MediaLibraryTest.php
Files Modified
- app/Support/Rbac.php
- routes/admin.php
- resources/views/admin/layouts/partials/sidebar.blade.php
- resources/js/app.js
- tests/Feature/Rbac/AdminAccessTest.php
- tests/Feature/Admin/DashboardTest.php
- docs/features/F006_MEDIA_LIBRARY.md
Files Removed
None.
Migration / Model
Media/table media; initial create migration preserved, nullable title added through a separate follow-up. Both applied locally before final validation. Read-only schema verification confirmed id, original_name, title, disk, path, mime_type, size, nullable width/height and timestamps only; primary key plus created_at/id index; no foreign keys.
Storage
Laravel public disk, server-generated media/YYYY/MM/hashName paths, runtime URLs. Local public URLs are root-relative to retain the active serving origin/port; non-local disks retain Storage URL generation. Standard php artisan storage:link created public/storage pointing to storage/app/public. No configuration changes or custom link workaround.
Permission
media.manage only; admin.access also required. Admin defaults and additive seeder implementation unchanged; Super Admin Gate bypass retained.
Dependency Changes
None. Composer/npm manifests and lockfiles unchanged.
Tests Added / Updated
MediaLibraryTest covers authorization/navigation, types/limits/batches, storage metadata/dimensions/security, browsing, Display Name/JSON update/escaping, deletion and cleanup failures. AdminAccessTest covers permission defaults/idempotency/custom grants. DashboardTest's stale fixed count was replaced by the configured permission count plus its one custom web-permission fixture; dashboard application code unchanged.
86. Validation Record — 2026-10-16
Full Regression Suite
php artisan test — 92 passed, 556 assertions. Initial run: 1 failed/91 passed because the dashboard test still expected the old permission count. Corrected the expectation only; affected tests and complete suite then passed.
Focused Tests
php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php tests/Feature/Rbac — 35 passed, 355 assertions.
Affected Tests After Correction
php artisan test --compact tests/Feature/Admin/MediaLibraryTest.php tests/Feature/Rbac tests/Feature/Admin/DashboardTest.php — 46 passed, 409 assertions.
Laravel Pint
Project-wide php vendor/bin/pint --test reports only known unrelated findings in app/View/Components/FrontendLayout.php and routes/frontend.php. All 13 F006-created/modified PHP files pass explicit scoped --test, including both migrations and the dashboard test. Known unrelated files left unchanged.
Frontend Build
npm.cmd run build — passed. No dependency update. Generated public/build assets remain ignored/local.
Routes
Exactly index/store/update/destroy under /admin/media, named admin.media.*, each protected by web/auth/can:admin.access/can:media.manage; verified with filtered route:list -vv.
Migration / Database Review
Passed by source review, prior forward execution, isolated test migrations and read-only local schema inspection. No destructive migration or data edits performed during final validation. Rollback definitions reviewed; rollback was not executed against the development database.
Storage / Security Review
Passed: approved MIME/extension agreement and native image validation/dimensions; 1–10 files fully validated before normal processing; individual 5 MiB image/10 MiB PDF limits; generated destination and trusted metadata; request cannot select persistence attributes; escaped Blade/x-text output; CSRF and server authorization preserved. No SVG/GIF/executable/HTML/JS or other arbitrary formats allowed. File storage/persistence coordination remains in focused Actions; failure feedback is safe and cleanup failures are reported.
Display Name / JavaScript Review
Passed: title-only updates through existing PATCH/Form Request; original_name/path/disk/URL unchanged. JSON content negotiation retains ordinary form redirects. Alpine/native fetch uses existing CSRF token and text binding, updates heading, collapses only after success, and keeps errors/drafts inline on failure. No added frontend dependency or broader AJAX operations.
Browsing / Preview / Deletion Review
Passed: title/original-name grouped parameterized search, All/Images/Documents filters, created_at DESC/id DESC and 24-item pagination with filter preservation. Original-image previews and PDF representation share fixed 12rem region; no transformation files. Authorized permanent deletion uses stored disk/path, retains metadata on file-removal failure, allows already-missing-file cleanup and reports DB failure after removal; explicit confirmation retained.
RBAC / Scope Review
Passed: media.manage registration remains additive/idempotent; customized Admin grants survive; no default media grant or explicit Super Admin checks. Permission-aware navigation extends F003 only. F004 application and F005 Website Settings unchanged. No picker, logo/favicon integration, future-module relationships, folders/tags, cloud/private storage, transformations or F007 work.
Manual UI Review
Passed — confirmed by user
Desktop/mobile/narrow layout, overflow, responsive grid, image previews/PDF representation, consistent preview heights, public serving/Open asset, multiple upload, search/filter, Display Name persistence/clearing, no-refresh save/auto-collapse, immutable physical identity and deletion UI/confirmation all confirmed by user. Browser validation was not repeated during final validation.
Final Diff Review
Passed: all tracked/untracked feature files reviewed; no unrelated changes, debug code, temporary routes, secrets or unexplained TODOs found. No test media/build artifacts added to the change set. Initial user-prepared F006 specification preserved and updated only for the approved workflow. Local media/link/build output are ignored; no remote/Git mutations performed.
87. Known Limitations
- Filesystem/database operations are not atomic; cleanup is best effort. A later batch failure retains earlier successful uploads; a DB deletion failure after file removal can leave metadata for retry.
- Future consuming modules must add reference protection before referenced assets can safely be deleted.
- Original-image previews only; no thumbnails or transformations.
- Media picker and Website Settings logo/favicon integration remain deferred.
- Public assets only; no private-document management, malware scanning or PDF sanitization pipeline.
- PHP/web-server overall request limits still apply to batches; configuration unchanged.
- Known unrelated project-wide Pint findings and previously documented Composer advisories remain outside F006.
88. Completion Record
Implementation: Complete
Acceptance Criteria: 55/55 satisfied
Focused Testing: Passed
User Code Review: approved by user.
User Review: Approved
Manual UI Review: Passed — confirmed by user
Final Validation: Passed
Project-wide Pint retains only the two documented unrelated findings; previously documented Composer advisories remain outside F006.
Final User Approval: Approved
Feature Status: Complete
89. Acceptance Criteria
Persistence
- [x] Approved Media model/table is implemented.
- [x] Physical files and database metadata remain coordinated.
- [x] Relative storage paths are persisted instead of environment-specific absolute URLs.
- [x] Original filenames are retained safely where approved.
- [x] Stored filenames are generated safely.
Authorization
- [x] Media administration requires authentication.
- [x] Media routes require approved authorization.
- [x] Unauthorized authenticated users receive 403.
- [x] Super Admin access continues through centralized Gate bypass.
- [x] Media sidebar visibility follows authorization.
Upload
- [x] Approved media types upload successfully.
- [x] Unsupported media types are rejected.
- [x] File-size limits are enforced.
- [x] Server-side file validation is used.
- [x] User-controlled storage paths are not accepted.
- [x] Client-provided filenames are not blindly used as physical storage filenames.
- [x] Failed uploads do not create invalid database records.
Security
- [x] Executable/script uploads are prohibited.
- [x] SVG is prohibited unless explicitly approved.
- [x] Original filenames are escaped when displayed.
- [x] No arbitrary HTML/script execution is introduced.
- [x] CSRF protection applies to mutating actions.
- [x] No infrastructure secrets are exposed.
- [x] Public media access does not bypass CMS upload/delete authorization.
Browsing
- [x] Authorized users can browse uploaded media.
- [x] Media previews are usable.
- [x] Non-image files have an appropriate representation if supported.
- [x] Pagination exists when needed.
- [x] Approved search/filter behavior works.
- [x] UI remains responsive.
Deletion
- [x] Authorized users can delete media according to the approved strategy.
- [x] Deletion has clear confirmation.
- [x] Database and physical-file cleanup are coordinated.
- [x] Unauthorized users cannot delete media.
- [x] Future reference-safety considerations are documented.
Architecture
- [x] Laravel filesystem abstraction is used.
- [x] No absolute environment-specific storage URLs are persisted.
- [x] No unnecessary repository layer is introduced.
- [x] No generic DAM platform is introduced.
- [x] No media package is installed without approval.
- [x] No speculative future-module foreign keys are added.
- [x] F006 remains business-neutral.
Scope Boundaries
- [x] No advanced image editor is introduced.
- [x] No automatic video/audio transcoding is introduced.
- [x] No private document-management system is introduced.
- [x] No AI media functionality is introduced.
- [x] No F007+ content modules are implemented prematurely.
- [x] Website Settings logo/favicon integration occurs only if explicitly approved.
Quality
- [x] Focused F006 tests pass.
- [x] Storage tests use safe/fake test storage where appropriate.
- [x] F006-modified PHP files pass Pint.
- [x] Frontend build passes when applicable.
- [x] Manual UI review passes.
- [x] Full regression suite passes during final validation.
- [x] Final diff remains scoped to F006.
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
