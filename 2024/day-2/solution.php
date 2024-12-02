<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $result = 0;

        $input = $this->input->load();
        foreach ($input as $line) {
            $report = explode(' ', $line);
            if ($this->isSafe(1, 3, $report)) {
                $result++;
            }
        }
        return $result;
    }

    public function second(): int
    {
        $result = 0;
        $input = $this->input->load();

        foreach ($input as $line) {
            $report = explode(' ', $line);
            if ($this->isSafe(1, 3, $report)) {
                $result++;
            } else {
                foreach ($report as $key => $_) {
                    $alteredReport = $this->removeLevel($report, $key);
                    if ($this->isSafe(1, 3, $alteredReport)) {
                        $result++;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    private function removeLevel(array $report, int $key): array
    {
        unset($report[$key]);
        return array_values($report);
    }

    private function isSafe(
        int $minDifference,
        int $maxDifference,
        array $report
    ): bool {
        $parity = $report[0] - $report[1] > 0 ? 1 : -1;
        for ($i = 0; $i < sizeof($report)-1; $i++) {
            $diff = (int)$report[$i] - (int)$report[$i+1];

            if ($diff*$parity < 0) {
                return false;
            }
            if ($diff*$parity > $maxDifference || $diff*$parity < $minDifference) {
                return false;
            }
        }
        return true;
    }
}
