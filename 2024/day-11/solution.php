<?php

class Solution extends AdventOfCode\Solution
{
    private $cache = [];

    public function first(): int
    {
        $input = $this->input->load();
        $stones = explode(" ", $input[0]);

        for ($i = 0; $i < 25; $i++) {
            $stones = $this->blink($stones);
        }

        return sizeof($stones);

    }

    public function second(): int
    {
        $input = $this->input->load();
        $result = 0;

        $input = $this->input->load();
        $stones = explode(" ", $input[0]);

        foreach ($stones as $stone) {
            $result += $this->blinkRec($stone, 75);
        }

        return $result;
    }

    private function blink(array $stones): array
    {
        $result = [];
        foreach ($stones as $stone) {
            if ((int)$stone === 0) {
                $result[] = 1;
                continue;
            }

            $sStone = (string)$stone;
            $sLength = strlen($sStone);

            if ($sLength % 2 === 1) {
                $result[] = $stone * 2024;
                continue;
            }

            $splits = str_split($sStone, $sLength/2);
            $result[] = (int)$splits[0];
            $result[] = (int)$splits[1];
        }
        return $result;
    }

    private function blinkRec(int $stone, int $remainingBlinks): int
    {
        if ($remainingBlinks === 0) {
            return 1;
        }
        if ((int)$stone === 0) {
            return $this->cache(1, $remainingBlinks-1);
        }
        $sStone = (string)$stone;
        $sLength = strlen($sStone);

        if ($sLength % 2 === 1) {
            return $this->cache($stone*2024, $remainingBlinks-1);
        }

        $splits = str_split($sStone, $sLength/2);
        return $this->cache((int)$splits[0], $remainingBlinks-1) + $this->cache((int)$splits[1], $remainingBlinks-1);
    }

    private function cache(int $stone, int $remainingBlinks): int
    {
        if (array_key_exists("{$stone}-{$remainingBlinks}", $this->cache)) {
            return $this->cache["{$stone}-{$remainingBlinks}"];
        }
        $value = $this->blinkRec($stone, $remainingBlinks);
        $this->cache["{$stone}-{$remainingBlinks}"] = $value;
        return $value;
    }

}
