# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## 0.1.1 - 2017-12-06
### Fixed
- Handling of `DateTimeInterface` value by`DateTimeUtcImmutableType::convertToPHPValue()`.
  
  This bug relates to DB diver which automatically converts database value to
  the instance of `DateTimeInterface`.
  
  - Previously error could occur for input value of type `DateTime`.
  - Time zone offset could be invalid on some cases.
  
## 0.1.0 - 2017-09-17
### Added
- `DateTimeUtcImmutableType`.