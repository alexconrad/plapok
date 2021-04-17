<?php
declare(strict_types=1);

namespace PlaPok\Services;


class SessionService
{

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }


}
