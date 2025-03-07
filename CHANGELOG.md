# Changelog

All notable changes to `laravel-database-mail` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] - 2025-03-07
### Fixed
- Added missing create_mail_exceptions_table from migrations registration
- Added option to get local accessor path on Property
- Added cast to decode html entities on MailTemplate subject and body

## [1.1.0] - 2025-03-07
### Added
- Added MailException model that catches exceptions thrown by your badly formated mail templates

## [1.0.0] - 2025-03-05
### Added
- Added initial version of Laravel Database Mail

[unreleased]: https://github.com/martinpetricko/laravel-database-mail/compare/1.1.1...HEAD
[1.1.1]: https://github.com/martinpetricko/laravel-database-mail/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/martinpetricko/laravel-database-mail/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/martinpetricko/laravel-database-mail/releases/tag/1.0.0
