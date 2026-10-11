# Kopere BI: MCP read integration

When **local_mcp** is installed and enabled, Kopere BI is discovered automatically
through `classes/mcp/provider.php`. The integration requires no configuration or
changes to `local_mcp` and is optional: Kopere BI still works without it.

Every saved, accessible BI **page/report** is published as an independent READ
tool named `kopere_bi_report_<pageid>`. `kopere_bi_list_reports` returns the
current user's discoverable reports with IDs, titles, categories and tool names.
New reports appear on the next MCP request, without maintaining a static registry.

### Example

Call `kopere_bi_list_reports`, select a report and call its tool:

```json
{
  "name": "kopere_bi_report_27",
  "arguments": {
    "course_id": 2,
    "limit": 50,
    "offset": 0
  }
}
```

The ID `27` above is an example; use the ID from your Moodle installation.

The response includes report/category metadata, the active filters and all
report blocks. Each element contains its name and visualization type, its
column headings, raw SQL result rows, `row_count`, `has_more`, and `status`.
KPI/info elements also return `value` when available. Elements without a
data source are marked `no_data_source`. Failed elements report a generic
error without disclosing SQL or stack traces.

Supported arguments: `user_id`, `course_id`, `cohort_id`,
`limit` (default 50, maximum 200 rows per element) and `offset`.
Pagination is independent for each element, and `has_more` indicates whether
another page can be retrieved. The data returned are the raw values, not
Mustache-rendered HTML or chart JavaScript.

### Permissions and behavior

All tools are read-only and require the MCP READ scope and
`local/kopere_bi:view`. The report's own Moodle capability, page visibility
and Kopere BI feature switches also apply to discovery and execution. A user
cannot select another user's ID unless they have
`local/kopere_bi:manage`; changing the cohort filter also requires manage
permission. Course filtering respects enrolment or course-view permission
for non-manager users.

Only **saved** report SQL is executed, never SQL supplied by MCP clients.
SQL data sources are limited to a single SELECT or WITH query, with bound
filter parameters and a database-side row limit. Saved class-based data sources
must implement Kopere BI's `external_report` contract. The integration
does not alter database session read-only flags and does not expose SQL text.
