<?php

class Solution extends AdventOfCode\Solution
{
    public function first()
    {
        $input = $this->input->load();

        $l = [];
        $r = [];
        $this->parseInput($input, $l, $r);

        asort($l);
        asort($r);
        $l = array_values($l);
        $r = array_values($r);

        $distances = 0;
        for ($i = 0; $i < sizeof($l); $i++) {
            $distances+= abs($l[$i] - $r[$i]);
        }

        return $distances;
    }


    public function second()
    {
        $input = $this->input->load();

        $l = [];
        $r = [];
        $this->parseInput($input, $l, $r);

        $score = 0;

        $map = [];
        foreach ($r as $value) {
            if (!array_key_exists($value, $map)) {
                $map[$value] = 1;
            } else {
                $map[$value]++;
            }
        }
        foreach ($l as $needle) {
            if (!array_key_exists($needle, $map)) {
                continue;
            }
            $score += $needle*$map[$needle];
        }

        return $score;
    }

    private function parseInput(array $input, &$l, &$r)
    {
        foreach ($input as $line) {
            $values = explode("  ", $line);
            $l[] = intval($values[0]);
            $r[] = intval($values[1]);
        }
    }

}
