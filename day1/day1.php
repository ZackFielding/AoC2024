<?php

try {
    $debugFlag = (bool) $argv[1];
    $fileName = $argv[2];

    if (file_exists($fileName) && is_readable($fileName)) {
        $fp = fopen($fileName, 'r');

        if ($fp) {
            $lhs = array();
            $rhs = array();

            while ( $line = fgets($fp) ) {
                $nums = explode("   ", $line);
                $lhs[] = (int) $nums[0];
                $rhs[] = (int) $nums[1];
            }

            // === Part 1 ===
            $sortedLhs = $lhs;
            $sortedRhs = $rhs;

            // sort both arrays an asc order
            sort($sortedLhs, SORT_NUMERIC);
            sort($sortedRhs, SORT_NUMERIC);

            if ($debugFlag) {
                var_dump($sortedLhs);
                var_dump($sortedRhs);
            }

            $diff = array();

            for ($i = 0, $len = count($sortedLhs); $i < $len; $i++) {
                $diff[] = abs($sortedLhs[$i] - $sortedRhs[$i]);
            }

            $p1Ans = array_reduce($diff, "reducer", 0);

            echo "Part 1 answer: {$p1Ans}\n";

            // === Part 2 ===
            $rhsFreq = array();

            foreach($rhs as $key => $val) {
                if (array_key_exists($val, $rhsFreq)) {
                    $rhsFreq[$val] = ++$rhsFreq[$val];
                } else {
                    $rhsFreq[$val] = 1;
                }
            }

            $simTotal = 0;

            foreach($lhs as $key => $val) {
                $factor = array_key_exists($val, $rhsFreq) ? $rhsFreq[$val] : 0;
                $simTotal += $val * $factor;
            }

            echo "Part 2 answer: {$simTotal}";

        } else {
            throw new Exception("Failed to get file Resource");
        }
    }
} catch (Exception $exp) {
    echo "Error:", $exp->getMessage();
}

function reducer(int $acc, int $cur): int
{
    return $acc + $cur;
}