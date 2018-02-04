<?php

namespace App\Helper;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConsoleHelper
 * @package App\Helper
 */
class ConsoleHelper
{
    /** @var OutputInterface $output */
    public static $output;
    /** @var \DateTime $start */
    private static $start;
    /** @var \DateTime $stop */
    private static $stop;

    public static function header(): void
    {
        self::$start = new \DateTime();
        self::$output->writeln(
            [
                'Beginning script',
                'Script started, '.self::$start->format('Y-m-d H:i:s'),
                str_pad('', 80, '-', STR_PAD_BOTH),
            ]
        );
    }

    public static function footer(): void
    {
        self::$stop = new \DateTime();
        $difference = self::$start->diff(self::$stop);
        self::$output->writeln(
            [
                str_pad('', 80, '-', STR_PAD_BOTH),
                'Script ended, '.self::$stop->format('Y-m-d H:i:s'),
                'Processed time - '.$difference->format('%H:%I:%S'),
            ]
        );
    }

    public static function outputEmptyLine(): void
    {
        self::$output->writeln('');
    }

    /**
     * @param string $message
     */
    public static function outputMessage(string $message = ''): void
    {
        $message = $message !== '' ? '- '.$message : $message;
        $now = new \DateTime();
        self::$output->writeln(
            sprintf(
                '---- %s %s',
                $now->format('Y-m-d H:i:s'),
                $message
            )
        );
    }
}