<?php

class Solution extends AdventOfCode\Solution
{
    public function first()
    {
        $input = $this->input->load();
        $board = new Board($input);

        $result = 0;
        foreach ($board->getTrailheads() as $startingPosition) {
            $board->getScore($startingPosition, $result);
        }

        return $result;
    }


    public function second()
    {
        return $this->first();
    }
}

class Board
{
    private readonly int $rows;
    private readonly int $cols;
    private readonly array $map;
    private array $trailheads = [];

    public const MOVE_DIRECTIONS = [
        [1, 0],
        [0, 1],
        [-1, 0],
        [0, -1]
    ];


    public function __construct(array $input)
    {
        $this->rows = sizeof($input);
        $this->cols = strlen($input[0]);

        $map = [];
        for ($i = 0; $i < $this->rows; $i++) {
            $row = [];
            for ($k = 0; $k < $this->cols; $k++) {
                $val = (int)$input[$i][$k];
                $row[] = $val;
                if ($val === 0) {
                    $this->trailheads[] = [$i, $k];
                }
            }
            $map[] = $row;
        }
        $this->map = $map;
    }

    public function getTrailheads(): array
    {
        return $this->trailheads;
    }

    public function getHeight(array $pos): int
    {
        if ($pos[0] < $this->rows && $pos[0] >= 0) {
            if ($pos[1] < $this->cols && $pos[1] >= 0) {
                return $this->map[$pos[0]][$pos[1]];
            }
        }
        return -1;
    }

    public function getScore(array $pos, int &$score, &$seen = []): void
    {
        $height = $this->getHeight($pos);
        if ($height === 9) {
            if (!in_array($pos, $seen)) {
                $seen[] = $pos;
                $score++;
            }
            return;
        }
        foreach (self::MOVE_DIRECTIONS as $dir) {
            $nextPos = [$pos[0]+$dir[0], $pos[1]+$dir[1]];
            if ($this->getHeight($nextPos) === $height+1) {
                $this->getScore($nextPos, $score, $seen);
                // $this->getScore($nextPos, $score); // uncomment for part 2
            }
        }
    }
}
