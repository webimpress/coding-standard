# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.1.0 - TBD

### Added

- [#35](https://github.com/webimpress/coding-standard/pull/35) adds `PHP\StaticCallback` sniff which detects closures and check if these can be marked as static

- [#36](https://github.com/webimpress/coding-standard/pull/36) adds `PHP\DisallowCurlyOffsetAccessBrace` sniff which detects array and string offset access using curly brackets.
  This functionality is deprecated as of PHP 7.4 so sniff can be useful when providing compatibility with PHP 7.4.

- [#38](https://github.com/webimpress/coding-standard/pull/38) adds `ControlStructures\RedundantCase` sniff which detects redundant cases within a switch control structure

- [#41](https://github.com/webimpress/coding-standard/pull/41) adds `ControlStructures\DefaultAsLast` sniff which requires `default` case to be last case in a switch control structure

- [#39](https://github.com/webimpress/coding-standard/pull/39) adds `Arrays\DuplicateKey` sniff which detects duplicated keys in arrays

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.6 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#43](https://github.com/webimpress/coding-standard/pull/43) fixes `WhiteSpace\ScopeIndent` sniff - case with object calls within ternary operator

- [#40](https://github.com/webimpress/coding-standard/pull/40) fixes `PHP\RedundantSemicolon` sniff to remove redundant semicolon after colon and goto label 

## 1.0.5 - 2019-10-06

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#34](https://github.com/webimpress/coding-standard/pull/34) fixes removing unused imports in files without namespaces

## 1.0.4 - 2019-07-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#30](https://github.com/webimpress/coding-standard/pull/30) `Functions\Param` - fixes duplicated error and fixer conflict

- [#31](https://github.com/webimpress/coding-standard/pull/31) `WhiteSpace\BraceBlankLine` - fixes fixer conflict with empty structures

- [#33](https://github.com/webimpress/coding-standard/pull/33) fixes sorting issue of imported class and traits usage. Fixes the following sniffs:
    - `Classes\AlphabeticallySortedTraits`
    - `Namespaces\AlphabeticallySortedUses`

## 1.0.3 - 2019-05-28

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

- [#18](https://github.com/webimpress/coding-standard/pull/18) `Commenting\DocComment` - fixes requiring content straight after doc-block - allows empty line when next content is another doc-block

- [#19](https://github.com/webimpress/coding-standard/pull/19) `Commenting\DocComment` - fixes issue with doc-block after colon (for example in switch statement) - empty line is no longer required before doc-block in that case

- [#20](https://github.com/webimpress/coding-standard/pull/20) `Functions\ReturnType` - fixes false-positive error when function may return `$this`

- [#21](https://github.com/webimpress/coding-standard/pull/21) fixes regular expression to check class name (type). Fixes the following sniffs:
    - `Commenting\TagWithType` - types in doc-block comments,
    - `Functions\Param` - param typehint and type within `@param` tag,
    - `Functions\ReturnType` - return type declaration and type within `@return` tag.

- [#22](https://github.com/webimpress/coding-standard/pull/22) fixes recognising parameter types and return type declaration with PHPDocs tags. Affects the following sniffs:
    - `Functions\Param` - param typehint and type within `@param` tag,
    - `Functions\ReturnType` - return type declaration and type within `@return` tag.

- [#23](https://github.com/webimpress/coding-standard/pull/23) `Functions\ReturnType` - fixes recognising yoda comparison in return statement and type of returned value

- [#24](https://github.com/webimpress/coding-standard/pull/24) fixes error in fixer when recognised type was invalid. Affects the following sniffs:
    - `PHP\CorrectClassNameCase`
    - `PHP\DisallowFqn`

- [#25](https://github.com/webimpress/coding-standard/pull/25) fixes type suggestions, allows `self` and `parent` to be used as specification for complex types (like `object` or class/interface)

- [#26](https://github.com/webimpress/coding-standard/pull/26) `Commenting\DocComment` fixes indents detection before closing brackets in doc-block comments

- [#27](https://github.com/webimpress/coding-standard/pull/27) `Commenting\TagWithType` fixes ordering types in PHPDoc tags

- [#28](https://github.com/webimpress/coding-standard/pull/28) `WhiteSpace\ScopeIndent` fixes false-positive

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
