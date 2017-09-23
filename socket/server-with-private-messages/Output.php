<?php


class Output {
	public static function warning($message)
	{
		return self::getColoredMessage("01;31", $message);
	}

	public static function info($message)
	{
		return self::getColoredMessage("0;32", $message);
	}

	public static function message($text)
	{
		return self::getColoredMessage("0;36", $text);
	}

	/**
	 * @param string $hexColor
	 * @param string $message
	 * @return string
	 */
	private static function getColoredMessage($hexColor, $message)
	{
		return "\033[{$hexColor}m{$message}\033[0m";
	}
}
