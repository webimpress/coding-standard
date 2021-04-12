# Webimpress Coding Standard

[![Unit Tests](https://github.com/webimpress/coding-standard/actions/workflows/phpunit.yml/badge.svg)](https://github.com/webimpress/coding-standard/actions/workflows/phpunit.yml)
[![Coding Standards](https://github.com/webimpress/coding-standard/actions/workflows/phpcs.yml/badge.svg)](https://github.com/webimpress/coding-standard/actions/workflows/phpcs.yml)
[![Coverage Status](https://coveralls.io/repos/github/webimpress/coding-standard/badge.svg?branch=master)](https://coveralls.io/github/webimpress/coding-standard?branch=master)

## Installation

1. Install the module via composer by running:

   ```bash
   $ composer require --dev webimpress/coding-standard
   ```

2. Add composer scripts into your `composer.json`:

   ```json
   "scripts": {
       "cs-check": "phpcs",
       "cs-fix": "phpcbf"
   }
   ```

3. Create file `phpcs.xml` on base path of your repository with content:

   ```xml
   <?xml version="1.0"?>
   <ruleset name="Webimpress Coding Standard"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="./vendor/squizlabs/php_codesniffer/phpcs.xsd">
       <rule ref="./vendor/webimpress/coding-standard/ruleset.xml"/>

       <!-- Paths to check -->
       <file>config</file>
       <file>src</file>
       <file>test</file>
   </ruleset>
   ```

As shown above you must define locations or files you want to be
checked and fixed by code sniffer. For a reference please see:
https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-Ruleset


## Usage

* To run checks only:

  ```bash
  $ composer cs-check
  ```

* To automatically fix many CS issues:

  ```bash
  $ composer cs-fix
  ```
