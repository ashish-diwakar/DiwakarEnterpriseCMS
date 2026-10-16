# F008 — Page Sections

**Feature Status:** `Planned`

## 1. Objective

Introduce reusable, structured content sections within CMS-managed Pages.

Authorized administrators should be able to add, edit, arrange, enable/disable, and remove approved section types without hard-coding business-specific page content into frontend templates.

F008 should establish a maintainable content architecture for future frontend themes without becoming a general-purpose visual page builder.

---

## 2. Background

The following foundation is complete:

- F001 — Authentication Integration
- F002 — Roles, Permissions and Super Admin
- F003 — Admin Application Shell
- F004 — Admin Dashboard
- F005 — Website Settings
- F006 — Media Library
- F007 — Page Management

F007 introduced the `Page` model with:

- title
- slug
- Draft/Published status
- creation/update timestamps

F007 intentionally deferred actual Page content to F008.

F006 introduced reusable media records, but deferred the Media Picker until an approved consuming feature required one.

F008 must evaluate how these two completed capabilities should work together.

---

## 3. Core Responsibility

F008 manages the structured content within a Page.

The intended separation is:

**F007 — Page Management**

Page identity and publication lifecycle.

**F008 — Page Sections**

Content, section configuration, and ordering.

**F009 — Menu Management**

Navigation structure and page links.

**F010 — SEO Management**

Search metadata and related SEO behavior.

**F020/F021 — Theme Architecture / Frontend Theme**

Public website presentation and theme-specific rendering.

Do not merge these responsibilities prematurely.

---

## 4. Core Principles

Page Sections should be:

- reusable
- business-neutral
- structured
- type-safe where practical
- securely validated
- manageable without technical knowledge
- compatible with Laravel conventions
- compatible with future themes
- simple to extend with additional approved section types

Favor explicit section schemas and templates over arbitrary page-builder configuration.

---

## 5. In Scope

F008 should evaluate the minimum functionality needed to manage Page Sections.

Potential capabilities:

- display sections belonging to a Page
- add an approved section type
- edit section content
- change section order
- enable/disable a section if justified
- delete a section
- associate approved media where appropriate
- validate type-specific section content
- preserve the relationship to the parent Page
- provide permission-aware administration
- provide responsive forms and controls

Exact capabilities require analysis and user approval.

---

## 6. Out of Scope

Do not automatically implement:

- Elementor-style page builder
- drag-and-drop visual designer
- arbitrary HTML editor
- arbitrary CSS editor
- unrestricted custom JavaScript
- page-template designer
- section-template marketplace
- visual grid/layout designer
- nested unlimited sections
- reusable global content blocks
- content revisions/version history
- scheduled section publishing
- multilingual content
- AI content generation
- theme styling configuration
- complete frontend page rendering
- SEO metadata
- menu assignment
- activity logging
- content approval workflows
- arbitrary administrator-defined section schemas

These require separate features or explicit approval.

---

## 7. Relationship to Page

Every Page Section must belong to exactly one existing Page.

Expected relationship:

`Page hasMany PageSection`

`PageSection belongsTo Page`

A section must not exist without an owning Page.

Analysis must determine the appropriate foreign-key behavior and deletion safeguards.

Standard Eloquent relationships should be preferred.

---

## 8. Proposed Model Naming

Preferred:

`PageSection`

Preferred table:

`page_sections`

Analysis should verify naming consistency with the existing project.

Avoid generic names such as `Section` if they could become ambiguous across future CMS modules.

---

## 9. Section Type Architecture

Each Page Section should have an approved type.

Potential examples:

- Hero
- Text
- Image with Text
- Call to Action
- Image Gallery

However, F008 must not implement every conceivable section type.

Analysis must select the smallest useful initial set.

Each type should have a clear purpose, field schema, validation rules, and eventual rendering behavior.

Do not allow administrators to create arbitrary section types.

---

## 10. Initial Section Types

Evaluate the following candidates during Analysis.

### Hero

Potential fields:

- heading
- subheading
- background or featured image
- button text
- button destination

### Text Content

Potential fields:

- heading
- text/content

### Image with Text

Potential fields:

- heading
- text/content
- selected image
- image position

### Call to Action

Potential fields:

- heading
- description
- button label
- button URL

### Gallery

Potential fields:

- heading
- multiple selected images

Gallery may be deferred to F016 — Gallery unless a basic reusable section clearly adds immediate value.

### Preferred Direction

Begin with approximately **2–3 useful section types**.

Analysis must determine the exact list and justify it.

Do not build five or more section types automatically.

---

## 11. Section Type Registry

Analysis should compare:

A. Explicit enum/configuration-based approved types.

B. Database-defined section types.

C. Dynamically registered section components.

Preferred initial direction:

a fixed application-defined list of approved section types.

Possible implementation:

`PageSectionType`

as a string-backed enum or another small Laravel-native mechanism.

Do not introduce a plugin-style section registry without demonstrated need.

---

## 12. Section Data Storage

This is a significant architectural decision.

Analysis must evaluate:

### Option A — Explicit Columns

Store section content in typed columns such as:

- heading
- description
- media_id
- button_label
- button_url

Advantages:

- explicit structure
- predictable queries
- straightforward validation
- database-level relationships for media

Disadvantages:

- potentially many nullable columns
- different types may require different combinations

### Option B — Controlled JSON Content

Use a column such as:

`content`

or:

`data`

containing application-defined fields for each approved section type.

Advantages:

- flexible type-specific content
- fewer schema changes for new types
- suitable for known structured section definitions

Disadvantages:

- weaker database-level structure
- more complex validation
- more difficult relational media reference protection
- potential for uncontrolled arbitrary data

### Option C — Separate Tables per Type

Different tables/models for each section type.

Potentially stronger typing but more complexity.

### Required Decision

Recommend the simplest maintainable model for this CMS.

Do not choose JSON merely to avoid migrations.

Do not create a general schema engine.

If JSON is recommended, define strict type-specific validation and persistence boundaries.

Media references and future deletion safety must be considered explicitly.

---

## 13. Proposed Common Fields

Potential shared section attributes:

- `id`
- `page_id`
- `type`
- `sort_order`
- `is_enabled`
- approved content attributes
- timestamps

Analysis must determine the exact schema.

Do not add optional columns without a concrete initial requirement.

---

## 14. Section Ordering

Administrators must be able to control the order in which Page Sections appear.

Analysis should recommend:

- integer ordering
- a stable secondary ordering rule
- safe reorder operations
- handling of new/deleted sections
- ordering within one Page only

Potential field:

`sort_order`

Do not rely on record ID as the permanent display order.

---

## 15. Reordering UI

Evaluate:

A. Move Up / Move Down controls.

B. Drag-and-drop ordering.

Preferred low-complexity direction:

Move Up / Move Down initially.

Drag-and-drop may improve usability, but it should not require a JavaScript library or complex synchronization system unless justified.

The user must approve the final approach.

---

## 16. Reordering Integrity

Reordering must not affect sections belonging to another Page.

Consider concurrent updates where relevant, but do not create a generalized concurrency framework.

Tests should confirm that section ordering remains deterministic after create, reorder, and delete operations.

---

## 17. Section Visibility

Analysis should determine whether F008 needs:

`is_enabled`

Potential behavior:

Enabled sections are eligible for future frontend rendering.

Disabled sections remain stored but should not appear publicly.

Page publication status remains independent.

A Published Page may contain enabled or disabled sections.

Do not create a complex independent section publishing workflow.

---

## 18. Page Publication Relationship

F007 already supports:

`draft`

`published`

F008 should not duplicate the Page lifecycle.

Future public rendering should generally require:

- parent Page is Published
- section is enabled, if section-level visibility is approved

Do not introduce scheduled section publishing.

---

## 19. Section Content Validation

Each approved section type must have explicit validation rules.

For example:

Hero may require a heading.

Call to Action may require a button label and destination together.

Image with Text may require a selected image when that type's contract requires one.

Do not accept arbitrary unvalidated JSON or request fields.

Do not persist unrestricted:

`$request->all()`

---

## 20. Plain Text vs Rich Text

Analysis must determine whether section text should support:

- plain text
- limited formatted text
- Markdown
- sanitized HTML

Preferred initial direction:

use simple text fields where practical.

Do not introduce a WYSIWYG or HTML sanitizer package automatically.

If rich-text formatting is genuinely required, define its security and rendering contract before implementation.

---

## 21. XSS and Content Safety

Section content is administrator-controlled but must still be treated as untrusted input.

Requirements:

- server-side validation
- escaped HTML output by default
- no unsafe HTML injection
- no arbitrary JavaScript
- no executable content fields
- safe URL handling
- safe media references

Do not render unvalidated stored content through raw Blade output.

If limited HTML is approved, sanitization must be explicitly designed and approved.

---

## 22. Media Library Integration

F006 provides:

`Media`

records and publicly accessible asset URLs.

F008 may become the first consuming feature that needs to select Media.

Analysis must determine whether the initial section types genuinely require images.

If so, the implementation should reuse F006 rather than introduce a separate upload system.

Do not duplicate Media Library storage or upload logic.

---

## 23. Reusable Media Picker

Evaluate whether F008 should introduce a minimal reusable Media Picker.

Potential functionality:

- browse existing media
- select one approved image
- return the selected media reference
- show the selected image in the section form

Preferred direction:

implement only the minimum picker functionality required by approved F008 section types.

Do not create a complex universal asset-management modal.

Do not implement bulk media selection unless an approved section type needs it.

---

## 24. Media Reference Storage

If sections reference Media, analysis must determine how references are persisted.

Potential approach:

a nullable foreign key such as:

`media_id`

pointing to:

`media.id`

Alternatively, a separately justified structure for multiple media references.

Prefer explicit relational references for simple single-image sections.

Do not store arbitrary public media URLs when a reusable Media record already exists.

---

## 25. Media Deletion Safety

F006 currently permits permanent deletion because earlier CMS modules did not reference Media.

Once Page Sections use Media records, deleting referenced Media could break content.

**F008 must not introduce Media references without an approved reference-protection strategy.**

Potential strategy:

- prevent Media deletion while referenced by Page Sections
- display useful feedback explaining why deletion was blocked
- allow deletion after references are removed

Avoid silent cascading destruction of Page Section content.

Do not implement a generic CMS-wide usage-tracking engine.

---

## 26. F006 Modification Boundary

If F008 requires changes to F006 Media deletion behavior:

- identify exact affected files
- explain the specific reference-safety requirement
- obtain approval as part of F008 implementation scope
- preserve all unrelated F006 behavior
- add focused regression tests

F006 remains a completed feature.

Do not reopen or redesign it unnecessarily.

---

## 27. Page Deletion Safety

F007 currently supports permanent Page deletion.

After F008, Pages may own multiple sections.

Analysis must decide whether Page deletion should:

A. Cascade-delete its sections after explicit confirmation.

B. Be blocked until sections are removed.

C. Use another clearly justified policy.

Preferred conservative direction:

prevent accidental loss of populated Page content.

Do not silently introduce cascade deletion.

Document exact behavior before implementation.

---

## 28. Section Deletion

Removing a Page Section should require an explicit, understandable confirmation.

Deleting a section should not automatically delete any associated Media file.

Media remains a reusable independent asset.

Deletion should remove only the appropriate section record and its owned content.

---

## 29. Section Editing

Authorized administrators should be able to edit the fields of an existing Page Section.

Editing should preserve:

- section identity
- parent Page
- approved type
- ordering unless intentionally changed

Analysis must determine whether changing a section's type after creation is allowed.

Preferred direction:

type stays fixed; remove/recreate to change type.

This avoids incompatible field schemas and accidental data loss.

---

## 30. Section Type Immutability

Once a section is created, changing its type may invalidate its existing data.

Preferred direction:

do not allow type changes during ordinary editing.

Type selection belongs to section creation.

If a different type is needed, add a new section of that type.

---

## 31. Section Duplication

Do not implement section duplication automatically.

It may become useful later but adds handling for media references, ordering, and copied metadata.

Defer unless Analysis identifies immediate value.

---

## 32. Section Templates

The CMS should eventually render section types through reusable frontend components.

However, F008 must distinguish:

- content storage/schema
- admin editing
- public rendering/presentation

Do not hard-code Dewatering India styles or templates into reusable CMS content models.

---

## 33. Frontend Rendering Boundary

F007 deferred public Page rendering.

F008 must determine whether any minimal public rendering is necessary now.

Preferred direction:

keep actual frontend/theme presentation deferred.

F008 may establish renderer-friendly data structures without introducing a public catch-all route.

F020/F021 will handle theme architecture and the Dewatering India frontend.

---

## 34. Admin Preview

Analysis should determine whether F008 requires:

- content summary in the admin section list
- image thumbnail when media exists
- full Page preview
- live visual rendering

Preferred direction:

small administrative content summaries.

Do not build a full frontend preview engine.

---

## 35. Page Sections Index

Expected administration flow:

Open a Page and manage its sections.

Potential route:

`GET /admin/pages/{page}/sections`

Expected presentation:

- Page title
- current section list
- section type
- ordering
- enabled/disabled state if approved
- Edit
- Delete
- Add Section

Exact route and interface require Analysis.

---

## 36. Relationship to F007 Page Edit

Consider whether Page Sections should be accessible through:

- a Manage Sections action in the Page list
- a link on the Edit Page screen
- both

Preferred direction:

clear navigation from existing Page administration without redesigning F007.

Do not mix page identity editing and detailed section editing into one overly complex form.

---

## 37. Route Design

Potential routes:

`GET /admin/pages/{page}/sections`

`GET /admin/pages/{page}/sections/create`

`POST /admin/pages/{page}/sections`

`GET /admin/pages/{page}/sections/{section}/edit`

`PATCH /admin/pages/{page}/sections/{section}`

`DELETE /admin/pages/{page}/sections/{section}`

Additional reorder/visibility routes only if approved.

Analysis must recommend exact route names and middleware.

---

## 38. Nested Route Safety

When routes contain both Page and Section identifiers, ensure the section actually belongs to the supplied Page.

A section from Page A must not be editable or deletable through Page B's URL.

Prefer Laravel scoped route model binding or an equally clear ownership check.

This must be covered by focused tests.

---

## 39. Permission Model

F007 introduced:

`pages.manage`

Analysis must determine whether F008 should reuse it or create new section-specific permissions.

Potential options:

A. Reuse `pages.manage`.

B. Introduce `page-sections.manage`.

Preferred direction:

reuse `pages.manage` if section management is naturally part of Page administration.

Do not introduce permissions without a real authorization distinction.

Any additional permission requires approval.

---

## 40. Default Admin Permissions

Do not grant new Page Section permissions automatically.

The existing Admin baseline must remain unchanged unless explicitly approved.

Super Admin continues through centralized `Gate::before`.

---

## 41. Form Requests

Use dedicated Laravel Form Requests where type-specific validation becomes meaningful.

Potential:

`StorePageSectionRequest`

`UpdatePageSectionRequest`

Reorder and visibility validation should use the smallest appropriate implementation.

Do not build a generic form-schema framework.

---

## 42. Controller Architecture

Potential:

`PageSectionController`

Responsibilities should remain focused on:

- section listing
- create/edit forms
- validated persistence
- ordering
- visibility
- deletion

Do not introduce a large controller that handles unrelated Page or Media administration.

---

## 43. Actions / Services

Ordinary section CRUD may not require Actions.

However, reordering multiple records or coordinating reference checks could justify a focused Action.

Analysis should determine whether these operations are cohesive enough to need extraction.

Do not introduce interfaces, repositories, or service hierarchies without concrete need.

---

## 44. Database Transactions

Section reordering or multi-record updates may need a database transaction.

Use Laravel-native transactions only where multiple related writes must remain consistent.

Single-record create/update operations do not automatically require transactions.

Do not over-engineer transaction architecture.

---

## 45. Section Ordering Constraints

Analysis should determine:

- initial sort order
- ordering after section deletion
- move-up/down semantics
- deterministic tie-breaking
- whether sort order must be contiguous
- concurrency behavior appropriate to project size

Do not introduce unnecessary complex sorting algorithms.

---

## 46. Section Administration UI

The admin UI should use the existing F003 shell.

Expected characteristics:

- clean Page context
- readable section list
- clear Add Section control
- appropriately styled Edit/Delete buttons
- visible ordering controls
- useful status indication
- understandable empty state
- success/validation feedback

No page-builder canvas.

---

## 47. Create Section UX

The user should understand what each approved section type represents.

Potential approach:

1. Choose section type.
2. Display its approved fields.
3. Enter content.
4. Save.
5. Return to Page Sections list.

Analysis should recommend whether type selection and data entry occur on one screen or separate screens.

Avoid unnecessary multi-step complexity.

---

## 48. Edit Section UX

The Edit screen should clearly identify:

- owning Page
- section type
- editable fields
- visibility state if supported

On success, return to the owning Page's Section list with feedback.

On validation failure, preserve entered values and field errors.

---

## 49. Deletion Dialog

F007 introduced a styled Page Delete dialog.

F008 should use a consistent confirmation experience for section deletion.

Analysis should determine whether a small reusable confirmation component is now justified.

Do not perform unrelated F007/F006 refactoring merely to standardize dialogs.

---

## 50. Responsive Design

Page Section administration must work on desktop and mobile.

Review:

- section list
- section type selector
- field groups
- media selection if approved
- ordering controls
- action buttons
- confirmation dialog
- long content summaries
- no horizontal page overflow

Avoid a layout that only works at desktop width.

---

## 51. Accessibility

Admin controls should include:

- semantic labels
- accessible buttons
- meaningful section type labels
- keyboard-usable forms
- visible focus styles
- clear validation errors
- accessible ordering controls
- appropriate image descriptions

Do not make drag-and-drop the only method of changing section order.

---

## 52. Search / Filter / Pagination

F008 Analysis should determine whether Page Sections need separate search/filter controls.

Preferred direction:

no search or pagination initially if each Page is expected to contain a relatively small number of sections.

Order and type labels may be sufficient.

Do not copy Media Library search/pagination behavior without a real need.

---

## 53. Section Content Limits

Analysis should recommend appropriate field limits for every approved section type.

Consider:

- heading length
- description length
- CTA labels
- URL lengths
- selected media requirements

Avoid unbounded text fields where content has a predictable role.

Do not impose overly strict limits without justification.

---

## 54. Link and CTA Security

If section types contain links, validate them appropriately.

Analysis must decide whether supported targets include:

- absolute HTTPS URLs
- HTTP URLs
- relative internal paths

Reject unsafe schemes such as:

`javascript:`

`data:`

Do not allow arbitrary executable URLs.

Avoid mixing navigation-menu architecture into F008.

---

## 55. Business Neutrality

F008 must not contain Dewatering India-specific field names or content structures.

Avoid:

- pump-specific headings
- industrial-equipment-specific section types
- industry-specific media fields
- hard-coded business slogans
- predetermined business content

Those belong to actual website content or later theme implementations.

---

## 56. No Default Section Seeder

Do not create sample Page Sections automatically unless there is a demonstrated development requirement.

The reusable CMS must not seed business-specific content.

Analysis should confirm that a seeder is unnecessary.

---

## 57. F004 Dashboard Boundary

F008 should not automatically add Page Section metrics to the F004 dashboard.

Sidebar/Page navigation should be sufficient.

Do not alter the dashboard without separate justification.

---

## 58. Public Data Consumption

The future frontend should be able to load sections through an Eloquent relationship in an approved order.

Prefer a simple query/relationship structure.

Avoid N+1 queries when associated media is displayed.

Do not implement frontend caching or a headless API solely for F008.

---

## 59. Media URL Handling

If Media references are approved, use F006 runtime media URLs.

Do not store:

- localhost URLs
- absolute Windows paths
- copied media storage paths as canonical identities

Prefer references to Media records.

---

## 60. Security Requirements

Follow:

`docs/06_SECURITY_RULES.md`

F008-specific security requirements:

- authenticated administration
- server-side permissions
- scoped Page/Section ownership
- CSRF on mutations
- validated content fields
- allowlisted section types
- safe output escaping
- safe URLs
- approved media references only
- restricted mass assignment
- no arbitrary HTML/JS injection
- no cross-Page record modification
- safe deletion behavior

---

## 61. Testing Strategy

Focused F008 tests must cover approved functionality.

### Authorization

- Guest access redirects.
- Unauthorized user receives 403.
- Authorized Page administrator can manage sections.
- Super Admin continues through Gate bypass.

### Ownership

- Section belongs to the correct Page.
- Section cannot be updated through another Page's URL.
- Section cannot be deleted through another Page's URL.

### Creation

- Approved section types can be created.
- Invalid section types are rejected.
- Type-specific required fields are validated.
- Section order is initialized correctly.

### Updating

- Approved content persists.
- Unapproved fields cannot be mass-assigned.
- Section type cannot be changed if immutable.
- Existing Page relationship cannot be reassigned through request input.

### Ordering

- Reordering operates only within the owning Page.
- Ordering remains deterministic.
- Reorder actions do not mutate unrelated Pages.

### Visibility

If approved:

- sections can be enabled/disabled
- visibility persists
- invalid visibility values are rejected

### Media

If approved:

- valid selected Media references work
- invalid Media IDs are rejected
- incompatible file types are rejected where necessary
- referenced Media deletion is protected
- updating/removing a section handles references correctly

### Deletion

- authorized deletion works
- unauthorized deletion is denied
- parent-Page deletion follows approved safeguards
- deleting a section does not delete its Media file

### Security

- content is escaped
- unsafe URLs are rejected
- arbitrary HTML/JS cannot execute
- foreign Page sections cannot be manipulated
- validation errors preserve form context

Do not add tests for unapproved future functionality.

---

## 62. Existing Regression Protection

F008 must preserve:

- F002 RBAC behavior
- F003 Admin Shell
- F004 Dashboard
- F005 Website Settings
- F006 Media Library
- F007 Page Management

If F008 modifies Page or Media deletion behavior for reference safety, add targeted regression tests for those exact changes.

Do not refactor completed features unnecessarily.

---

## 63. Manual UI Verification

Expected later desktop review:

- open a Page's Sections
- add each approved section type
- view section summaries
- edit content
- verify type-specific validation
- reorder sections
- enable/disable if approved
- select Media if approved
- delete with confirmation
- verify feedback and navigation

Expected mobile review:

- section list readable
- add/edit forms usable
- ordering controls accessible
- media picker usable if included
- delete confirmation fits
- no horizontal overflow

Do not mark UI review passed without user confirmation.

---

## 64. Database Impact

F008 is expected to require:

`page_sections`

Potential foreign keys:

`page_id`

and, if approved:

`media_id`

Additional schema changes may be necessary for approved content architecture or reference safety.

Every database change must be proposed during Analysis.

Do not create migrations before implementation approval.

---

## 65. Migration Safety

New migrations must:

- be non-destructive
- avoid rewriting executed migration history
- use suitable foreign keys
- use appropriate nullability/defaults
- avoid unintended data loss
- preserve existing Page and Media records

Do not use destructive database reset commands.

---

## 66. Dependency Impact

Expected:

`None`

Use existing:

- Laravel
- Eloquent
- Blade
- Tailwind
- Alpine
- Laravel filesystem
- existing RBAC

A new package must receive explicit approval before installation.

---

## 67. Known Existing Issues

Known unrelated issues remain outside F008.

### Pint

`app/View/Components/FrontendLayout.php`

`routes/frontend.php`

### Composer

Previously documented security advisories.

Do not remediate unrelated findings during F008 unless separately approved.

---

## 68. Git / GitHub Restrictions

Follow `AGENTS.md`.

Local read-only Git inspection is allowed.

Do not:

- access GitHub or other remote repositories
- fetch or pull
- push
- clone
- commit
- create branches
- merge
- rebase
- reset
- tag

The user handles Git/GitHub manually.

---

## 69. F008 Analysis Questions

Analysis must explicitly answer:

1. What initial section types should be supported?
2. Should F008 begin with 2–3 types?
3. What exact fields belong to each type?
4. Should section types use an enum or another fixed definition?
5. Should content use typed columns, controlled JSON, or separate tables?
6. What common fields should exist on `page_sections`?
7. How should type-specific validation work?
8. Should section types be immutable after creation?
9. Should sections support enable/disable?
10. What default visibility should new sections have?
11. How should section ordering work?
12. Should ordering use Move Up/Down or drag-and-drop?
13. How are concurrent ordering changes handled simply?
14. Should rich text be supported now?
15. If formatting is needed, what safe representation should be used?
16. Should F008 introduce a minimal Media Picker?
17. Which section types actually need Media?
18. How should Media IDs be stored and validated?
19. How should deleting referenced Media be prevented?
20. Should deleting a populated Page cascade or be blocked?
21. Should section deletion affect Media files?
22. What exact Page/Section ownership enforcement is needed?
23. What routes and route names should be introduced?
24. Should existing `pages.manage` authorize Page Sections?
25. What controller/Form Requests are needed?
26. Are focused Actions justified for ordering or reference checks?
27. Should Page Sections have a dedicated management screen?
28. How should F007 Pages link to section administration?
29. Should admin previews show content summaries/media thumbnails?
30. Should a styled confirmation dialog be reused?
31. Should section creation be one-step or type-first?
32. What content validation limits are appropriate?
33. Should CTA URLs permit internal relative links?
34. Is separate Page Sections pagination necessary?
35. Should public frontend rendering remain deferred?
36. Is any new dependency required?
37. What exact migration/index/foreign-key structure is needed?
38. Which completed-feature files need changes?
39. What focused tests are required?
40. What should be explicitly deferred?

---

## 70. Expected Direction

Unless Analysis identifies a compelling reason otherwise:

- `PageSection` model/table
- direct ownership by Page
- approximately 2–3 initial section types
- fixed approved section definitions
- minimal structured content schema
- explicit type-specific validation
- simple section ordering
- Move Up/Down initially
- no drag-and-drop page builder
- no generic schema engine
- type immutable after creation
- optional visibility control if useful
- `pages.manage` reused if sufficient
- Admin default grants unchanged
- secure nested routes
- responsive section administration
- no new package
- no frontend catch-all route
- no full frontend renderer
- no SEO/Menu functionality
- no generic Media upload duplication
- minimal Media Picker only if required
- explicit Page and Media deletion safeguards
- no business-specific content or section types

---

## 71. Decisions Requiring User Approval

Before implementation, the user must approve:

- initial section types
- exact fields for each type
- section data-storage architecture
- PageSection schema
- section type representation
- ordering behavior
- visibility behavior
- editing/type-change behavior
- Media Picker scope
- Media reference structure
- Media deletion safeguards
- Page deletion safeguards
- permission model
- administration UI flow
- public rendering deferment
- affected F006/F007 files and behavior
- any new dependency

Do not implement these decisions before approval.

---

## 72. Proposed Implementation Plan

Status:

`Pending Analysis`

Expected implementation plan:

approximately 6–8 meaningful steps.

Do not implement until explicitly approved.

---

## 73. Implementation Record

### Files Created

Pending.

### Files Modified

Pending.

### Files Removed

Pending.

### Migration

Pending.

### Models / Enums

Pending.

### Controllers / Requests

Pending.

### UI

Pending.

### Media Integration

Pending decision.

### Page Deletion Protection

Pending decision.

### Permission

Pending decision.

### Dependency Changes

Expected: `None`

### Tests

Pending.

---

## 74. Validation Record

### Focused Tests

Pending.

### Regression Suite

Pending.

### Scoped Pint

Pending.

### Frontend Build

Pending.

### Schema / Migration Review

Pending.

### Page Ownership Review

Pending.

### Media Reference Safety Review

Pending if applicable.

### Authorization Review

Pending.

### Content Security Review

Pending.

### Manual UI Review

Pending.

### Final Diff Review

Pending.

---

## 75. Known Limitations

Potential F008 limitations:

- only approved initial section types
- no visual page builder
- no arbitrary HTML/CSS/JavaScript
- no advanced rich-text editor unless approved
- no content revisions
- no scheduled publication
- no global reusable block system
- no multilingual content
- no complete frontend rendering
- no theme customization
- no SEO/Menu functionality
- advanced Media Picker behavior deferred unless justified

Final limitations must be recorded after implementation.

---

## 76. Completion Record

Implementation: `Pending`

Acceptance Criteria: `Pending`

Focused Testing: `Pending`

User Code Review: `Pending`

Manual UI Review: `Pending`

Final Validation: `Pending`

Final User Approval: `Pending`

Feature Status: `Planned`

---

## 77. Acceptance Criteria

### Persistence and Relationships

- [ ] Approved PageSection model/table is implemented.
- [ ] Every section belongs to an existing Page.
- [ ] Section relationships enforce ownership correctly.
- [ ] Only approved section types may be persisted.
- [ ] Type-specific content follows approved schema.
- [ ] No unrestricted arbitrary section JSON/schema system exists.

### Authorization

- [ ] Section administration requires authentication.
- [ ] Server-side permission checks protect every operation.
- [ ] Unauthorized authenticated users receive 403.
- [ ] Super Admin access retains centralized Gate behavior.
- [ ] Cross-Page section access is prevented.

### Section CRUD

- [ ] Authorized users can add approved sections.
- [ ] Authorized users can edit approved fields.
- [ ] Invalid content receives useful validation errors.
- [ ] Section type-change behavior follows approved decision.
- [ ] Deletion requires clear confirmation.
- [ ] Deleting a section does not remove reusable Media assets.

### Ordering and Visibility

- [ ] Sections display in deterministic approved order.
- [ ] Administrators can reorder sections.
- [ ] Reordering cannot alter another Page's sections.
- [ ] Visibility behavior follows approved design.
- [ ] Section lists remain usable on desktop and mobile.

### Content Security

- [ ] Content validation is server-side.
- [ ] Disallowed section types are rejected.
- [ ] Arbitrary request fields are not persisted.
- [ ] Unsafe URLs are rejected.
- [ ] Stored content is rendered safely.
- [ ] Raw executable HTML/JavaScript is not introduced.

### Media Integration

If approved:

- [ ] Media references use approved reusable storage.
- [ ] Media selection enforces appropriate file types.
- [ ] Invalid Media references are rejected.
- [ ] Referenced Media cannot be deleted unsafely.
- [ ] No duplicate Media upload system is introduced.

### Page Safety

- [ ] Deleting a populated Page follows the approved policy.
- [ ] No accidental silent loss of Page Section content occurs.
- [ ] Completed F007 behavior remains protected by regression tests.

### Architecture

- [ ] Laravel conventions are followed.
- [ ] No speculative generic page-builder framework is introduced.
- [ ] No unnecessary repository/service layer is introduced.
- [ ] No package is added without approval.
- [ ] F008 remains business-neutral.
- [ ] Public rendering and future feature boundaries remain respected.

### Quality

- [ ] Focused F008 tests pass.
- [ ] Related F006/F007 regression tests pass where affected.
- [ ] F008-modified PHP files pass Pint.
- [ ] Frontend build passes when applicable.
- [ ] Manual desktop/mobile UI review passes.
- [ ] Full regression suite passes during final validation.
- [ ] Final diff remains scoped to F008.

---

## 78. Required Workflow

Feature Specification

→ USER AUTHORIZES ANALYSIS

→ Targeted Analysis

→ Section Architecture / Media / Deletion Recommendations

→ USER APPROVES SIGNIFICANT DECISIONS

→ Implementation

→ Focused Tests

→ USER CODE/UI REVIEW

→ Corrections if required

→ USER APPROVES FINAL VALIDATION

→ Final Validation

→ USER FINAL APPROVAL

→ Mark F008 Complete

→ Identify F009 only

→ ASK BEFORE F009 ANALYSIS

Do not automatically proceed to F009.

---

## 79. Low-Cost Requirements

Follow:

`docs/08_COST_CONTROL.md`

For F008:

- inspect only relevant Page, Media, RBAC, admin UI, and route files
- avoid unnecessary repository-wide scans
- do not research page-builder frameworks
- avoid premature rich-text editor selection
- do not inspect future content modules unnecessarily
- prefer Laravel-native functionality
- avoid new packages
- implement only approved section types
- run focused tests first
- defer full regression until after user review
- maintain explicit approval gates
- keep reports concise
- report token/cost usage only if actually available

---

## 80. Completion Rule

F008 may be marked `Ready for Final Approval` only when:

- the approved section architecture is implemented
- approved section types work
- content validation is secure
- ordering and visibility behave correctly
- section ownership is enforced
- Page deletion is safe
- Media reference deletion is safe if Media integration exists
- administration is responsive
- F002/F003/F006/F007 behavior remains intact
- no unnecessary dependency is introduced
- focused tests pass
- manual review passes
- full regression validation passes
- final diff remains F008-only

Only explicit final user approval may change status from:

`Ready for Final Approval`

to:

`Complete`

Do not begin F009 without separate authorization.