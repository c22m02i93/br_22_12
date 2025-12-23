<?php

declare(strict_types=1);

$searchUrl = getenv('SMOKE_SEARCH_URL') ?: 'http://localhost/api/search.php?q=test';

function fetchSearchResponse(string $url): string
{
    $response = @file_get_contents($url);
    if ($response === false) {
        throw new RuntimeException('Unable to reach search endpoint: ' . $url);
    }

    return $response;
}

try {
    $rawResponse = fetchSearchResponse($searchUrl);
    $decoded = json_decode($rawResponse, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new RuntimeException('Search response is not valid JSON: ' . json_last_error_msg());
    }

    printf("search.php response decoded successfully. Items: %d\n", is_countable($decoded) ? count($decoded) : 0);
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, '[smoke] ' . $e->getMessage() . PHP_EOL);
    fwrite(STDERR, "This is a stub check. Point SMOKE_SEARCH_URL to a running instance of api/search.php.\n");
    exit(1);
}
