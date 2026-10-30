# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- PHPStan analysis and DOM and COM stubs for development checks.
- DDEV configuration for local development and testing.

### Changed

- Require `slothsoft/core` ^1.15.1 and `slothsoft/farah` ^1.29.7, and explicitly declare the required DOM extension.
- Expand integration checks across PHP 8.2–8.5, Linux, and Windows.

### Fixed

- Resolve PHPStan findings in schema documentation and version information generation.
- Return an empty string from `XSDNode::getComplexType()` when no named complex type exists.

## [1.7.3] - 2026-04-02

### Changed

- Lower the minimum PHP version from 8.3 to 8.2.
- Require `slothsoft/farah` ^1.28.2 and update development dependencies.

## [1.7.2] - 2026-04-02

### Changed

- Update the Farah testing dependency to ^1.3.4.

## [1.7.1] - 2026-04-02

### Changed

- Raise the minimum PHP version from 8.0 to 8.3.
- Require `slothsoft/farah` ^1.28.1.

## [1.7.0] - 2026-04-02

### Added

- Optional RAM data rate and CAS latency attributes in Computers Schema 1.0, with data rate, CAS latency, and calculated total latency in HTML output.

### Changed

- Raise the minimum PHP version from 7.4 to 8.0.

## [1.6.7] - 2026-03-26

### Fixed

- Allow computer cards to wrap onto additional rows in HTML output.

## [1.6.6] - 2026-03-26

### Changed

- Group computer and spare-parts listings into separate HTML sections, with anchors on their articles.

## [1.6.5] - 2026-03-26

### Fixed

- Correct GPU details in computer HTML output and update the GPU examples.

## [1.6.4] - 2026-03-26

### Added

- Support the `ECC` suffix in RAM type values in Computers Schema 1.0.

## [1.6.3] - 2026-03-26

### Added

- EATX mainboard and USB 2, USB 3, and SATA I–III storage interface values in Computers Schema 1.0.

### Fixed

- Correct the mainboard form-factor value from `Mini-ATX` to `Mini-ITX`.

## [1.6.2] - 2026-03-26

### Added

- More detailed RAM and storage definitions and HTML output for Computers Schema 1.0.

### Fixed

- Correct CPU socket matching in computer HTML output.

## [1.6.1] - 2026-03-26

### Fixed

- Correct heading levels in computer HTML output.

## [1.6.0] - 2026-03-26

### Added

- Computers Schema 1.0, example inventory, styles, and XSLT functions and HTML rendering for computers and spare parts.

### Changed

- Update Core, Farah, and Farah testing dependencies.

### Fixed

- Make nullable DOM parameters explicit for PHP 8.5 compatibility.
- Use HTTPS for the Farah module schema location.

## [1.5.15] - 2025-11-24

### Fixed

- Display special wildcard namespace values such as `##other` as text instead of external links in schema documentation.

## [1.5.14] - 2025-11-19

### Changed

- Migrate schema documentation templates to Farah's namespaced dictionary attributes and dictionary resources.

### Removed

- The legacy `dictionary/de-de` and `dictionary/en-us` assets.

### Fixed

- Correct dictionary lookup in the schema documentation footer.

## [1.5.13] - 2025-11-04

### Changed

- Expand Historical Games Night Schema 2.0 attribute documentation, including country codes, game names, ports, and semicolon-separated developer names.

### Fixed

- Preserve CDATA content and ignore whitespace-only text nodes when collecting schema documentation.

## [1.5.12] - 2025-11-04

### Added

- The optional `ported` attribute and detailed game attribute documentation in Historical Games Night Schema 2.0.

### Changed

- Use `slothsoft/farah-testing` for module integration tests and update Core and Farah dependencies.

## [1.5.11] - 2025-10-17

### Added

- Optional game and platform `wiki` URLs in Historical Games Night Schema 2.0.

### Changed

- Restrict game, platform, and reading links to HTTP or HTTPS URLs; replace the required platform `href` with optional `wiki`.

### Fixed

- Stop generating empty platform `href` attributes during conversion from Historical Games Night 1.0 to 2.0.

## [1.5.10] - 2025-10-17

### Changed

- Rename game `from` and reading `year` to `released`, reading `title` to `name`, and reading `author` to `by` in Historical Games Night Schema 2.0.
- Require `version="2.0"` on Historical Games Night `index` and `events` documents.

### Fixed

- Apply the renamed attributes in the Historical Games Night 1.0-to-2.0 converter.

## [1.5.9] - 2025-10-15

### Changed

- Identify Historical Games Night 2.0 event requirements by `theme` instead of a date reference, and update the converter accordingly.

### Removed

- Historical Games Night 2.0 event `rerun` attributes and the associated date references.

### Fixed

- Preserve event `twitter` attributes during Historical Games Night conversion.

## [1.5.8] - 2025-10-15

### Fixed

- Convert Historical Games Night dates and times that omit the weekday prefix.

## [1.5.7] - 2025-10-15

### Fixed

- Convert event requirements to date references and generate child elements in the Historical Games Night namespace during 1.0-to-2.0 conversion.

## [1.5.6] - 2025-10-15

### Added

- Historical Games Night Schema 2.0, an example index, and an XSLT converter from version 1.0.
- Separate platform definitions, track identifiers, and event date and time fields in Historical Games Night Schema 2.0.

### Changed

- Require `slothsoft/core` ^1.13.18.

## [1.5.5] - 2025-09-23

### Fixed

- Use matching, deterministic section IDs and table-of-contents links in schema documentation.

## [1.5.4] - 2025-09-23

### Fixed

- Render schema page headers, bodies, and footers in the correct version-information context so generated links use the correct schema URLs.
- Correct URLs in Presskit examples.

## [1.5.3] - 2025-09-22

### Fixed

- Render the contents of a Presskit company description instead of copying its schema wrapper into HTML.

## [1.5.2] - 2025-09-20

### Fixed

- Import the HTML5 asset from Farah instead of Core, with `slothsoft/farah` ^1.20.0 required.

## [1.5.1] - 2025-09-13

### Fixed

- Publish the JUnit schema directory through the module manifest and remove the obsolete third-party directory registration.

## [1.5.0] - 2025-09-13

### Added

- JUnit Schema 1.0.

### Changed

- Resolve the standard XML namespace schema through Farah and update the Farah dependency.

## [1.4.6] - 2025-09-13

### Fixed

- Remove the duplicate `about` ID from the Presskit special-thanks heading.

## [1.4.5] - 2025-09-13

### Fixed

- Give the schema documentation groups heading its own `groups` anchor instead of reusing `categories`.

## [1.4.4] - 2025-09-12

### Fixed

- Add the trailing slash to schema documentation version links.

## [1.4.3] - 2025-09-12

### Added

- Revision history in Historical Games Night Schema 1.0.

### Changed

- Restrict Historical Games Night `gfx` and `manual` values to asset filenames without path separators.

## [1.4.2] - 2025-09-10

### Changed

- Distinguish full `index` documents from standalone `events` lists in Historical Games Night Schema 1.0.
- Move module registration into the PSR-4 source bootstrap.

## [1.4.1] - 2025-07-16

### Fixed

- Mark essential event, game, and reading attributes as required in Historical Games Night Schema 1.0.

## [1.4.0] - 2025-07-16

### Added

- Historical Games Night Schema 1.0.

### Changed

- Update Core and Farah dependency requirements.

## [1.3.5] - 2025-07-06

### Added

- Support multiple `theme` elements in Presskit Schema 1.1.

## [1.3.4] - 2025-07-06

### Added

- Support `xml:lang` on embedded HTML content in Presskit Schema 1.1.

## [1.3.3] - 2025-07-06

### Fixed

- Respect the supplied Presskit link relationship instead of always forcing `rel="external"` and a new browser tab.

## [1.3.2] - 2025-07-06

### Changed

- Make the description of Presskit `additional` entries optional.
- Document allowed HTML phrasing content in Presskit Schema 1.1.

## [1.3.1] - 2025-07-04

### Added

- Automated PHP API documentation publishing to GitHub Pages.

## [1.3.0] - 2025-07-04

### Added

- Presskit Schema 1.1, including a game-jam example and dedicated link elements.
- Reusable Presskit XSLT functions for links and dates.

## [1.2.1] - 2025-07-03

### Fixed

- Correct YouTube video embedding on Presskit game pages.

## [1.2.0] - 2025-07-03

### Added

- Presskit Schema 1.0 for company and game documents, example documents, and an HTML game-page renderer.

### Changed

- Remove the PHP 8.0 upper bound, allowing PHP versions from 7.4 onward.

## [1.1.6] - 2024-10-05

### Changed

- Update locked development dependencies and PHPUnit and phpDocumentor configuration.

## [1.1.5] - 2024-09-29

### Fixed

- Align Composer license metadata with the MIT license introduced in 1.1.4.

## [1.1.4] - 2024-09-23

### Changed

- Replace the license text with the MIT license.
- Use `main` as the Composer development branch alias.

## [1.1.3] - 2024-04-01

### Changed

- Update locked development dependencies.

## [1.1.2] - 2024-04-01

### Fixed

- Correct Composer's bootstrap path after renaming the module registration script.
- Correct the CI badge in the README.

## [1.1.1] - 2022-01-21

### Changed

- Require PHP 7.4–8.0, Core ^1.5, and Farah ^1.6.
- Replace Travis CI with GitHub Actions testing.

## [1.1.0] - 2021-01-03

### Added

- Direct `.xsd` downloads from the schema version listing.

### Changed

- Migrate source and test autoloading to PSR-4 and update Farah integration to ^1.4.
- Declare support for PHP 7.2–7.4.

### Fixed

- Sort schema versions by version number, with the newest first.

## [1.0.4] - 2020-03-30

### Added

- Display schema revision histories on documentation and version-listing pages.

## [1.0.3] - 2020-03-24

### Added

- Generate example content for XML Schema `float` and `double` types.

### Fixed

- Remove unwanted whitespace around quoted enumeration tokens in schema documentation.
- Include the schema URL in errors about missing version information.

## [1.0.2] - 2020-03-23

### Fixed

- Correct schema download URLs for the derived asset routing scheme.

## [1.0.1] - 2018-08-10

### Fixed

- Remove redundant angle brackets around schema tags in documentation styling.

## [1.0.0] - 2018-08-10

### Added

- Initial tagged release of the Farah XML Schema module.
- Schema version information and manifest generation, HTML documentation, and XML example generation.
- Schema Versioning Schema 1.0 and English and German documentation dictionaries.

[Unreleased]: https://github.com/Faulo/slothsoft-schema/compare/1.7.3...HEAD
[1.7.3]: https://github.com/Faulo/slothsoft-schema/compare/1.7.2...1.7.3
[1.7.2]: https://github.com/Faulo/slothsoft-schema/compare/1.7.1...1.7.2
[1.7.1]: https://github.com/Faulo/slothsoft-schema/compare/1.7.0...1.7.1
[1.7.0]: https://github.com/Faulo/slothsoft-schema/compare/1.6.7...1.7.0
[1.6.7]: https://github.com/Faulo/slothsoft-schema/compare/1.6.6...1.6.7
[1.6.6]: https://github.com/Faulo/slothsoft-schema/compare/1.6.5...1.6.6
[1.6.5]: https://github.com/Faulo/slothsoft-schema/compare/1.6.4...1.6.5
[1.6.4]: https://github.com/Faulo/slothsoft-schema/compare/1.6.3...1.6.4
[1.6.3]: https://github.com/Faulo/slothsoft-schema/compare/1.6.2...1.6.3
[1.6.2]: https://github.com/Faulo/slothsoft-schema/compare/1.6.1...1.6.2
[1.6.1]: https://github.com/Faulo/slothsoft-schema/compare/1.6.0...1.6.1
[1.6.0]: https://github.com/Faulo/slothsoft-schema/compare/1.5.15...1.6.0
[1.5.15]: https://github.com/Faulo/slothsoft-schema/compare/1.5.14...1.5.15
[1.5.14]: https://github.com/Faulo/slothsoft-schema/compare/1.5.13...1.5.14
[1.5.13]: https://github.com/Faulo/slothsoft-schema/compare/1.5.12...1.5.13
[1.5.12]: https://github.com/Faulo/slothsoft-schema/compare/1.5.11...1.5.12
[1.5.11]: https://github.com/Faulo/slothsoft-schema/compare/1.5.10...1.5.11
[1.5.10]: https://github.com/Faulo/slothsoft-schema/compare/1.5.9...1.5.10
[1.5.9]: https://github.com/Faulo/slothsoft-schema/compare/1.5.8...1.5.9
[1.5.8]: https://github.com/Faulo/slothsoft-schema/compare/1.5.7...1.5.8
[1.5.7]: https://github.com/Faulo/slothsoft-schema/compare/1.5.6...1.5.7
[1.5.6]: https://github.com/Faulo/slothsoft-schema/compare/1.5.5...1.5.6
[1.5.5]: https://github.com/Faulo/slothsoft-schema/compare/1.5.4...1.5.5
[1.5.4]: https://github.com/Faulo/slothsoft-schema/compare/1.5.3...1.5.4
[1.5.3]: https://github.com/Faulo/slothsoft-schema/compare/1.5.2...1.5.3
[1.5.2]: https://github.com/Faulo/slothsoft-schema/compare/1.5.1...1.5.2
[1.5.1]: https://github.com/Faulo/slothsoft-schema/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/Faulo/slothsoft-schema/compare/1.4.6...1.5.0
[1.4.6]: https://github.com/Faulo/slothsoft-schema/compare/1.4.5...1.4.6
[1.4.5]: https://github.com/Faulo/slothsoft-schema/compare/1.4.4...1.4.5
[1.4.4]: https://github.com/Faulo/slothsoft-schema/compare/1.4.3...1.4.4
[1.4.3]: https://github.com/Faulo/slothsoft-schema/compare/1.4.2...1.4.3
[1.4.2]: https://github.com/Faulo/slothsoft-schema/compare/1.4.1...1.4.2
[1.4.1]: https://github.com/Faulo/slothsoft-schema/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/Faulo/slothsoft-schema/compare/1.3.5...1.4.0
[1.3.5]: https://github.com/Faulo/slothsoft-schema/compare/1.3.4...1.3.5
[1.3.4]: https://github.com/Faulo/slothsoft-schema/compare/1.3.3...1.3.4
[1.3.3]: https://github.com/Faulo/slothsoft-schema/compare/1.3.2...1.3.3
[1.3.2]: https://github.com/Faulo/slothsoft-schema/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/Faulo/slothsoft-schema/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/Faulo/slothsoft-schema/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/Faulo/slothsoft-schema/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/Faulo/slothsoft-schema/compare/1.1.6...1.2.0
[1.1.6]: https://github.com/Faulo/slothsoft-schema/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/Faulo/slothsoft-schema/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/Faulo/slothsoft-schema/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/Faulo/slothsoft-schema/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/Faulo/slothsoft-schema/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/Faulo/slothsoft-schema/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/Faulo/slothsoft-schema/compare/1.0.4...1.1.0
[1.0.4]: https://github.com/Faulo/slothsoft-schema/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/Faulo/slothsoft-schema/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Faulo/slothsoft-schema/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Faulo/slothsoft-schema/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Faulo/slothsoft-schema/releases/tag/1.0.0
