<?php

namespace App\Http\Traits;

use RuntimeException;
use Illuminate\Support\Facades\Http;

trait BlockFrostTrait
{
    public function callBlockFrost(string $endpoint): array
    {
        $requestUrl = sprintf(
            'https://cardano-%s.blockfrost.io/api/v0/%s',
            env('CARDANO_NETWORK'),
            $endpoint,
        );

        $response = Http::withHeaders([
            'project_id' => env('BLOCK_FROST_PROJECT_ID'),
        ])->get($requestUrl);

        if ($response->successful()) {
            return $response->json();
        }

        throw new RuntimeException('Blockfrost api call error: ' . $response->json('message'));
    }
}
