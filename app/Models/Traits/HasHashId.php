<?php

namespace App\Models\Traits;

use App\Services\HashIdService;
use Illuminate\Database\Eloquent\Model;

trait HasHashId
{
    public function getHashidAttribute(): string
    {
        return app(HashIdService::class)->encode($this->getKey());
    }

    // HANYA terima hash — tolak ID numerik
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $id = app(HashIdService::class)->decode((string) $value);
        if ($id === null) {
            return null; // 404 jika hash tidak valid
        }

        return $this->where($this->getRouteKeyName(), $id)->first();
    }
}
