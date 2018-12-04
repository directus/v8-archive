<?php

return [
    'filters' => [
        'item.create:before' => new \Directus\Extensions\Custom\Hooks\Products\BeforeInsertProducts()
    ]
];
