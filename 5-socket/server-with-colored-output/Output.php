<?php

final class Output
{
    public function warning(string $message): string
    {
        return $this->getColoredMessage("01;31", $message);
    }

    public function info(string $message): string
    {
        return $this->getColoredMessage("0;32", $message);
    }

    public function message(string $name, string $text): string
    {
        $name = $this->getColoredMessage("0;36", $name);
        return "$name: $text";
    }

    private function getColoredMessage(string $hexColor, string $message): string
    {
        return "\033[{$hexColor}m{$message}\033[0m";
    }
}
