<?php
declare(strict_types=1);

namespace YoCierge;

class Person {
    public function __construct(
        public string $id,
        public string $name,
        public string $dateOfBirth,
        public array  $faxes,
    ) {}
}
