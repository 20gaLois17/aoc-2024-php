<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $parsePredecessors = true;
        $predecessors = [];
        $lists = [];
        foreach ($input as $line) {
            if (empty($line)) {
                $parsePredecessors = false;
                continue;
            }
            if ($parsePredecessors) {
                $vals = explode("|", $line);
                $predecessors[$vals[1]][] = $vals[0];
            } else {
                $lists[] = explode(",", $line);
            }
        }

        $result = 0;
        foreach ($lists as $list) {
            if ($this->isValid($list, $predecessors)) {
                $result += $this->getCenterElement($list);
            }
        }
        return $result;
    }

    public function second(): int
    {
        $input = $this->input->load();
        $parsePredecessors = true;
        $predecessors = [];
        $lists = [];
        foreach ($input as $line) {
            if (empty($line)) {
                $parsePredecessors = false;
                continue;
            }
            if ($parsePredecessors) {
                $vals = explode("|", $line);
                $predecessors[$vals[1]][] = $vals[0];
            } else {
                $lists[] = explode(",", $line);
            }
        }

        $result = 0;
        // correct list needs to be unique, therefore each element should have a unique
        // count of predecessors
        foreach ($lists as $list) {
            if ($this->isValid($list, $predecessors)) {
                continue;
            } else {
                foreach ($list as $value) {
                    if ($this->countPredecessors($value, $list, $predecessors) === (sizeof($list)-1)/2) {
                        $result += $value;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    private function getCenterElement(array $input)
    {
        $centerIdx = intdiv(sizeof($input), 2);
        return $input[$centerIdx];
    }

    private function isValid(array $list, array $predecessors): bool
    {
        for ($i = 0; $i < sizeof($list)-1; $i++) {
            if (!array_key_exists($list[$i], $predecessors)) {
                continue;
            }
            if ($this->hasHigherIndexPredecessor($i, $list, $predecessors)) {
                return false;
            }
        }
        return true;
    }

    private function hasHigherIndexPredecessor($index, array $list, array $predecessors): bool
    {
        for ($k = $index+1; $k < sizeof($list); $k++) {
            if (in_array($list[$k], $predecessors[$list[$index]])) {
                return true;
            }
        }
        return false;
    }

    private function countPredecessors(mixed $element, array $list, array $predecessors) {
        if (!array_key_exists($element, $predecessors)) {
            return 0;
        }
        $count = 0;
        foreach ($list as $value) {
            if ($value !== $element) {
                if (in_array($value, $predecessors[$element])) {
                    $count++;
                }
            }
        }
        return $count;
    }
}
