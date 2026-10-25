# Changelog

This file documents the two most recent **published releases of the main `local_kopere_bi` plugin** confirmed in GitHub's release list on 2026-10-24. It excludes unreleased commits and the separately versioned `bifilters_period` subplugin release `1.0.0`.

## [3.2.9] - 2026-10-22

### Added
- Added 16 bundled analytics reports (`db/files/page-112.json` through `page-127.json`) covering course-section engagement, activity reach, time tracking by activity type, daily activity by role, active courses, enrolments, completions, learner participation, quiz comparisons, grade history, frequently incorrect answers, question tags, grading criteria, feedback distribution, forum activity and submission timelines.
- Added dedicated PostgreSQL variants of bundled reports for online activity, learners, instructors, courses, learning activities, quizzes and forums, while retaining the existing MySQL versions where database-specific SQL is required.

### Changed
- Updated the report-import routine to respect MySQL and PostgreSQL prerequisites independently on report pages, blocks and elements, including `postgres`, `postgresql` and `pgsql` aliases.
- Added an upgrade step to import only newly bundled pages 112–127 into existing installations, without replacing other report pages or local customisations.
- Adjusted BI block cache checks and the associated migration test expectations.
- Updated English and Brazilian Portuguese report language strings for the expanded report collection.

**Full comparison:** [3.2.8...3.2.9](https://github.com/EduardoKrausME/moodle-local_kopere_bi/compare/3.2.8...3.2.9)

## [3.2.8] - 2026-10-20

### Fixed
- Corrected SQL definitions in numerous bundled reports covering users, courses, enrolments, progress, instructors, assignments, forums, SCORM, quizzes and other learning-activity data.
- Reworked the feedback overview SQL (`db/files/page-083.json`) to avoid invalid placeholders and MySQL-specific expressions, calculate averages from eligible numeric responses, and support both MySQL/MariaDB and PostgreSQL.
- Removed the MySQL-only prerequisite from the corrected feedback overview report.

### Upgrade and data preservation
- Added checksum-based migrations in `classes/upgrade/report_migrations.php` to apply corrected SQL to installed reports only when their previous definition matches a known bundled version.
- Added handling for both the original and an intermediate feedback overview SQL revision; locally customised SQL remains unchanged.
- Added database upgrade steps to run these fixes for existing installations.

### Tests
- Added legacy and intermediate feedback SQL fixtures and regression tests for migration, idempotence, preserving local changes, and SQL execution.

**Full comparison:** [3.2.7...3.2.8](https://github.com/EduardoKrausME/moodle-local_kopere_bi/compare/3.2.7...3.2.8)
