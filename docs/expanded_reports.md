# Additional report collection (112–127)

The 16 bundled reports extend the existing catalog without creating Chat or Wiki dashboards. Definitions reside in `db/files/page-112.json` through `page-127.json`, and existing installations receive them during the plugin upgrade to database version `2026102200`. The upgrade imports only these pages and preserves existing reports and modifications. Both MySQL and PostgreSQL are supported; SQL uses Moodle epoch timestamps and portable aggregate functions.

- **112 — Engagement by course section**: Estimated tracked time, visits and reached learners per visible course section.
- **113 — Activity reach by course**: Share of active enrolled learners with at least one recorded module visit.
- **114 — Tracked time by activity type**: Tracked seconds, visits and reached learners by module type and course; time is an estimate, not active screen time.
- **115 — Daily activity by course role**: Daily logged module actions grouped by learner, teacher and other role, using UTC day boundaries.
- **116 — Courses active per day**: Daily number of courses with recorded module actions, based on the latest analytics snapshot.
- **117 — Weekly enrolments versus completions**: Newly enrolled users and course completions in each UTC Monday-based week.
- **118 — Weekly completions by course category**: Course completion volume and distinct courses with completions per category and UTC week.
- **119 — Learner participation versus class**: Individual 30-day action counts against peers in the same course and snapshot; actions do not measure learning quality.
- **120 — Compare two quiz results**: Compare the most recently finished attempts from pairs of quizzes within a course, as percentages; pair order follows quiz ID.
- **121 — Grade change history**: Recorded grade history entries, including previous grades and changes, where the native Moodle history is retained.
- **122 — Frequently incorrect quiz responses**: Frequently repeated answers that were not fully correct in finished quiz attempts, grouped by question and response summary.
- **123 — Quiz performance by question tag**: Fully correct question outcomes grouped by tags attached to questions; only finished quiz attempts count.
- **124 — Grading criteria performance**: Average awarded score per advanced grading guide or rubric criterion on graded assignments.
- **125 — Feedback answer distribution**: Counts and proportions of stored single-choice feedback answer values; response codes remain as stored by Moodle.
- **126 — Forum posts by weekday and time**: All forum posts by UTC weekday and time band; includes replies by all authors.
- **127 — Assessment submission timeline**: Recent submission and forum-post creation events for enrolled learners, taken from the standard event cache.

## Measurement and scope

Tracking-derived seconds are estimates derived from Kopere BI's online collector and should not be presented as measured active screen time. Daily aggregates use the latest completed analytics snapshot. UTC epoch day/week boundaries are used where noted. Question statistics count finished attempts; each latest question step is used rather than every intermediate step. The historical grade report only shows rows retained in Moodle's native grade history. The advanced-grading criteria report requires the core guide/rubric tables. Queries returning individual data require access controls on report pages consistent with site privacy policies. Heavy row-level reports are capped at 500 results; consider course-level filtering for large installations.



## Refinements to items 2–7 (existing pages)

Pages 121, 122, 124, 125 and 126 were already bundled; page 52 already handled grading
backlogs. This change improves their existing SQL rather than adding duplicate pages.

- **121:** Link successive history rows of the same original grade (oldid) to show previous
  grade, signed difference and acting user. If history was purged, previous values remain blank.
- **122:** Ignore quiz previews and deleted users, expose the question ID and the percentage
  of *not fully correct* responses represented by each answer, calculated per course/question.
- **124:** Include assignment and criterion IDs, group by the assignment, count distinct
  active assessment instances and preserve guide/rubric results.
- **125:** Count only completed Feedback submissions; include IDs, response type, distinct
  respondents and denominator. Moodle stores codes for selection questions. These remain
  codes, and multi-select combinations are not split into independent choices.
- **52 (MySQL and PostgreSQL):** Replace enrolment joins with EXISTS to avoid duplicate
  pendencies for learners with multiple concurrent enrolments, exclude expired enrolments,
  add overdue and days-waiting indicators, and cap results at 500. The listed teachers are
  course-level teachers, not a guarantee of an individual grading assignment.
- **126:** Break out forum activity by forum, count discussion starts and unique contributors,
  and show posts per contributor. Time bands are UTC, not the viewer's local timezone.

### Applying changes to an existing Moodle site

The Moodle upgrade to plugin database version `2026102401` automatically imports missing
MySQL/PostgreSQL report variants and refreshes the refined analytics SQL. No separate
Kopere BI CLI scripts are required; administrators can use the standard Moodle upgrade
interface or `php admin/cli/upgrade.php --non-interactive` from the Moodle root.

The upgrade keeps existing pages, blocks and elements, and changes SQL only when it matches
an exact checksum from a previously shipped definition. Locally customised SQL remains
untouched, and existing column formatting is preserved while missing columns are added.
Moodle records the upgrade savepoint only after these operations complete.
