# GeoServer client changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

This project adhere to Semantic Versioning.

## Unreleased

### Added
### Changed
### Deprecated
### Removed
### Fixed
### Security

## [0.4.1] - 2022-08-07

### Added

- Automated testing against GeoServer 2.20.x and 2.21.x

### Changed

- Ignore already existing errors while creating a workspace

## [0.4.0] - 2022-08-06

### Added

- Support for PHP 8.0 and 8.1
- Support for GeoServer 2.17.x

### Removed

- Support for PHP 7.1, 7.2 and 7.3

## [0.3.0] - 2020-01-06

### Added

- Support for PHP 7.4
- Support for GeoServer 2.15.x

### Changed

- Style upload original filename is not preserved anymore. The file name will be the same as the given name via `StyleFile->name()` 
  (if not specified the default value is equal to the filename without the extension)
- Style files are directly uploaded in the workspace without a first request to create an empty style

## [0.2.1] - 2020-01-02

### Fixed

- Style format identification on PHP 7.2 and 7.3

## [0.2.0] - 2018-10-23

### Changed

- File uploads to GeoServer now imposes a UTF-8 charset

## [0.1.0] - 2018-10-16

### Added

- Connection to a Geoserver instance, with authentication support
- Ability to identify Shapefile, GeoTiff and GeoPackage formats
- Create workspace or retrieve existing workspace details
- Create coverage stores and listing them
- Create data stores and listing them
- Upload files in various formats
- Manage styles in SLD format
