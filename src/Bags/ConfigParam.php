<?php

declare(strict_types=1);

namespace MarkHelp\Bags;

class ConfigParam
{
    private string $type;

    private ?string $value;

    private bool $required;

    public function __construct(string $type, ?string $value, bool $required = false)
    {
        $this->type = $type;
        $this->value = $value;
        $this->required = $required;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): ?string
    {
        return $this->value;
    }

    public function required(): bool
    {
        return $this->required;
    }
}
