<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $result = 0;

        $machines = $this->parseInput($input);

        foreach ($machines as $machine) {
            $solutions = $this->getSolutions(
                $machine['v1'],
                $machine['v2'],
                $machine['target']
            );
            $result += $this->getMinTokens($solutions);
        }

        return $result;
    }


    public function second(): int
    {
        $input = $this->input->load();
        $result = 0;

        $machines = $this->parseInput($input, true);

        foreach ($machines as $machine) {
            $solutions = $this->solveLinearEquation(
                $machine['v1'],
                $machine['v2'],
                $machine['target']
            );
            $result += $this->getMinTokens($solutions);
        }

        return $result;
    }

    private function parseInput(array $input, $isPartTwo = false): array
    {
        $chunks = [];
        $lines = [];
        for ($i = 1; $i <= sizeof($input); $i++) {
            if ($i % 4 === 0) {
                $chunks[] = $lines;
                $lines = [];
                continue;
            }
            $lines[] = $input[$i-1];
        }
        $chunks[] = $lines;

        $machines = [];
        foreach ($chunks as $machine) {
            $machine[0] = str_replace(["Button A: ", ",", "X+", "Y+"], "", $machine[0]);
            $machine[1] = str_replace(["Button B: ", ",", "X+", "Y+"], "", $machine[1]);
            $machine[2] = str_replace(["Prize: ", ",", "X=", "Y="], "", $machine[2]);

            $valuesA = explode(" ", $machine[0]);
            $valuesB = explode(" ", $machine[1]);
            $valuesTarget = explode(" ", $machine[2]);
            $machines[] = [
                "v1" => [(int)$valuesA[0], (int)$valuesA[1]],
                "v2" => [(int)$valuesB[0], (int)$valuesB[1]],
                "target" => [
                    $isPartTwo ? (int)$valuesTarget[0] + 10000000000000 : (int)$valuesTarget[0],
                    $isPartTwo ? (int)$valuesTarget[1] + 10000000000000 : (int)$valuesTarget[1]
                ]
            ];
        }
        return $machines;
    }

    private function getSolutions(array $v1, array $v2, array $target): array
    {
        $solutions = [];

        // takes too long for part two
        for ($i = 0; $i <= 100; $i++)
        {
            $diffX = $target[0] - $i*$v1[0];
            $diffY = $target[1] - $i*$v1[1];
            if ($diffX % $v2[0] === 0 && $diffY % $v2[1] === 0)
            {
                if ($diffX / $v2[0] === $diffY / $v2[1]) {
                    $solutions[] = [$i, $diffX / $v2[0]];
                }
            }
        }
        return $solutions;
    }

    private function solveLinearEquation(array $v1, array $v2, array $target): array
    {
        $line1 = [$v1[0], $v2[0], $target[0]];
        $line2 = [$v1[1], $v2[1], $target[1]];

        $line1Mod = array_map(function($value) use ($line2) {
            return $value * $line2[0];
        }, $line1);

        $line2Mod = array_map(function($value) use ($line1) {
            return $value * $line1[0];
        }, $line2);

        $buttonB = ($line2Mod[2] - $line1Mod[2]) / ($line2Mod[1] - $line1Mod[1]);
        if (!is_int($buttonB) || $buttonB < 0) {
            return [];
        }

        $buttonA = ($target[0] - $v2[0]*$buttonB) / $v1[0];

        if (!is_int($buttonA) || $buttonA < 0) {
            return [];
        }

        return [[$buttonA, $buttonB]];
    }


    private function getMinTokens(array $solutions)
    {
        if (sizeof($solutions) === 0) {
            return 0;
        }
        $tokens = [];
        foreach ($solutions as $solution) {
            $tokens[] = $solution[0]*3 + $solution[1];
        }
        return min($tokens);
    }
}
