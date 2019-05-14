# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.3 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#15](https://github.com/webimpress/coding-standard/pull/15) `PHP\DisallowFqn` - fixes importing FQN

- [#16](https://github.com/webimpress/coding-standard/pull/16) `WhiteSpace\ScopeIndent` - fixes code fixer for the case when boolean operator is at the end of the line

- [#17](https://github.com/webimpress/coding-standard/pull/17) fixes recognising types in `@method` PHPDoc tag. Fixes the following sniffs:
    - `Namespaces\UnusedUseStatement` - imported classes/interfaces are not removed when used within the tag,
    - `PHP\CorrectClassNameCase` - fixes caps in class/interface names used within the tag,
    - `PHP\DisallowFqn` - imports FQN used within the tag.

## 1.0.2 - 2019-05-12

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#14](https://github.com/webimpress/coding-standard/pull/14) `Formatting\StringClassReference` - fixes regular expression to check if string is valid FQCN

## 1.0.1 - 2019-05-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#5](https://github.com/webimpress/coding-standard/pull/5) `Methods\LineAfterSniff` - fixes adding empty line after method when next content is doc-block

- [#6](https://github.com/webimpress/coding-standard/pull/6) `Commenting\Placement` - fixes false-positive when space was required before comment in next line after PHP open tag

- [#7](https://github.com/webimpress/coding-standard/pull/7) `Commenting\DocComment` - fixes false-positive when new line was required before doc-block

- [#8](https://github.com/webimpress/coding-standard/pull/8) `Formatting\StringClassReference` - fixes checking if string is a valid class name before check if class/interface/trait exists

- [#9](https://github.com/webimpress/coding-standard/pull/9) `PHP\DisallowFqn` - fixes conflict with `Namespaces\UnusedUseStatement` when newly added import was deleted straight away

- [#10](https://github.com/webimpress/coding-standard/pull/10) `PHP\DisallowFqn` - fixes issue when backslash was removed from the beginning of the class name but there was no space before

- [#11](https://github.com/webimpress/coding-standard/pull/11) `Arrays\Format` - fixes issue with doc-block in arrays

- [#12](https://github.com/webimpress/coding-standard/pull/12) `Formatting\RedundantParentheses` - fixes false-positive for invokable classes treated as single expression

- [#13](https://github.com/webimpress/coding-standard/pull/13) `Namespaces\UnusedUseStatement` - fixes recognising classes in doc-block annotations

## 1.0.0 - 2019-03-07

Initial release.

### Added

- Everything.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.
