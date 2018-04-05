<?php
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesBeforeColon 1
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesAfterColon 2
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesAfterNullable 0

function a() : int {}
function b(): ?  int {}
function c()
    :int{}
function d():
    ?int{}

// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesBeforeColon 2
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesAfterColon 0
// @phpcs:set WebimpressCodingStandard.Formatting.ReturnType spacesAfterNullable 1

function e() : bool {}
function f(): ?  bool {}
function g()
    :bool{}
function h():
    ?bool {}
