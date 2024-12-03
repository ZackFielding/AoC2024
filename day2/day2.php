<?php

// Mixing namespaces in the same file because it's AoC
namespace Enums {
    enum Direction {
        case Increasing;
        case Decreasing;
    }
}

namespace {
    $debug = $argv[1];
    $fileName = $argv[2];

    try {
        if (file_exists($fileName) && is_readable($fileName)) {
            $fp = fopen($fileName, "r");

            // will be: array<array<int>>
            $reports = array();

            while ( $line = fgets($fp) ) {
                $report = explode(" ", $line);
                array_walk($report, "toInt");
                $reports[] = $report;
            }

            // === Part 1 ====
            $p1 = numValidReports($reports, $debug);

            echo "Part 1 answer: {$p1}";
        } else {
            throw new Exception("File doesn't exist or is not readable: " . $fileName);
        }
    } catch (Exception $excp) {
        echo "Error: {$excp->getMessage()}";
    }

    // My system returns 2GB ... seems too high
    $peakBytes = memory_get_peak_usage(true);
    printf("\nScript used %u MB", $peakBytes / 1000);

    // ==================== functions ====================

    function numValidReports(array &$reports, $debug) : int
    {
        $validReportCount = 0;

        foreach($reports as $_k1 => $report) {

            $last = $report[0];
            $direction = null;
            $isValidReport = true;

            if ($debug) {
                echo "=== report ===\n";
            }

            for($i = 1, $len = count($report); $i < $len; $i++) {

                $diff = $last - $report[$i];

                if ($debug) {
                    echo "{$diff}\t";
                }

                $diffAbs = abs($diff);
                $validDiffMagnitude = $diffAbs >= 1 && $diffAbs <= 3;

                if ( ! $validDiffMagnitude ) {
                    $isValidReport = false;
                    break;
                }

                $localDirection = ($diff < 0) ? Enums\Direction::Increasing : Enums\Direction::Decreasing;
                $sameDirection = $direction === null || $direction === $localDirection;

                if ( ! $sameDirection) {
                    $isValidReport = false;
                    break;
                }

                // update for next level check
                $last = $report[$i];
                $direction = $localDirection;
            }

            if ($debug) {
                printf("\nIS VALID: %u\n", $isValidReport);
            }

            if ($isValidReport) {
                $validReportCount++;
            }
        }

        return $validReportCount;
    }

    function toInt(string $num): int
    {
        return (int) $num;
    }
}