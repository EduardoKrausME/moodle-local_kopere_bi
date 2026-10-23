# Moodle Local Kopere BI

The **Kopere BI** plugin was designed to provide **visual and interactive data analysis** for Moodle environments. It empowers educators and administrators with actionable insights into student performance, engagement, and overall course effectiveness.  

## Requirements  
To function properly, **Kopere BI** requires the [**local_kopere_dashboard**](https://github.com/EduardoKrausME/moodle-local-kopere_dashboard) plugin to be installed.

## Features

Kopere BI offers a variety of options to present data, including diverse charts and formats:

#### Available Charts

Kopere BI uses [**ApexCharts**](https://apexcharts.com/) to provide a variety of interactive and customizable chart options, allowing for clear and dynamic data visualization. The available chart types include:

- **Area Charts**: Ideal for visualizing trends over time, showing the magnitude of data relative to time. Perfect for analyzing student progress.
- **Column Charts**: Useful for comparing different categories or groups. Column charts help highlight the relative performance of different classes or modules.
- **Line Charts**: Excellent for tracking progress over time, making it easy to identify patterns and fluctuations in student performance.
- **Pie Charts**: Perfect for representing parts of a whole, allowing you to visualize the percentage distribution of data clearly and intuitively.

In addition to a wide range of chart types, ApexCharts offers customization options such as colors, animations, and interactivity, allowing you to tailor the data presentation to your needs.

#### Data Presentation

Data can be displayed in different formats:

- **HTML**: With Mustache support, you can show data in any format and layout desired.
- **Info**: Small, visually appealing blocks that display key information clearly.
- **Maps**: A map that shows the location of users based on their IP address.

#### Additional Formats

- **Table**: Data can be presented in a tabular format with support for [DataTables](https://datatables.net/). This provides clear and organized data visualization, including features such as:
  - **Pagination**: Splits large datasets into pages for easier navigation.
  - **Filtering**: Real-time search to quickly locate specific information.
  - **Sorting**: Allows column sorting for better data analysis.
  - **Export**: Data can be exported in various formats, including:
      - **Print**: Allows printing the table directly from the interface while maintaining formatting.
    - **PDF**: Generates a PDF document of the table for easy sharing.
    - **Excel**: Exports data to an Excel file for further analysis.
    - **CSV**: Exports data in CSV format for importing into other applications.
      - **Copy to Clipboard**: Copies data directly to the clipboard for easy pasting into other applications.

## Learning analytics dashboards

The Learning analytics category adds six operational dashboards:

- Learning analytics overview with key indicators and daily activity trends.
- Learner attention queue with configurable risk scores and reasons.
- Course health board combining completion, progress, grades, engagement, and risk.
- Student 360 analytics for every active course enrolment.
- Progress versus grade analysis and top learner results.
- Learning momentum with falling-participation detection.

The dashboards read small snapshot tables instead of querying `logstore_standard_log` when a page opens. An hourly scheduled task rebuilds the learner, course, and daily aggregates after the standard log cache synchronisation. Risk thresholds and trend history are configurable in the plugin settings. MySQL and PostgreSQL are supported.

Table columns can be customized using [Mustache](https://moodledev.io/docs/guides/templates), allowing you to format and display the data as needed, including applying custom data.

See [the additional report collection](docs/expanded_reports.md) for 16 reports on activity reach, tracking, participation, grading, questions, feedback and forum activity.


## MySQL and PostgreSQL report editions

The built-in catalog includes separate MySQL and PostgreSQL definitions for reports that use
database-specific SQL. PostgreSQL variants use their own report, block and element reference
keys, while shared dashboards keep their database-specific blocks on the same page. The installer
selects the variant for the configured Moodle database engine.

For an existing site that has already installed this plugin version, an administrator may run
the following command from the Moodle root to add missing built-in report variants immediately
without replacing customised SQL or settings:

```bash
php local/kopere_bi/cli/import_database_reports.php
```

The operation is idempotent: existing report definitions are preserved and only missing
pages, blocks and elements are added. A future plugin version update also contains an
incremental migration for these editions.

## MCP: report APIs

Install [local_mcp](https://eduardokrausme.github.io/moodle-local_mcp/) to
automatically publish each BI report as a separate read-only MCP tool. Use
`kopere_bi_list_reports` to discover report APIs and
`kopere_bi_report_<refkey>` to read the underlying tabular data from
all elements, with optional filters and pagination. See
[the MCP integration guide](docs/mcp.md) for parameters and access control.

## Contribution
Contributions are welcome!

Feel free to open issues or pull requests with ideas.

If you encounter any bugs or have suggestions for improvement, don’t hesitate to let us know.
   
## License
This plugin is licensed under the [GNU GPL v3](https://www.gnu.org/licenses/gpl-3.0.en.html).
   
## Contact
For more information, contact Eduardo Kraus via [Contact](https://eduardokraus.com/contato).
