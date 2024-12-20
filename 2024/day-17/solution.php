<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): string
    {
        $input = $this->input->load();

        $computer = new Computer(...$this->parseInput($input));

        $computer->runProgram();

        return join(",", $computer->out);
    }

    public function second(): string
    {
        $input = $this->input->load();

        $compInput = $this->parseInput($input);


        // Program: 2,4,1,5,7,5,0,3,1,6,4,3,5,5,3,0
        // 2,4 => B = A % 8 (least significant 3 bits of A)
        // 1,5 => B = B ^ 101 (toggle bits)
        // 7,5 => C = A / 2 ^ B (bitshift A by B)
        // 0,3 => A = A / 2^3 (right bitshift by 3 bits)
        // 1,6 => B = B ^ 110 (toggle bits)
        // 4,3 => B = B ^ C (toggle bits)
        // 5,5 => Out B % 8
        // 3,0 => Jump 0
        //
        // A = 111 (7)
        // B = 010 (2)
        // C = 001 (1)
        // A = 0
        // B = 100 (4)
        // B = 101 (5)

        // 011000 => 3,0
        // 100111 => 5,5
        //

        // $test = bindec("011000" . "100111" . "101"); // 3,5,5,3,0
        $computer = new Computer(106078322330010, $compInput[1], $compInput[2], $compInput[3]);
        $computer->runProgram();
        echo join(",", $computer->out) . PHP_EOL;

        die();
        //

        $results = [];
        for ($i = 0; $i < pow(2, 24); $i++) {
            // do we care for endless loops?
            // echo $i;
            $s = decbin($i);
            $diff = 23 - strlen($s);
            for ($k = 0; $k < $diff; $k++) {

                $s = "0" . $s;
            }
            //6322700: 11000000111101000001100 1,6,4,3,5,5,3,0
            $tails = [
                "11000000111101001001000",
                "11000000111101001001011",
                "11000000111101001001100",
                "11000000111111000001100",
                "11000000111111000010111",
                "11000100100010011110111",
                "11000100100101011110111",
                "11000100111101000001100",
                "11000100111101001001000",
                "11000100111101001001011",
                "11000100111101001001100",
                "11000100111111000001100",
                "11000100111111000010111",
                "11000110100101011110111",
                "11001000100101011110111",
                "11001000111101000001100",
                "11001000111101001001000",
                "11001000111101001001011",
                "11001000111101001001100",
                "11001000111111000001100",
                "11001000111111000010111"
            ];
            foreach ($tails as $tail) {
                $computer = new Computer(bindec($tail . $s), $compInput[1], $compInput[2], $compInput[3]);
                $computer->runProgram();

                // if (sizeof($computer->out) > 2) {
                //     if ($computer->out[0] === 2 && $computer->out[1] === 4) {
                //         echo $i . ": " . decbin($i) . " " .  join(",", $computer->out) . PHP_EOL;
                //     }
                // }
                if (join(",", $computer->out) == "2,4,1,5,7,5,0,3,1,6,4,3,5,5,3,0") {
                    $results[] = bindec($tail . $s);
                    echo $i . ": " . bindec($tail . $s) . " " .  join(",", $computer->out) . PHP_EOL;
                }
                // echo $i . ": " . decbin($i) . " " . join(",", $computer->out) . PHP_EOL;
                // echo $i . ": " . decbin($i) . " " .  sizeof($computer->out) . PHP_EOL;
                // if (sizeof($computer->out) !== sizeof($computer->program)) {
                //     continue;
                // }
                // if ($computer->out === $computer->program) {
                //     return $i;
                // }
            }
        }
        return min($results);
    }

    private function parseInput(array $input): array
    {
        $registerA = str_replace("Register A: ", "", $input[0]);
        $registerB = str_replace("Register B: ", "", $input[1]);
        $registerC = str_replace("Register C: ", "", $input[2]);
        $program = str_replace("Program: ", "", $input[4]);

        return [
            (int)$registerA,
            (int)$registerB,
            (int)$registerC,
            array_map(function($value) {
                return (int)$value;
            }, explode(",", $program))
        ];
    }
}

class Computer
{
    private int $ip = 0; // instruction pointer
    public array $out = []; // output
    public function __construct(
        private int $registerA,
        public int $registerB,
        public int $registerC,
        public readonly array $program
    ) {
    }

    public function runProgram($partTwo = false): void
    {
        while ($this->ip < sizeof($this->program)) {
            $this->performOperation(
                $this->program[$this->ip],
                $this->program[$this->ip+1]
            );
        }
    }

    private function performOperation(int $opcode, int $operand): void
    {
        switch ($opcode) {
            case 0:
                $this->registerA = intdiv(
                    $this->registerA,
                    pow(2, $this->getComboOperand($operand))
                );
                $this->ip += 2;
                break;

            case 1:
                $this->registerB = $this->registerB ^ $operand;
                $this->ip += 2;
                break;

            case 2:
                $this->registerB = $this->getComboOperand($operand) % 8;
                $this->ip += 2;
                break;

            case 3:
                if ($this->registerA !== 0) {
                    $this->ip = $operand;
                } else {
                    $this->ip += 2;
                }
                break;

            case 4:
                $this->registerB = $this->registerB ^ $this->registerC;
                $this->ip += 2;
                break;

            case 5:
                $this->out[] = $this->getComboOperand($operand) % 8;
                $this->ip += 2;
                break;

            case 6:
                $this->registerB = intdiv(
                    $this->registerA,
                    pow(2, $this->getComboOperand($operand))
                );
                $this->ip += 2;
                break;

            case 7:
                $this->registerC = intdiv(
                    $this->registerA,
                    pow(2, $this->getComboOperand($operand))
                );
                $this->ip += 2;
                break;

        }
    }

    private function getComboOperand(int $val): int
    {
        switch ($val) {
            case 0:
            case 1:
            case 2:
            case 3:
                return $val;
            case 4:
                return $this->registerA;
            case 5:
                return $this->registerB;
            case 6:
                return $this->registerC;
            case 7:
                throw new Exception("combo operand 7 not valid");
        }
    }
}
