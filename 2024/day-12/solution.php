<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $result = 0;

        $plots = $this->getPlots($input);
        foreach ($plots as $key => $p) {
            echo "{$key} area: {$this->getArea($p)} perim: {$this->getPerimeter($p)}" . PHP_EOL;
            $result += $this->getArea($p) * $this->getPerimeter($p);
        }
        return $result;
    }


    public function second(): int
    {
        $input = $this->input->load();

        $result = 0;
        $plots = $this->getPlots($input);
        $this->getNumberOfSides($plots[4]);
        die();
        foreach ($plots as $key => $p) {
            echo "{$key} area: {$this->getArea($p)} sides: {$this->getNumberOfSides($p)}" . PHP_EOL;
            $result += $this->getArea($p) * $this->getNumberOfSides($p);
        }
        return $result;
    }
    private function getPlots(array $input): array
    {
        $rows = sizeof($input);
        $cols = strlen($input[0]);
        $plots = [];
        $visited = [];
        for ($i = 0; $i < $rows; $i++) {
            for ($k = 0; $k < $cols; $k++) {
                if (in_array([$i, $k], $visited)) {
                    continue;
                }
                $list = [];
                $queue = [[$i, $k]];
                while (sizeof($queue) > 0) {
                    $e = array_shift($queue);
                    if (in_array($e, $visited)) {
                        continue;
                    }
                    $list[] = $e;
                    $visited[] = $e;
                    $r = $e[0];
                    $c = $e[1];
                    $symbol = $input[$r][$c];
                    foreach ([[$r, $c+1], [$r+1, $c], [$r, $c-1], [$r-1, $c]] as $pos) {
                        if ($pos[0] < 0 || $pos[0] >= $rows) {
                            continue;
                        }
                        if ($pos[1] < 0 || $pos[1] >= $cols) {
                            continue;
                        }
                        if ($input[$pos[0]][$pos[1]] !== $symbol) {
                            continue;
                        }
                        if (in_array($pos, $visited)) {
                            continue;
                        }
                        $queue[] = $pos;
                    }
                }
                $plots[] = $list;
            }
        }
        return $plots;
    }

    private function getPerimeter(array $input): int
    {
        $result = 0;
        foreach ($input as $pos) {
            $i = $pos[0];
            $k = $pos[1];
            $tmpPerim = 4;
            foreach([[$i, $k+1], [$i+1, $k], [$i, $k-1], [$i-1, $k]] as $neighbor) {
                if (in_array($neighbor, $input)) {
                    $tmpPerim--;
                }
            }
            $result += $tmpPerim;
        }
        return $result;
    }

    private function getArea(array $input) :int
    {
        return sizeof($input);
    }

    private function getNumberOfSides(array $input): int
    {
        // how can we count the number of corners of a shape?
        $result = 0;
        return $result;
    }
}
