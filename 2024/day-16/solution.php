<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $maze = new Maze($input);
        $startPosition = $maze->search("S", $input);
        $endPosition = $maze->search("E", $input);

        $minScore = sizeof($input)*sizeof($input) * 1000;
        $winningPaths = [];

        $this->move(
            maze: $maze,
            pos: $startPosition,
            dir: [0, 1],
            end: $endPosition,
            score: 0,
            minScore: $minScore,
            path: [],
            winningPaths: $winningPaths
        );
        return $minScore;
    }

    public function second(): int
    {
        $input = $this->input->load();
        $maze = new Maze($input);
        $startPosition = $maze->search("S", $input);
        $endPosition = $maze->search("E", $input);

        // $minScore = $this->first($input);
        $minScore = 160624;

        $winningPaths = [];

        $this->move(
            maze: $maze,
            pos: $startPosition,
            dir: [0, 1],
            end: $endPosition,
            score: 0,
            minScore: $minScore,
            path: [],
            winningPaths: $winningPaths,
        );

        $uniquePositions = [];

        foreach ($winningPaths as $winningPath) {
            foreach ($winningPath as $pos) {
                $uniquePositions["{$pos[0]}:{$pos[1]}"] = 1;
            }
        }
        $maze->printUniquePositions($uniquePositions);
        return sizeof($uniquePositions);
    }

    private function move(
        Maze $maze,
        array $pos,
        array $dir,
        array $end,
        int $score,
        int &$minScore,
        ?array $path,
        ?array &$winningPaths,
    ): void
    {
        // readline();
        // $maze->print($pos, $dir);
        // echo $score . PHP_EOL;
        $path[] = $pos;
        if ($score > $minScore) {
            return;
        }
        // adding 1000 ensures we do not preemptively dismiss possible solutions
        // for part two
        if ($maze->getVisitedScore($pos, $score) + 1000 < $score) {
            return;
        }
        if ($pos === $end) {
            if ($score < $minScore) {
                $winningPaths = [];
            }
            $minScore = min($score, $minScore);
            $winningPaths[] = $path;
            // $maze->print($pos, $dir);
            return;
        }

        // try to keep direction
        $nextPos = [$pos[0]+$dir[0], $pos[1]+$dir[1]];
        if ($maze->canTraverse($nextPos)) {
            $this->move(
                $maze,
                $nextPos,
                $dir,
                $end,
                $score+1,
                $minScore,
                $path,
                $winningPaths
            );
        }

        // turn by 90 degrees left | right and try new directions
        switch ($dir[0]) {
            case 1:
            case -1:
                foreach ([1, -1] as $newDir) {
                    $nextPos = [$pos[0], $pos[1]+$newDir];
                    if ($maze->canTraverse($nextPos)) {
                        $this->move($maze,
                            $nextPos,
                            [0, $newDir],
                            $end,
                            $score+1001,
                            $minScore,
                            $path,
                            $winningPaths
                        );
                    }
                }
        }
        switch ($dir[1]) {
            case 1:
            case -1:
                foreach ([1, -1] as $newDir) {
                    $nextPos = [$pos[0]+$newDir, $pos[1]];
                    if ($maze->canTraverse($nextPos)) {
                        $this->move(
                            $maze,
                            $nextPos,
                            [$newDir, 0],
                            $end,
                            $score+1001,
                            $minScore,
                            $path,
                            $winningPaths
                        );
                    }
                }
        }
    }
}

class Maze
{
    private int $rows;
    private int $cols;
    private array $visited = [];

    public function __construct(
        private readonly array $data
    ) {
        $this->rows = sizeof($data);
        $this->cols = strlen($data[0]);
    }

    public function get(array $pos): ?string
    {
        if ($pos[0] < 0 || $pos[0] >= $this->rows) {
            return null;
        }
        if ($pos[1] < 0 || $pos[1] >= $this->cols) {
            return null;
        }
        return $this->data[$pos[0]][$pos[1]];
    }

    public function search(string $needle): array
    {
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ($this->data[$i][$k] === $needle) {
                    return [$i, $k];
                }
            }
        }
    }

    public function canTraverse(array $pos): bool
    {
        $symbol = $this->get($pos);

        if ($symbol === "." || $symbol === "E") {
            return true;
        }
        return false;
    }

    public function getVisitedScore(array $pos, int $score) {
        if (array_key_exists("{$pos[0]}:{$pos[1]}", $this->visited)) {
            $result = $this->visited["{$pos[0]}:{$pos[1]}"];
            if ($score < $result) {
                $this->visited["{$pos[0]}:{$pos[1]}"] = $score;
            }
            return $this->visited["{$pos[0]}:{$pos[1]}"];
        }

        $this->visited["{$pos[0]}:{$pos[1]}"] = $score;
        return $score;
    }

    public function printUniquePositions(array $uniquePositions) {
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if (array_key_exists("{$i}:{$k}", $uniquePositions)) {
                    echo "O";
                } else {
                    echo $this->data[$i][$k];
                }
            }
            echo PHP_EOL;
        }
    }

    public function print(array $pos, array $dir)
    {
        for ($i = 0; $i < $this->rows; $i++) {
            for ($k = 0; $k < $this->cols; $k++) {
                if ([$i, $k] === $pos) {
                    switch ($dir[0]) {
                        case 1:
                            echo "v";
                            break;

                        case -1:
                            echo "^";
                            break;
                    }
                    switch ($dir[1]) {
                        case 1:
                            echo ">";
                            break;

                        case -1:
                            echo "<";
                            break;
                    }
                } else {
                    echo $this->data[$i][$k];
                }
            }
            echo PHP_EOL;
        }
    }
}
