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
        Source::from_env();
    }

    /**
     * @Revs(10000)
     * @Iterations(3)
     */
    public function benchPhp()
    {
        Source::from_php(__DIR__ . "/../../../tests/api/Config/sources/source.php");
    }

    /**
     * @Revs(10000)
     * @Iterations(3)
     */
    public function benchJson()
    {
        Source::from_json(__DIR__ . "/../../../tests/api/Config/sources/source.json");
    }
}
