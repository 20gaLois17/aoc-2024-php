<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $board = $this->parseInputBoard($input);
        $instructions = $this->parseInputInstructions($input);

        for ($i = 0; $i < sizeof($instructions); $i++) {
            switch($instructions[$i]) {
                case "^":
                    $board->moveRobot([-1, 0]);
                    break;

                case ">":
                    $board->moveRobot([0, 1]);
                    break;

                case "v":
                    $board->moveRobot([1, 0]);
                    break;

                case "<":
                    $board->moveRobot([0, -1]);
                    break;
            }
            // DEBUG
            // readline();
            // print($board);
        }
        return $board->getResult();
    }


    public function second()
    {
        $input = $this->input->load();
        $board = $this->parseInputBoard2($input);

        $instructions = $this->parseInputInstructions($input);

        print($board);
        echo $board->countBoxes() . PHP_EOL;
        for ($i = 0; $i < sizeof($instructions); $i++) {
            switch($instructions[$i]) {
                case "^":
                    // echo "move robot up" . PHP_EOL;
                    $board->moveRobot2([-1, 0]);
                    break;

                case ">":
                    // echo "move robot right" . PHP_EOL;
                    $board->moveRobot2([0, 1]);
                    break;

                case "v":
                    // echo "move robot down" . PHP_EOL;
                    $board->moveRobot2([1, 0]);
                    break;

                case "<":
                    // echo "move robot left" . PHP_EOL;
                    $board->moveRobot2([0, -1]);
                    break;
            }
            // DEBUG
            // readline();
            // print($board);
        }
        print($board);
        return $board->getResult2();
    }

    private function parseInputBoard(array $input): Board
    {
        $board = [];
        for ($i = 0; $i < sizeof($input); $i++) {
            if (strlen($input[$i]) === 0) {
                return new Board($i, strlen($input[0]), $board);
            }
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $symbol = $input[$i][$k];
                $board[$i][$k] = $symbol;
            }
        }
    }

    private function parseInputBoard2(array $input): Board
    {
        $board = [];
        for ($i = 0; $i < sizeof($input); $i++) {
            if (strlen($input[$i]) === 0) {
                return new Board($i, strlen($input[0])*2, $board);
            }
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $symbol = $input[$i][$k];
                switch ($symbol) {
                    case "#":
                    case ".":
                        $board[$i][2*$k] = $symbol;
                        $board[$i][2*$k+1] = $symbol;
                        break;

                    case "@":
                        $board[$i][2*$k] = $symbol;
                        $board[$i][2*$k+1] = ".";
                        break;

                    case "O":
                        $board[$i][2*$k] = "[";
                        $board[$i][2*$k+1] = "]";
                        break;

                }
            }
        }
    }

    private function parseInputInstructions(array $input): array
    {
        $instructions = [];
        $i = 0;
        while (strlen($input[$i]) !== 0) {
            $i++;
            continue;
        }
        for (; ++$i < sizeof($input);) {
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $instructions[] = $input[$i][$k];
            }
        }
        return $instructions;
    }
}

class Board
{
    private array $robotPosition;
    public function __construct(
        private readonly int $rows,
        private readonly int $cols,
        private array $data
    ) {
        for ($i = 0; $i < $rows; $i++) {
            for ($k = 0; $k < $cols; $k++) {
                // the robot position will not be saved on the board
                if ($data[$i][$k] === "@") {
                    $this->robotPosition = [$i, $k];
                    $this->data[$i][$k] = ".";
                    return;
                }
            }
        }
    }

    public function moveRobot(array $dir): void
    {
        $nextPos = [
            $this->robotPosition[0] + $dir[0],
            $this->robotPosition[1] + $dir[1]
        ];
        switch ($this->data[$nextPos[0]][$nextPos[1]]) {
            case "#":
                break;

            case ".":
                $this->robotPosition = $nextPos;
                break;

            case "O":
                if ($this->moveBox($nextPos, $dir)) {
                    $this->robotPosition = $nextPos;
                }
                break;

        }
    }

    private function moveBox(array $pos, array $dir): bool
    {
        $nextPos = [
            $pos[0] + $dir[0],
            $pos[1] + $dir[1]
        ];
        switch ($this->data[$nextPos[0]][$nextPos[1]]) {
            case ".":
                $this->data[$pos[0]][$pos[1]] = ".";
                $this->data[$nextPos[0]][$nextPos[1]] = "O";
                return true;

            case "O":
                if ($this->moveBox($nextPos, $dir)) {
                    $this->data[$pos[0]][$pos[1]] = ".";
                    $this->data[$nextPos[0]][$nextPos[1]] = "O";
                    return true;
                } else {
                    return false;
                }

            case "#":
                return false;
        }
    }

    public function moveRobot2(array $dir): void
    {
        $nextPos = [
            $this->robotPosition[0] + $dir[0],
            $this->robotPosition[1] + $dir[1]
        ];
        switch ($this->data[$nextPos[0]][$nextPos[1]]) {
            case "#":
                break;

            case ".":
                $this->robotPosition = $nextPos;
                break;

            case "[":
                if ($this->moveBox2([$nextPos, [$nextPos[0], $nextPos[1]+1]], $dir))
                {
                    $this->robotPosition = $nextPos;
                }
                break;

            case "]":
                if ($this->moveBox2([[$nextPos[0], $nextPos[1]-1] ,$nextPos], $dir)) {
                    $this->robotPosition = $nextPos;
                }
                break;

        }
    }

    private function moveBox2(array $box, array $dir, $doMove = true): bool
    {
        $canMove = true;
        switch ($dir[0]) {
            // vertical direction
            case 1:
            case -1:
                // special case
                //   ##
                // [][]
                //  []
                //  @
                $nextPos1 = [$box[0][0] + $dir[0], $box[0][1] + $dir[1]];
                $nextPos2 = [$box[1][0] + $dir[0], $box[1][1] + $dir[1]];
                if ($this->data[$nextPos1[0]][$nextPos1[1]] === "]" && $this->data[$nextPos2[0]][$nextPos2[1]] === "[")
                {
                    if (!$this->moveBox2([[$nextPos1[0], $nextPos1[1]-1], [$nextPos1[0], $nextPos1[1]]], $dir, false) ||
                       !$this->moveBox2([[$nextPos2[0], $nextPos2[1]-1], [$nextPos2[0], $nextPos2[1]]], $dir, false))
                    {
                        return false;
                    }
                }

                foreach ($box as $part) {
                    $nextPos = [
                        $part[0] + $dir[0],
                        $part[1] + $dir[1]
                    ];

                    switch ($this->data[$nextPos[0]][$nextPos[1]]) {
                        case ".":
                            break;

                        case "#":
                            $canMove = false;
                            break;

                        case "[":
                            if (!$this->moveBox2([$nextPos, [$nextPos[0], $nextPos[1]+1]], $dir)) {
                                $canMove = false;
                            }
                            break;

                        case "]":
                            if (!$this->moveBox2([[$nextPos[0], $nextPos[1]-1], $nextPos], $dir)) {
                                $canMove = false;
                            }
                            break;

                    }
                }
                break;

            // horizontal direction
            default:
                $nextPos = $dir[1] === 1
                ? [$box[1][0], $box[1][1]+1]
                : [$box[0][0], $box[0][1]-1];

                switch ($this->data[$nextPos[0]][$nextPos[1]]) {
                    case ".":
                        break;

                    case "#":
                        $canMove = false;
                        break;

                    case "[":
                        if (!$this->moveBox2([$nextPos, [$nextPos[0], $nextPos[1]+1]], $dir)) {
                            $canMove = false;
                        }
                        break;

                    case "]":
                        if (!$this->moveBox2([[$nextPos[0], $nextPos[1]-1], $nextPos], $dir)) {
                            $canMove = false;
                        }
                        break;

                }


        }
        if ($canMove && $doMove) {
            foreach ($box as $part) {
                $this->data[$part[0]][$part[1]] = ".";
            }

            $this->data[$box[0][0] + $dir[0]][$box[0][1] + $dir[1]] = "[";
            $this->data[$box[1][0] + $dir[0]][$box[1][1] + $dir[1]] = "]";

        }
        return $canMove;
    }

    public function __toString(): string
    {
        $s = "";
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ([$i, $k] === $this->robotPosition) {
                    $s .= "@";
                } else {
                    $s .= $this->data[$i][$k];
                }
            }
            $s .= "\n";
        }
        return $s;
    }

    public function getResult(): int
    {
        $result = 0;
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ($this->data[$i][$k] === "O") {
                    $coordinate = 100 * $i + $k;
                    $result += $coordinate;
                }
            }
        }
        return $result;
    }

    public function getResult2(): int
    {
        $result = 0;
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ($this->data[$i][$k] === "[") {
                    $coordinate = 100 * $i + $k;
                    $result += $coordinate;
                }
            }
        }
        return $result;
    }

    public function countBoxes(): int
    {
        $result = 0;
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ($this->data[$i][$k] === "[") {
                    $result++;
                }
            }
        }
        return $result;
    }
}
