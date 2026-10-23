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

