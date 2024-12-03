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
            [$p1, $invalidIndices] = numValidReports($reports);

            echo "Part 1 answer: {$p1}";

            // === Part 2 ===
            $numValidDampened = numValidReportsDampened($reports, $invalidIndices);
            $p2 = $p1 + $numValidDampened;

            echo "\nPart 2 answer: {$p2}";
        } else {
            throw new Exception("File doesn't exist or is not readable: " . $fileName);
        }
    } catch (Exception $excp) {
        echo "Error: {$excp->getMessage()}";
    }

    // ==================== functions ====================

    /**
     * Returns the number of valid reports according the constraints of Part 1, and the indices of invalid reports
     * (used for part 2).
     *
     * @param array $reports
     * @return array
     */
    function numValidReports(array &$reports) : array
    {
        $validReportCount = 0;
        $invalidIndices = array();

        for ($i = 0, $len = count($reports); $i < $len; $i++) {
            if (isValidReport($reports[$i])) {
                $validReportCount++;
            } else {
                $invalidIndices[] = $i;
            }
        }

        return array($validReportCount, $invalidIndices);
    }

    /**
     * Returns the number of previously invalid reports that are now valid when dampened.
     *
     * @param array $reports
     * @param array $invalidIndices
     * @return int
     */
    function numValidReportsDampened(array $reports, array &$invalidIndices): int
    {
        $numValidReports = 0;

        foreach($invalidIndices as $_k => $idx) {
            // iteratively remove one element until either: (a) the report is valid or (b) end of report
            $report = $reports[$idx];

            for ($i = 0, $len = count($report); $i < $len; $i++) {
                // copy & splice
                $splicedReport = $report;
                array_splice($splicedReport, $i, 1);

                if (isValidReport($splicedReport)) {
                    $numValidReports++;
                    break;
                }
            }
        }

        return $numValidReports;
    }

    /**
     * Iterates across all levels of report, returning `true` if report is valid and `false` otherwise.
     *
     * @param array $report array of levels representing the report
     * @return bool `true` if report is valid
     */
    function isValidReport(array  &$report) : bool
    {
        global $debug;
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

        return $isValidReport;
    }

    function toInt(string $num): int
    {
        return (int) $num;
    }
}