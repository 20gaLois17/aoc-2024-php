<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();

        $board = new Board(sizeof($input), strlen($input[0]));
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $symbol = $input[$i][$k];
                if ($symbol === ".") {
                    continue;
                }
                $board->addAntenna($symbol, [$i, $k]);
            }
        }

        foreach($board->getAntennas() as $type) {
            for ($i = 0; $i < sizeof($type) - 1; $i++) {
                for ($k = $i+1; $k < sizeof($type); $k++) {
                    $diff = $this->sub($type[$k], $type[$i]);
                    $board->addAntinode($this->add($type[$k], $diff));
                    $board->addAntinode($this->add($type[$i], $this->flip($diff)));
                }
            }
        }

        return sizeof($board->getAntinodes());
    }

    public function second()
    {
        $input = $this->input->load();

        $board = new Board(sizeof($input), strlen($input[0]));
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[$i]); $k++) {
                $symbol = $input[$i][$k];
                if ($symbol === ".") {
                    continue;
                }
                $board->addAntenna($symbol, [$i, $k]);
            }
        }

        foreach($board->getAntennas() as $type) {
            for ($i = 0; $i < sizeof($type) - 1; $i++) {
                for ($k = $i+1; $k < sizeof($type); $k++) {
                    $diff = $this->sub($type[$k], $type[$i]);
                    $board->addAntinodes($type[$k], $diff);
                    $board->addAntinodes($type[$i], $this->flip($diff));
                }
            }
        }

        return sizeof($board->getAntinodes());
    }

    public static function flip(array $vec): array
    {
        return [
            -$vec[0],
            -$vec[1]
        ];
    }

    public static function add(array $vec1, array $vec2): array
    {
        return [
            $vec1[0] + $vec2[0],
            $vec1[1] + $vec2[1]
        ];
    }
    public static function sub(array $vec1, array $vec2): array
    {
        return [
            $vec1[0] - $vec2[0],
            $vec1[1] - $vec2[1]
        ];
    }
}

class Board
{
    private array $antennas;
    private array $antinodes;
    public function __construct(
        private readonly int $rows,
        private readonly int $cols
    ) {
    }

    public function addAntenna(string $type, array $pos): void
    {
        $this->antennas[$type][] = $pos;
    }

    public function getAntennas(): array
    {
        return $this->antennas;
    }

    public function addAntinode(array $pos): void
    {
        if ($pos[0] < 0 || $pos[0] >= $this->rows) {
            return;
        }
        if ($pos[1] < 0 || $pos[1] >= $this->rows) {
            return;
        }
        $this->antinodes["{$pos[0]}:{$pos[1]}"] = 1;
    }

    public function addAntinodes(array $pos, array $dir): void
    {
        while (true) {
            $this->antinodes["{$pos[0]}:{$pos[1]}"] = 1;
            $pos = Solution::add($pos, $dir);
            if ($pos[0] < 0 || $pos[0] >= $this->rows) {
                return;
            }
            if ($pos[1] < 0 || $pos[1] >= $this->rows) {
                return;
            }
        }
    }

    public function getAntinodes(): array
    {
        return $this->antinodes;
    }
}
