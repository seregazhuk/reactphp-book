<?php

declare(strict_types=1);

namespace Chat;

final class Message
{
    private const COLOR_RED = "01;31";
    private const COLOR_GREEN = "0;32";
    private const COLOR_CYAN = "0;36";

    public string $text;
    private ?string $from;
    private ?string $to = null;

    private function __construct(string $text, string $from = null)
    {
        $this->from = $from;

        preg_match('/^@(\w+):\s*(.+)/s', $text, $matches);
        if (!empty($matches)) {
            $this->to = $matches[1];
            $this->text = $matches[2];
        } else {
            $this->text = $text;
        }
    }

    public static function from(string $text, string $from): self
    {
        return new self($text, $from);
    }

    public static function warning(string $text): self
    {
        return self::colored($text, self::COLOR_RED);
    }

    public static function info(string $text): self
    {
        return self::colored($text, self::COLOR_GREEN);
    }

    private static function colored(string $text, string $hexColor): self
    {
        return new self(self::coloredString($text, $hexColor));
    }

    private static function coloredString(string $text, string $hexColor): string
    {
        return "\033[{$hexColor}m{$text}\033[0m";
    }

    public function toString(): string
    {
        if ($this->from === null) {
            return $this->text;
        }

        $text = "{$this->from}: {$this->text}";
        if ($this->to !== null) {
            $text = self::coloredString($text, self::COLOR_CYAN);
        }

        return $text;
    }

    public function isPublic(): bool
    {
        return $this->to === null;
    }

    public function to(): ?string
    {
        return $this->to;
    }
}
