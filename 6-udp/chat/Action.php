<?php

declare(strict_types=1);

final class Action
{
    private const ENTER = 'enter';
    private const LEAVE = 'leave';
    private const MESSAGE = 'message';

    private string $value;
    private ?string $message;

    public function __construct(string $value, string $message = null)
    {
        if (!in_array($value, [self::ENTER, self::LEAVE, self::MESSAGE])) {
            throw new InvalidArgumentException("Not valid command $value");
        }

        $this->value = $value;
        $this->message = $message;
    }

    public function isEnter(): bool
    {
        return $this->value === self::ENTER;
    }

    public function isLeave(): bool
    {
        return $this->value === self::LEAVE;
    }

    public static function leave(): self
    {
        return new self(self::LEAVE);
    }

    public static function enter(): self
    {
        return new self(self::ENTER);
    }

    public static function message(string $text): self
    {
        return new self(self::MESSAGE, $text);
    }

    public function toArray(string $clientName): array
    {
        return [
            'type' => $this->value,
            'name' => $clientName,
            'message' => $this->message,
        ];
    }
}
