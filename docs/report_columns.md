# Global report columns in Kopere BI

Open Kopere Dashboard > Kopere BI > Report columns. Only users with local/kopere_bi:manage can save these global preferences. They are stored as component configuration reportcols_user and reportcols_course (JSON arrays), not in the site plugin settings screen.

Existing reports retain their original results until settings are first saved. After saving, compatible table elements are enriched in the regular interface, AJAX data, exports using the table dataset, and structured MCP reports, without rewriting persisted report SQL.

## Fields

User columns include name (required), email, username, identification number, phone, city, country, institution and department. Moodle custom profile fields are discovered in user_info_field and their data is read from user_info_data in batches. A profile field with shortname cpf is exposed as user_custom_cpf.

Course columns include name (required), shortname, identification number, category ID, dates, visibility and custom course fields. The course field handler uses Moodle core_course customfield APIs. A shortname campus is exposed as course_custom_campus.

Private user profile values require the user's own identity, Moodle user full-details permission, or Kopere BI management capability. The course custom fields API's visibility rules apply. Field values are escaped in the table/JSON output.

## Identity mapping

This feature never assumes that every SQL id is a user ID. A table should return userid or user_id for users; username is supported where the SQL references the user table. A table with id, firstname and lastname together can also identify a user when the SQL references the user table. For courses, use courseid, course_id or c_id, or shortname when the SQL references the course table.

For unusual custom SQL, an editor may add identity metadata to the persisted element info JSON:

    "entities": {
      "user": {"idcolumn": "studentid"},
      "course": {"idcolumn": "coursekey"}
    }

The names must be existing SQL result keys whose values are real user and course identifiers. Avoid mapping aggregate IDs or unrelated attempt/question IDs.

The plugin enriches raw rows after executing the original query. It does not affect joins, ranking or aggregation in the SQL. Existing caches hold raw data; changes in preferences purge cached table datasets.

## Compatibility

- SQL statements, existing reports and migrations are preserved.
- Charts, metrics and aggregate reports without a reliable entity reference are not modified.
- Rows with insufficient identity information cannot be enriched until that report is given an explicit mapping.
- Column preferences apply to matching table elements; they do not grant additional permissions to view protected Moodle data.
- Institutions should restrict reports containing CPF or other personal identifiers according to their privacy policies.
