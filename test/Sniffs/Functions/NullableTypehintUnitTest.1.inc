<?php // @phpcs:set WebimpressCodingStandard.Functions.NullableTypehint withQuestionMark false

function withoutQuestionMarks(
    $a,
    ?int $b,
    \DateTime $c = null,
    ?bool $d = null,
    string $e = 'NULL',
    string $f = NULL
) {
}

$closure = function (
    $a,
    ?int $b,
    \DateTime $c = null,
    ?bool $d = null,
    string $e = 'NULL',
    string $f = NULL
) {
};
