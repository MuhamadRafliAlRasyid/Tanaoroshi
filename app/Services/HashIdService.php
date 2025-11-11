<?php

namespace App\Services;

use Hashids\Hashids;

class HashIdService
{
    protected Hashids $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(config('app.key'), 10);
    }

    public function encode(int $id): string
    {
        return $this->hashids->encode($id);
    }

    public function decode(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return !empty($decoded) ? $decoded[0] : null;
    }
}
