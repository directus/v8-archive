<?php

interface CustomSchemaDefine {
    function getCustomConfigPath();
    function addToSchema();
    function readCustomSchema();
}
