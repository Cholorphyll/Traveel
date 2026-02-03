<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class OpenAIHelper
{
    public static function getEmbedding($text)
    {
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => 'text-embedding-3-small',
                'input' => $text,
            ]);

        return $response['data'][0]['embedding'];
    }
}
