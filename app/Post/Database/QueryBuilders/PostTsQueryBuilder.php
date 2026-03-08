<?php

namespace App\Post\Database\QueryBuilders;

class PostTsQueryBuilder
{
    public function build(string $search): ?string
    {
        $terms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
        if ($terms === false || $terms === []) {
            return null;
        }

        $sanitized = [];

        foreach ($terms as $term) {
            $withoutApostrophe = preg_replace("/['\x{2019}\x{02BC}]/u", '', $term);
            $cleaned = preg_replace('/[^\p{L}\p{N}\-]/u', '', $withoutApostrophe);
            if (is_string($cleaned) && $cleaned !== '') {
                $sanitized[] = $cleaned;
            }
        }

        if ($sanitized === []) {
            return null;
        }

        return implode(' & ', array_map(fn (?string $t): string => ($t ?? '').':*', $sanitized));
    }
}
