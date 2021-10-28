# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.2.4 - TBD

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

## 1.2.3 - 2021-10-28

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#153](https://github.com/webimpress/coding-standard/pull/153) fixes calculating spaces before double arrow in arrays - `Arrays\DoubleArrow` sniff.

## 1.2.2 - 2021-04-12

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#143](https://github.com/webimpress/coding-standard/pull/143) fixes support for [PHP 8 `match`](https://www.php.net/match) expression.

## 1.2.1 - 2021-01-11

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#139](https://github.com/webimpress/coding-standard/pull/139) fixes calculating spaces before double arrow in arrays - `Arrays\DoubleArrow` sniff.

## 1.2.0 - 2020-11-27

### Added

- [#111](https://github.com/webimpress/coding-standard/pull/111) adds support for PHP 8.0.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [#134](https://github.com/webimpress/coding-standard/pull/134) removes support for PHP 7.1 and 7.2.

### Fixed

- [#133](https://github.com/webimpress/coding-standard/pull/133) fixes crashing when parent class does not exist in `NamingConventions\Exception` sniff.

## 1.1.7 - 2020-11-27

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#132](https://github.com/webimpress/coding-standard/pull/132) fixes invalid placement of comments when PHP closing tag is after the comment - `Commenting\Placement`.

## 1.1.6 - 2020-10-14

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#121](https://github.com/webimpress/coding-standard/pull/121) fixes false-positive on importing class constant with global constant names - `PHP\ImportInternalConstant`.

- [#122](https://github.com/webimpress/coding-standard/pull/122) fixes false-positive on unused use statement for constant used with bitwise operators - `Namespaces\UnusedUseStatement`.

## 1.1.5 - 2020-04-06

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#93](https://github.com/webimpress/coding-standard/pull/93) fixes importing FQCN when namespace is in use in `PHP\DisallowFqn`.

## 1.1.4 - 2020-02-20

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#90](https://github.com/webimpress/coding-standard/pull/90) fixes false-positive in `Formatting\RedundantParentheses` sniff when using nested ternary with `instanceof` condition.

## 1.1.3 - 2020-02-09

### Added

- Nothing.

### Changed

- [#87](https://github.com/webimpress/coding-standard/pull/87) bumps squizlabs/php_codesniffer dependency to version ^3.5.4 which contains fix for correct detecting indices in arrays.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#86](https://github.com/webimpress/coding-standard/pull/86) fixes false-positive in `PHP\DisallowFqn` for FQN in PHPDocs.

## 1.1.2 - 2020-01-25

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#74](https://github.com/webimpress/coding-standard/pull/74) fixes false-positive in `Namespaces\UnusedUseStatement` for multiple trait usages.

- [#75](https://github.com/webimpress/coding-standard/pull/75) allows `null` value for properties with nullable type declaration (PHP 7.4+). 

- [#76](https://github.com/webimpress/coding-standard/pull/76) fixes recognising annotations in property PHPDocs: `Commenting\TagWithType` sniff. 

- [#78](https://github.com/webimpress/coding-standard/pull/78) fixes false-positive for mixed type in PHPDoc return tag: `Functions\ReturnType` sniff. 

## 1.1.1 - 2019-12-27

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#70](https://github.com/webimpress/coding-standard/pull/70) fixes `Namespaces\AlphabeticallySortedUses` sniff to not remove additional content between use statements.

## 1.1.0 - 2019-12-08

### Added

- [#35](https://github.com/webimpress/coding-standard/pull/35) adds `PHP\StaticCallback` sniff which detects closures and check if these can be marked as static

- [#36](https://github.com/webimpress/coding-standard/pull/36) adds `PHP\DisallowCurlyOffsetAccessBrace` sniff which detects array and string offset access using curly brackets.
  This functionality is deprecated as of PHP 7.4 so sniff can be useful when providing compatibility with PHP 7.4.

- [#38](https://github.com/webimpress/coding-standard/pull/38) adds `ControlStructures\RedundantCase` sniff which detects redundant cases within a switch control structure

- [#41](https://github.com/webimpress/coding-standard/pull/41) adds `ControlStructures\DefaultAsLast` sniff which requires `default` case to be last case in a switch control structure

- [#39](https://github.com/webimpress/coding-standard/pull/39) adds `Arrays\DuplicateKey` sniff which detects duplicated keys in arrays

- [#42](https://github.com/webimpress/coding-standard/pull/42) adds requiring camelCase names for class members and variables used in strings - extended sniff `NamingConventions\ValidVariableName`.
  Disallowed are two capital letters next to each other (strict mode).

- [#60](https://github.com/webimpress/coding-standard/pull/60) extends `Classes\TraitUsage` sniff to check if traits declarations are on the top of the class.
  Traits must be specified before constants, properties and methods.

- [#45](https://github.com/webimpress/coding-standard/pull/45) adds `Classes\ConstBeforeProperty` sniff to require constant definitions in classes and interfaces before properties and methods

- [#46](https://github.com/webimpress/coding-standard/pull/46) adds `Classes\PropertyBeforeMethod` sniff to require property definitions in classes before methods

- [#47](https://github.com/webimpress/coding-standard/pull/47) adds `Commenting\TagName` sniff which checks if PHPDoc tags have additional characters at the end of the name.
  By default `:` and `;` are disallowed and removed by fixer, but the list of disallowed characters can be configured by option `disallowedEndChars`

- [#48](https://github.com/webimpress/coding-standard/pull/48) adds configuration option `nullPosition` to `Commenting\TagWithType` sniff. Default value is `first` to keep backward compatibility.
  The other allowed value is `last` so then `null` values in type list is at the last position.

- [#49](https://github.com/webimpress/coding-standard/pull/49) adds `Commenting\DisallowEmptyComment` sniff to detect empty comments and multiple empty lines in comments

- [#50](https://github.com/webimpress/coding-standard/pull/50) adds check for open and close of doc block comment in `Commenting\DocComment`.
  Only short version is allowed: `/**` and `*/`. Additional asterisk are disallowed.

- [#51](https://github.com/webimpress/coding-standard/pull/51) adds check for blank lines and comments before arrow in arrays in `Array\Format` sniff.
  Arrow must be after the index value, can be in new line, but any additional lines or comments are disallowed. 

- [#54](https://github.com/webimpress/coding-standard/pull/54) adds `Namespaces\UniqueImport` sniff to detect if class/function/constant is imported only once.
  Sniff checks also if the name is used only once. The same name can be used for class/function/constant, and constant names are case sensitive.

- [#37](https://github.com/webimpress/coding-standard/pull/37) adds additional sniffs to `WebimpressCodingStandard` ruleset:
  - `Generic.ControlStructures.DisallowYodaConditions`,
  - `Squiz.Operators.IncrementDecrementUsage`,
  - `Squiz.PHP.DisallowMultipleAssignments` (with `Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure` exclusion).

- [#59](https://github.com/webimpress/coding-standard/pull/59) extends `Commenting\DocComment` sniff to check if every line of DocBlock comment starts with `*`

- [#53](https://github.com/webimpress/coding-standard/pull/53) adds support for use groups in `Namespaces\UnusedUseStatement` sniff

- [#58](https://github.com/webimpress/coding-standard/pull/58) adds property type declaration (PHP 7.4+). The following sniffs are affected:
  - `Commenting\PropertyAnnotation` - ensure that type is skipped when detecting the comment before property,
  - `Commenting\VariableComment` - add multiple checks to ensure type declaration is consistent with type provided in PHPDoc (`@var` tag). In case type declaration is provided and PHPDoc does not contain any additional information, `@var` tag can be omitted.                                     

- [#62](https://github.com/webimpress/coding-standard/pull/62) adds support for `Iterator` type in `Functions\Param` and `Functions\ReturnType` sniffs

- [#62](https://github.com/webimpress/coding-standard/pull/62) adds check for return type of function with `yield`. Generators may only declare a return type of `Generator`, `Iterator`, `Traversable` or `iterable`.

- [#63](https://github.com/webimpress/coding-standard/pull/63) adds ability to align array arrows when in new line. To use it, there is an example configuration:
  ```
  // configuration of double arrow alignment
  WebimpressCodingStandard.Arrays.DoubleArrow.maxPadding = 50
  WebimpressCodingStandard.Arrays.DoubleArrow.ignoreNewLineArrayArrow = false

  // ignore indent or double arrow when at the beginning of the line
  WebimpressCodingStandard.WhiteSpace.ScopeIndent.ignoreNewLineArrayArrow = true

  // ignore spacing before double arrow (so we can have more than one space)
  WebimpressCodingStandard.WhiteSpace.OperatorAndKeywordSpacing.ignoreSpacingBeforeAssignments = true
  ```

- [#67](https://github.com/webimpress/coding-standard/pull/67) adds support for PHP 7.4 arrow functions.

### Changed

- [#42](https://github.com/webimpress/coding-standard/pull/42) changes `NamingConventions\ValidVariableName` to require variable names be in strict camelCase. It means two capital letters next to each other are not allowed.

- [#44](https://github.com/webimpress/coding-standard/pull/44) changes `PSR1.Methods.CamelCapsMethodName` with `Generic.NamingConventions.CamelCapsFunctionName` so from now method names must be in strict camelCas. It means two capital letters next to each other are not allowed.

- [#37](https://github.com/webimpress/coding-standard/pull/37) updates the `squizlabs/php_codesniffer` dependency to `^3.5.2`

- [#61](https://github.com/webimpress/coding-standard/pull/61) replaces `Squiz.WhiteSpace.OperatorSpacing` with `WebimpressCodingStandard.WhiteSpace.OperatorAndKeywordSpacing` sniff.
  This sniff still extends `Squiz.WhiteSpace.OperatorSpacing` but check additional tokens: `as` and `insteadof` and Logical Operators.
  It also disallows mor then one empty line before operators, and for `as`, `insteadof`, and `instanceof` requires single space before and after.

### Deprecated

- Nothing.

### Removed

- [#42](https://github.com/webimpress/coding-standard/pull/42) excludes `PSR2.Classes.PropertyDeclaration.Underscore` check, as it is now covered by `NamingConventions\ValidVariableName` sniff

### Fixed

- [#51](https://github.com/webimpress/coding-standard/pull/51) fixes multiple cases when empty line before comment in array was not allowed

- [#53](https://github.com/webimpress/coding-standard/pull/53) reworks implementation of `Namespaces\UnusedUseStatement` sniff which solves numerous issues with detecting if class/function/constant is used within the file 

## 1.0.6 - 2019-11-13

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

- [#55](https://github.com/webimpress/coding-standard/pull/55) fixes `Namespaces\AlphabeticallySortedUses` sniff to work with files without namespaces

- [#56](https://github.com/webimpress/coding-standard/pull/56) fixes Annotations sniffs for specific cases:
  - `Commenting\ClassAnnotation` sniff - `final` classes.
  - `Commenting\MethodAnnotation` sniff - `final` methods.
  - `Commenting\PropertyAnnotation` sniff - properties defined with `var`.

- [#57](https://github.com/webimpress/coding-standard/pull/57) fixes parsing content of `@param` and `@var` tags with multiple spaces before variable name. Affects the following sniffs:
  - `Commenting\TagWithType`,
  - `Functions\Param`.

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
