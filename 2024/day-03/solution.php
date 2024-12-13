<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $result = 0;
        foreach ($input as $chunk) {
            $matches = [];
            preg_match_all("/mul\([0-9]{1,3},[0-9]{1,3}\)/", $chunk, $matches);
            foreach ($matches[0] as $match) {
                $numbers = [];
                preg_match_all("/[0-9]{1,3}/", $match, $numbers);
                $result += (int)$numbers[0][0]*(int)$numbers[0][1];
            }
        }
        return $result;
    }

    public function second(): int
    {
        $input = $this->input->load();

        $result = 0;
        foreach ($input as $chunk) {
            $matches = [];

            preg_match_all(
                "/mul\([0-9]{1,3},[0-9]{1,3}\)/",
                $this->filter($chunk),
                $matches
            );
            foreach ($matches[0] as $match) {
                $numbers = [];
                preg_match_all("/[0-9]{1,3}/", $match, $numbers);
                $result += (int)$numbers[0][0]*(int)$numbers[0][1];
            }
        }
        return $result;
    }

    private function filter(string $input): string
    {
        $filtered = "";
        $filterOn = false;
        for ($i = 0; $i < strlen($input); $i++) {
            if (!$filterOn) {
                $filtered .= $input[$i];
            }
            if ($input[$i] === "d") {
                if (!$filterOn) {
                    if (substr($input, $i, strlen("don't()")) === "don't()") {
                        // $filtered .= "###";
                        $filterOn = true;
                    }
                } else {
                    if (substr($input, $i, strlen("do()")) === "do()") {
                        // $filtered .= "###";
                        $filterOn = false;
                    }
                }
            }
        }
        return $filtered;
    }
}
