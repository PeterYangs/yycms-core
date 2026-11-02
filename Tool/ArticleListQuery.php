<?php

namespace Ycore\Tool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/** Keep large article fields out of the temporary table used to select a short list. */
class ArticleListQuery
{
    public static function get(Builder $query): Collection
    {
        // Unbounded callers retain their original query and memory behaviour.
        if (!$query->getQuery()->limit) {
            return $query->get();
        }

        $key = $query->getModel()->getQualifiedKeyName();
        // Apply every visibility scope and predicate BEFORE the original order/limit.
        $ids = (clone $query)->setEagerLoads([])->get([$key])->modelKeys();
        if (!$ids) {
            return $query->getModel()->newCollection();
        }

        // Retain predicates/scopes on hydration as well (including concurrent unpublishing).
        $hydrate = (clone $query)->whereKey($ids)->reorder();
        $hydrate->getQuery()->limit = null;
        $hydrate->getQuery()->offset = null;
        $items = $hydrate->get()->keyBy($query->getModel()->getKeyName());
        $ordered = [];
        foreach ($ids as $id) {
            if ($items->has($id)) {
                $ordered[] = $items->get($id);
            }
        }
        return $query->getModel()->newCollection($ordered);
    }
}
