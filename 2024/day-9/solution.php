<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $mem = [];

        $this->populateMemory($mem, $input[0]);
        $this->defrag($mem);
        return $this->calculateChecksum($mem);
    }

    public function second()
    {
        $input = $this->input->load();
        $mem = [];
        $chunkIndex = intdiv(strlen($input[0]), 2);

        $this->populateMemory($mem, $input[0]);

        $this->defrag2($mem, $chunkIndex);

        return $this->calculateChecksum($mem);
    }

    // use left and right pointer
    private function defrag(array &$mem): void
    {
        $leftIdx = 0;
        $rightIdx = sizeof($mem)-1;

        while ($leftIdx < $rightIdx) {
            if ($mem[$leftIdx] !== null) {
                $leftIdx++;
                continue;
            }
            if ($mem[$rightIdx] === null) {
                $rightIdx--;
                continue;
            }

            $mem[$leftIdx] = $mem[$rightIdx];
            $mem[$rightIdx] = null;
        }
        return;
    }

    private function defrag2(array &$mem): void
    {
        $rightIdx = sizeof($mem)-1;
        while (true) {
            $newRightIdx = $this->moveChunk($mem, $rightIdx);
            if ($newRightIdx === $rightIdx) {
                break;
            } else {
                $rightIdx = $newRightIdx;
            }
        }
    }

    private function moveChunk(array &$mem, int $rightIdx): int
    {
        $neededSpace = 0;
        $leftIdx = 0;
        while ($leftIdx < $rightIdx) {
            if ($mem[$leftIdx] !== null) {
                $leftIdx++;
                continue;
            }
            $freeSpace = $this->peek(null, $leftIdx, 1, $mem);

            while ($mem[$rightIdx] === null) {
                $rightIdx--;
                continue;
            }
            $neededSpace = $this->peek($mem[$rightIdx], $rightIdx, -1, $mem);

            if ($freeSpace >= $neededSpace) {
                for ($i = 0; $i < $neededSpace; $i++) {
                    $mem[$leftIdx + $i] = $mem[$rightIdx - $i];
                    $mem[$rightIdx - $i] = null;
                }
            } else {
                // look for next freeSpace
                $leftIdx += $freeSpace;
            }
        }
        return $rightIdx - $neededSpace;
    }

    // get the number of consecutive elements in the array, starting at the given index
    private function peek(mixed $value, int $start, int $dir, array $input): int
    {
        $result = 0;
        while (array_key_exists($start, $input)) {
            if ($input[$start] === $value) {
                $result++;
            } else {
                break;
            }
            $start += $dir;
        }
        return $result;
    }

    private function populateMemory(array &$mem, string $input)
    {
        for ($i = 0; $i < strlen($input); $i++) {
            $val = (int)$input[$i];
            if ($i%2 === 0) {
                for ($k = 0; $k < $val; $k++) {
                    $mem[] = intdiv($i, 2);
                }
            } else {
                for ($k = 0; $k < $val; $k++) {
                    $mem[] = null;
                }
            }
        }
    }

    private function calculateChecksum(array $input): int
    {
        $result = 0;
        for ($pos = 0; $pos < sizeof($input); $pos++) {
            $id = $input[$pos];
            if ($id === null) {
                continue;
            }
            $result += $pos*$id;
        }
        return $result;
    }
}
