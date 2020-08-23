#!/bin/bash

RULESET="src/WebimpressCodingStandard/ruleset.xml"
LOCAL="ruleset.xml"

set -e

wget -nv -N -P tmp/ "https://github.com/squizlabs/PHP_CodeSniffer/raw/master/phpcs.xsd"
wget -nv -N -P tmp/ "https://www.w3.org/2012/04/XMLSchema.xsd"

xmllint --noout --schema tmp/XMLSchema.xsd tmp/phpcs.xsd
xmllint --noout --schema tmp/phpcs.xsd "$RULESET"
diff -B "$RULESET" <(XMLLINT_INDENT="    " xmllint --format "$RULESET")

xmllint --noout --schema tmp/phpcs.xsd "$LOCAL"
diff -B "$LOCAL" <(XMLLINT_INDENT="    " xmllint --format "$LOCAL")

rm -Rf tmp/
