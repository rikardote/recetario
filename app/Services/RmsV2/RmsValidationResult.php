<?php

namespace App\Services\RmsV2;

/**
 * Result of parsing and validating an RMS v2.0 document.
 * Contains either a parsed recipe array OR a list of validation errors.
 */
class RmsValidationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?array $data = null,
        public readonly array $errors = [],
    ) {}

    public static function success(array $data): self
    {
        return new self(valid: true, data: $data);
    }

    public static function failure(array $errors): self
    {
        return new self(valid: false, errors: $errors);
    }
}
