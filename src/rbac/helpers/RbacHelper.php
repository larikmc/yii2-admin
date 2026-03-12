<?php

namespace larikmc\admin\rbac\helpers;

use yii\rbac\Item;

class RbacHelper
{
    public static function itemTypeLabel(int $type): string
    {
        return $type === Item::TYPE_ROLE ? 'Role' : 'Permission';
    }

    public static function sortItems(array $items): array
    {
        uasort($items, static fn($a, $b) => strcasecmp($a->name, $b->name));

        return $items;
    }
}
