<?php
enum Operations {
    case Add;
    case Mul;
    case Conc;
}

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $result = 0;
        foreach ($input as $line) {
            $split = explode(": ", $line);
            $target = $split[0];
            $operands = explode(" ", $split[1]);

            $count1 = 0;
            $count2 = 0;
            $this->backtrack($target, $operands, [Operations::Add], $count1);
            $this->backtrack($target, $operands, [Operations::Mul], $count2);

            if ($count1+$count2 > 0) {
                $result += $target;
            }
        }
        return $result;
    }

    public function second(): int
    {
        $input = $this->input->load();
        $result = 0;
        foreach ($input as $line) {
            $split = explode(": ", $line);
            $target = $split[0];
            $operands = explode(" ", $split[1]);

            $count1 = 0;
            $count2 = 0;
            $count3 = 0;
            $this->backtrack($target, $operands, [Operations::Add], $count1, true);
            $this->backtrack($target, $operands, [Operations::Mul], $count2, true);
            $this->backtrack($target, $operands, [Operations::Conc], $count3, true);

            if ($count1+$count2+$count3 > 0) {
                $result += $target;
            }
        }
        return $result;
    }

    private function backtrack(int $target, array $operands, array $operations, int &$solutionCount, bool $partTwo = false): void
    {
        if (sizeof($operands)-1 === sizeof($operations)) {
            if ($this->getResult($operations, $operands) === $target) {
                $solutionCount++;
                return;
            }
        }

        // if (sizeof($operands) > 0) {
        //     if ($this->getResult($operations, $operands) >= $target) {
        //         $lastOperation = $operations[sizeof($operations)-1];
        //         if (!$partTwo) {
        //             if ($lastOperation === Operations::Add) {
        //                 return;
        //             }
        //         } else {
        //             if ($lastOperation === Operations::Conc) {
        //                 return;
        //             }
        //         }
        //     }
        // }

        $ops = $this->down($operands, $operations);
        while ($ops !== null) {
            $this->backtrack($target, $operands, $ops, $solutionCount, $partTwo);
            $ops = $this->next($ops, $partTwo);
        }
    }

    private function down(array $operands, array $operations): ?array
    {
        if (sizeof($operations) === sizeof($operands)-1) {
            return null;
        }
        $operations[] = Operations::Mul;
        return $operations;
    }

    private function next(array $operations, $partTwo = false): ?array
    {
        $operation = array_pop($operations);
        if ($operation === Operations::Mul) {
            $operations[] = Operations::Add;
            return $operations;
        }
        if ($operation === Operations::Add && $partTwo) {
            $operations[] = Operations::Conc;
            return $operations;
        }
        return null;
    }

    private function getResult(array $operations, array $operands): int
    {
        $result = $operands[0];
        for ($i = 0; $i < sizeof($operations); $i++) {
            $result = $this->applyOperation($result, $operands[$i+1], $operations[$i]);
        }
        return $result;
    }

    private function applyOperation($arg1, $arg2, Operations $operation): int
    {
        switch ($operation) {
            case Operations::Add:
                return (int)$arg1+(int)$arg2;

            case Operations::Mul:
                return (int)$arg1*(int)$arg2;

            case Operations::Conc:
                return (int)((string)$arg1 . (string)$arg2);
        }
    }
}
