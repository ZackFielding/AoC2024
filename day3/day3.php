<?php

namespace {
    $debug = $argv[1];
    $fileName = $argv[2];

    if (file_exists($fileName) && is_readable($fileName)) {
        $regex = "/mul\((\d+),(\d+)\)/";

        $resource = fopen($fileName, "r");

        if ($resource) {

            $input = "";
            while ($line = fgets($resource) ) {
                $input .= $line;
            }

            $matches = array();

            preg_match_all($regex, $input, $matches);

            [, $lhs, $rhs] = $matches;

            function toInt(string &$val) {
                $val = (int) $val;
            }

            array_walk($lhs, "toInt");
            array_walk($rhs, "toInt");

            if ($debug) {
                var_dump($lhs, $rhs);
            }

            $total = 0;
            for ($i = 0, $len = count($lhs); $i < $len; $i++) {
                $total += $lhs[$i] * $rhs[$i];
            }

            echo "Part 1 answer: {$total}\n";

            // ----
        }
    }
}