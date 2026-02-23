# Changelog

All notable changes to `laravel-database-mail` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/martinpetricko/laravel-database-mail/compare/2.0.2...HEAD)

## [2.0.2](https://github.com/martinpetricko/laravel-database-mail/compare/2.0.1...2.0.2) - 2026-02-23

### What's Changed

* Add from address validation by @MartinPetricko in https://github.com/MartinPetricko/laravel-database-mail/pull/20

**Full Changelog**: https://github.com/MartinPetricko/laravel-database-mail/compare/2.0.1...2.0.2

## [2.0.1](https://github.com/martinpetricko/laravel-database-mail/compare/2.0.0...2.0.1) - 2026-02-22

**Full Changelog**: https://github.com/MartinPetricko/laravel-database-mail/compare/2.0.0...2.0.1

## [2.0.0](https://github.com/martinpetricko/laravel-database-mail/compare/1.4.0...2.0.0) - 2026-02-22

### Added

- Added from address to mail templates
- Added new migration

## [1.4.0](https://github.com/martinpetricko/laravel-database-mail/compare/1.3.1...1.4.0) - 2025-12-11

### Added

- Added onSuccess and onFailure callbacs to mail events

## [1.3.1](https://github.com/martinpetricko/laravel-database-mail/compare/1.3.0...1.3.1) - 2025-12-10

### Fixed

- Skip property resolving on thrown exception

## [1.3.0](https://github.com/martinpetricko/laravel-database-mail/compare/1.3.0...1.3.1) - 2025-05-31

### Added

- Added a ListResolver that can resolve arrayable event properties with generics defined

## [1.2.2](https://github.com/martinpetricko/laravel-database-mail/compare/1.2.1...1.2.2) - 2025-04-12

### Fixed

- Fix missleading event example in config

## [1.2.1](https://github.com/martinpetricko/laravel-database-mail/compare/1.2.0...1.2.1) - 2025-03-31

### Fixed

- Changed attachment type to base laravel Attachment class

## [1.2.0](https://github.com/martinpetricko/laravel-database-mail/compare/1.1.1...1.2.0) - 2025-03-09

### Added

- Added ExportMailTemplates command
- Added ImportMailTemplates command

## [1.1.1](https://github.com/martinpetricko/laravel-database-mail/compare/1.1.0...1.1.1) - 2025-03-07

### Fixed

- Added missing create_mail_exceptions_table from migrations registration
- Added option to get local accessor path on Property
- Added cast to decode html entities on MailTemplate subject and body

## [1.1.0](https://github.com/martinpetricko/laravel-database-mail/compare/1.0.0...1.1.0) - 2025-03-07

### Added

- Added MailException model that catches exceptions thrown by your badly formated mail templates

## [1.0.0](https://github.com/martinpetricko/laravel-database-mail/releases/tag/1.0.0) - 2025-03-05

### Added

- Added initial version of Laravel Database Mail
