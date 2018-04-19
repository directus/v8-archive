<?php

return [
    'filters' => [
        'collection.insert:before' => new \Directus\Customs\Hooks\Products\BeforeInsertProducts()
    ]
];
