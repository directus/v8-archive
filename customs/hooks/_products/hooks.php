<?php

return [
    'filters' => [
        'table.insert:before' => new \Directus\Customs\Hooks\Products\BeforeInsertProducts()
    ]
];
