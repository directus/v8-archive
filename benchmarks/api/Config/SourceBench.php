<?php

use Directus\Config\Source;


class SourceBench
{
    /**
     * @Revs(10000)
     * @Iterations(3)
     */
    public function benchEnv()
    {
        $_ENV = [
            "A" => 1
        ];
        Context::from_env();
    }

    /**
     * @Revs(10000)
     * @Iterations(3)
     */
    public function benchPhp()
    {
        Context::from_php(__DIR__ . "/../../../tests/api/Config/sources/source.php");
    }

    /**
     * @Revs(10000)
     * @Iterations(3)
     */
    public function benchJson()
    {
        Context::from_json(__DIR__ . "/../../../tests/api/Config/sources/source.json");
    }
}
