# keruald/report

Allow to build a report and output it.

## Report skeleton

### Introduction

A report is a collection of sections, a general title, and some metadata.

A section is a collection of entries, and a title. They can also be thought as the chapters of a book.

An entry is text and a title.

That gives the following hierarchy:

```
Report                  title   (sections)     properties
  ReportSection         title   (entries)
    ReportEntry         title   text
```

A full example can be found in the  `tests/WithSampleReport.php` file.

### Simplified report

You can build a simplified version using only the ReportSection class:

```php
use Keruald\Reporting\ReportSection;

$report = new ReportSection("A simple report about historical geometric problems");
$report->push("Issue 1", "Can we square a circle?");
$report->push("Issue 2", "Can we divise an angle by 3?");
$report->push("Issue 2", "Can we double a cube?");

print_r($report);
```

## Output

The library provides HTML and Markdown output.

Examples of such output can be found in the `tests/data` folder.

Those output classes aren't mandatory to use to present the results:
the report data structure can be easily walked with foreach loops
to manipulate it.

## Uses

The **keruald/healthcheck** library uses this reporting library to generate
a site health check, and present the results to help to remediate to
the issues detected.
