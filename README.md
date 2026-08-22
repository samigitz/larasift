# LaraSift

Find common security mistakes in Laravel code before they reach production.

LaraSift scans your project, shows the exact location of each finding, and suggests a safer approach. It reads your files without starting or executing your Laravel application.

> LaraSift is under development. The current version includes the first Blade XSS check. More Laravel security checks are coming next.

## Install

The package is not published yet. After it is available through Composer, install it in your Laravel project as a development dependency. Composer will create the `./vendor/bin/larasift` command, just as it does for Pint.

To work on LaraSift itself today, clone this repository and install its dependencies:

```bash
composer install
```

## Run a scan

From your Laravel project, scan the current directory:

```bash
./vendor/bin/larasift
```

Show the reason for each finding and the suggested fix:

```bash
./vendor/bin/larasift --details
```

## Choose what to scan

Run one category:

```bash
./vendor/bin/larasift --category=xss
```

Run one rule:

```bash
./vendor/bin/larasift --rule=LSEC-XSS-002
```

Only show findings at or above a severity level:

```bash
./vendor/bin/larasift --severity=high
```

## JSON output

Use JSON in CI or other tools:

```bash
./vendor/bin/larasift --format=json
```

## List the available rules

```bash
./vendor/bin/larasift list-rules
```

## Exit codes

- `0`: the scan completed with no matching findings
- `1`: the scan found one or more matching issues
- `2`: the command or an option was invalid
- `3`: the scan could not finish correctly

## Current security check

`LSEC-XSS-002` finds dynamic Blade output that uses `{!! !!}` without HTML escaping. It ignores escaped output, Blade comments, `@verbatim` blocks, and static text.

LaraSift reports potential security problems. A clean scan does not guarantee that an application is secure, and every finding should be reviewed in context.

## Development

Run the project checks:

```bash
composer test
composer analyse -- --debug
vendor/bin/pint --test
```
