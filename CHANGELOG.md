# Changelog

All notable changes to this project will be documented in this file.

## [v1.1.0](https://github.com/kolaybi/activity-log/compare/v1.0.0...v1.1.0)  (2026-03-05)

### Added
- `migrations` config option (default `true`) to control automatic migration loading. Set to `false` when the consuming app provides its own custom migration.

## [v1.0.0](https://github.com/kolaybi/activity-log/commits/v1.0.0)  (2026-03-04)

### Added
- `AbstractActivity` base class with queue dispatch and eager context resolution.
- `Activity` Eloquent model with ULID primary keys, soft deletes, and i18n entry resolution.
- `ActivityContextProvider` contract for supplying creator and tenant context.
- `ActivityGroup` marker interface for app-defined activity group enums.
- `NullContextProvider` default implementation (returns null for both creator and tenant).
- `ActivityLogServiceProvider` with config merging, context binding, and publishable migrations/config.
- Configurable database connection, table name, model class, and queue connection.
- Enum parameter resolution in `entry` attribute (supports `enum`/`value`/`function` array format).
- Validated `BackedEnum` check before resolving enum parameters.
- Migration for `activities` table with indexed columns.

## Notes

This is the initial release of the this package
