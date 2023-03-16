<?php

declare(strict_types=1);

namespace App\ValueObject;

use DateTimeImmutable as DateTimeIm;

class BirthDate
{
    private const DATE_FORMAT = 'Y-m-d';

    public function __construct(public readonly string $value)
    {
        if (!$this->isValidDateString($this->value)) {
            throw new \InvalidArgumentException('Невалидная дата рождения');
        }
    }

    public function getValueAsDate(): DateTimeIm
    {
        // Знак | нужен, чтобы информация о годе, месяце, дне, минуте, секунде,
        // доле и таймзоне не устанавливались текущими, если нет в переданной
        // строке для парсинга, а подставлялись нулевые значения.
        // https://www.php.net/manual/en/datetime.createfromformat.php
        return DateTimeIm::createFromFormat(
            self::DATE_FORMAT . '|',
            $this->value
        );
    }

    /**
     * Проверяет, является ли строка валидной датой в нужном формате,
     * учитывая валидность типа неправильных дат как 30 февраля.
     */
    private function isValidDateString(
        ?string $dateTimeString,
        string $dateTimeFormat = self::DATE_FORMAT
    ): bool {
        $dateTime = DateTimeIm::createFromFormat(
            $dateTimeFormat,
            $dateTimeString
        );

        return $dateTime
            && $dateTime->format($dateTimeFormat) === $dateTimeString;
    }
}
