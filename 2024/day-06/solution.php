<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $board = new Board(sizeof($input), strlen($input[0]));
        $this->parseInput($input, $board);
        while (true) {
            if ($board->moveGuard()) {
                continue;
            }
            break;
        }

        return $board->getUniqueGuardPositions();
    }

    public function second(): int
    {
        $input = $this->input->load();
        $loops = 0;
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[0]); $k++) {
                $board = new Board(sizeof($input), strlen($input[0]));
                $this->parseInput($input, $board);
                if (!$board->hasObstacle($i, $k)) {
                    $board->addObstacle($i, $k);
                    while (true) {
                        if ($board->closesCycle()) {
                            $loops++;
                        } else if ($board->moveGuard()) {
                            continue;
                        }
                        $board->removeObstacle($i, $k);
                        break;
                    }
                }
            }
        }

        return $loops;
    }

    private function parseInput($input, &$board): void
    {
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $symbol = $input[$i][$k];
                if ($symbol === "#") {
                    $board->addObstacle($i, $k);
                    continue;
                }
                if ($symbol === ".") {
                    continue;
                } else {
                    switch ($symbol) {
                        case "^":
                            $board->setGuard(new Guard($i, $k));
                            break;

                        default:

                        throw new \Exception("unhandled guard symbol");
                    }
                }
            }
        }
    }
}

enum Directions {
    case North;
    case South;
    case East;
    case West;
}

class Board {

    private $obstacles = [];
    private ?Guard $guard = null;
    private $guardPositions = [];

    public function __construct(
        private readonly int $cols,
        private readonly int $rows
    ) {
    }

    public function setGuard(Guard $guard): void
    {
        $this->guard = $guard;
        $this->guardPositions["{$this->guard->getPosition()[0]}:{$this->guard->getPosition()[1]}"] = $this->guard->getDirection();
    }

    public function getDimension(): array
    {
        return [
            $this->rows,
            $this->cols
        ];
    }

    public function addObstacle(int $posX, int $posY): void
    {
        $this->obstacles["{$posX}:{$posY}"] = 1;
    }

    public function removeObstacle(int $posX, int $posY): void
    {
        unset($this->obstacles["{$posX}:{$posY}"]);
    }

    public function addGuardPosition(int $posX, int $posY): void
    {
        $this->guardPositions["{$posX}:{$posY}"] = $this->guard->getDirection();
    }

    public function wasVisitedByGuard(int $posX, int $posY): bool
    {
        return array_key_exists("{$posX}:{$posY}", $this->guardPositions);
    }

    public function hasObstacle(int $posX, int $posY): bool
    {
        return array_key_exists("{$posX}:{$posY}", $this->obstacles);
    }


    public function moveGuard(): bool
    {
        // only update guardPositions if guard not out of bounds
        $nextPosition = $this->guard->getNextPosition();
        if ($nextPosition[0] < 0 || $nextPosition[0] >= $this->cols) {
            return false;
        }
        if ($nextPosition[1] < 0 || $nextPosition[1] >= $this->rows) {
            return false;
        }

        if ($this->hasObstacle(...$nextPosition)) {
            $this->guard->turn();
        } else {
            $this->guard->step();
            $this->addGuardPosition(...$nextPosition);
        }

        return true;
    }

    public function closesCycle(): bool
    {
        $nextPosition = $this->guard->getNextPosition();
        if ($this->wasVisitedByGuard(...$nextPosition)) {
            if ($this->guard->getDirection() === $this->guardPositions["{$nextPosition[0]}:{$nextPosition[1]}"]) {
                return true;
            }
        }
        return false;
    }

    public function getUniqueGuardPositions(): int
    {
        return sizeof($this->guardPositions);
    }
}

class Guard {
    private Directions $direction = Directions::North;
    private int $posX;
    private int $posY;

    public function __construct(int $posX, int $posY)
    {
        $this->direction = Directions::North;
        $this->posX = $posX;
        $this->posY = $posY;
    }

    public function getPosition(): array
    {
        return [
            $this->posX,
            $this->posY
        ];
    }

    public function getDirection(): Directions
    {
        return $this->direction;
    }

    public function turn(): void
    {
        switch ($this->direction) {
            case Directions::North:
                $this->direction = Directions::East;
                break;

            case Directions::South:
                $this->direction = Directions::West;
                break;

            case Directions::East:
                $this->direction = Directions::South;
                break;

            case Directions::West:
                $this->direction = Directions::North;
                break;
        }
    }

    public function getNextPosition(): array
    {
        switch ($this->direction) {
            case Directions::North:
                return [
                    $this->posX - 1,
                    $this->posY
                ];

            case Directions::South:
                return [
                    $this->posX + 1,
                    $this->posY
                ];

            case Directions::East:
                return [
                    $this->posX,
                    $this->posY + 1
                ];

            case Directions::West:
                return [
                    $this->posX,
                    $this->posY - 1
                ];
        }
    }

    public function step(): void
    {
        switch ($this->direction) {
            case Directions::North:
                $this->posX--;
            break;

            case Directions::South:
                $this->posX++;
            break;

            case Directions::East:
                $this->posY++;
            break;

            case Directions::West:
                $this->posY--;
            break;
        }
    }
}
