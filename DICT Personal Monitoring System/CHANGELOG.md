# Changelog

All notable changes to the DICT Personal Monitoring System will be documented in this file.

## [2.9.1] - 2025-10-15

### Added
- Activities: Periodic repeats when adding activities, supporting Daily/Weekly/Monthly/Yearly with interval and end conditions (never, on date, after N occurrences)

### Changed
- Activities: Repeat section UI redesigned for a more formal, professional layout with clearer labels and hints
- Dashboard: Activity score cards now scoped to the current year (monthly performance unchanged)

### Notes
- Repeats create independent activity records; editing one occurrence does not change others

## [2.9.0] - 2025-09-18

### Added
- Calendar view is now the default in `activities.php` for a more visual activity timeline
- Comprehensive calendar enhancements: color-coded status badges, activity detail modals, month navigation, today highlighting, and responsive layout

### Fixed
- Robust date parsing for activities: supports single dates and complex date ranges across months/years with validation and fallbacks

### Improved
- Auto-scroll to nearest date activity now triggers on page refresh, filter reset, activity deletion redirect, and when switching to table view
- Context-aware filter visibility: filters hidden in calendar view and shown in table view
- General UX, performance, and security improvements

## [2.6.0] - 2025-08-16

### Added
- **Activities**: Implemented comprehensive filtering system for activities
- **UI**: Added date range filtering for activities
- **Performance**: Optimized activity queries for better performance with filters
- **UX**: Improved popover behavior in activity requirements

### Fixed
- **Activities**: Resolved issues with activity filters not applying correctly
- **UI**: Removed redundant close button from activity requirements popover
- **Performance**: Optimized database queries for activity filtering

### Changed
- **UI**: Enhanced filter interface for better usability
- **Code**: Refactored activity filtering logic for maintainability

## [2.5.0] - 2025-08-15

### Added
- **Database**: Added robust error handling and retry logic for database queries
- **Security**: Enhanced PDO configuration with emulated prepares disabled for better security
- **UI**: Improved error messages and user feedback throughout the application
- **TEV Claims**: Added white text styling for better visibility of claims count
- **Documentation**: Created this CHANGELOG.md for better version tracking

### Fixed
- **Database**: Fixed "Unknown type 242" PDO/MySQL compatibility issue
- **Database**: Resolved unbuffered query errors with proper cursor management
- **Security**: Fixed potential SQL injection vulnerabilities in query building
- **UI**: Fixed text color contrast issues in various components
- **Performance**: Optimized database queries for better performance

### Changed
- **Database**: Updated PDO connection settings for better stability
- **Code**: Refactored database access layer for improved maintainability
- **Dependencies**: Updated PHP version requirement to 8.2+

## [2.4.0] - 2025-08-14

### Added
- Initial release of the DICT Personal Monitoring System
- User authentication and authorization
- Project and activity management
- Notes and TEV claims functionality
- Basic reporting and dashboard

---
*Note: This changelog was started with version 2.5.0. Previous versions may not be fully documented.*
