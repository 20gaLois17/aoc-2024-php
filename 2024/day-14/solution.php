<?php

class Solution extends AdventOfCode\Solution
{
    public function first(): int
    {
        $input = $this->input->load();
        $robots = $this->parseInput($input);

        for ($i = 0; $i < 100; $i++) {
            foreach ($robots as $robot) {
                $robot->move();
            }
        }

        $q1 = 0;
        $q2 = 0;
        $q3 = 0;
        $q4 = 0;

        for ($i = 0; $i < (Robot::WIDTH-1)/2; $i++) {
            for ($k = 0; $k < (Robot::HEIGHT-1)/2; $k++) {
                foreach ($robots as $robot) {
                    if ($robot->getPosition() === [$i, $k]) {
                        $q1++;
                    }
                }
            }
        }

        // second quadrant
        for ($i = (Robot::WIDTH-1)/2 + 1; $i < Robot::WIDTH; $i++) {
            for ($k = 0; $k < (Robot::HEIGHT-1)/2; $k++) {
                foreach ($robots as $robot) {
                    if ($robot->getPosition() === [$i, $k]) {
                        $q2++;
                    }
                }
            }
        }

        // third quadrant
        for ($i = 0; $i < (Robot::WIDTH-1)/2; $i++) {
            for ($k = (Robot::HEIGHT-1)/2 + 1; $k < Robot::HEIGHT; $k++) {
                foreach ($robots as $robot) {
                    if ($robot->getPosition() === [$i, $k]) {
                        $q3++;
                    }
                }
            }
        }

        for ($i = (Robot::WIDTH-1)/2 + 1; $i < Robot::WIDTH; $i++) {
            for ($k = (Robot::HEIGHT-1)/2 + 1; $k < Robot::HEIGHT; $k++) {
                foreach ($robots as $robot) {
                    if ($robot->getPosition() === [$i, $k]) {
                        $q4++;
                    }
                }
            }
        }

        return $q1*$q2*$q3*$q4;
    }

    public function second(): void
    {
        $input = $this->input->load();
        $input = $this->input->load();
        $robots = $this->parseInput($input);

        $i = 0;
        while (true) {
            $i++;
            echo "------{$i}------" . PHP_EOL;
            foreach ($robots as $robot) {
                $robot->move();
            }
            // looking at the rendered output, there is one suspicous image which periodically
            // appears every 103 iterations, starting with the 89th iteration
            if (($i - 89) % 103 === 0) {
                $this->drawBoard($robots);
                readline();
            }
        }
    }

    private function parseInput(array $input): array
    {
        $result = [];
        foreach ($input as $line) {
            $chunks = explode(" ", $line);
            $pos = explode(",", str_replace("p=", "", $chunks[0]));
            $vel = explode(",", str_replace("v=", "", $chunks[1]));
            $robot = new Robot(
                [(int)$pos[0], (int)$pos[1]],
                [(int)$vel[0], (int)$vel[1]]
            );
            $result[] = $robot;
        }
        return $result;
    }

    private function drawBoard(array $robots): void
    {
        for ($k = 0; $k < Robot::HEIGHT; $k++) {
            for ($i = 0; $i < Robot::WIDTH; $i++) {
                $hasRobot = false;
                foreach ($robots as $robot) {
                    if ($robot->getPosition() === [$i, $k]) {
                        $hasRobot = true;
                        break;
                    }
                }
                if ($hasRobot) {
                    echo "X";
                } else {
                    echo " ";
                }
            }
            echo PHP_EOL;
        }
    }
}

class Robot
{
    // const WIDTH = 11;
    // const HEIGHT = 7;
    const WIDTH = 101;
    const HEIGHT = 103;

    private array $position;

    public function __construct(
        private readonly array $startingPosition,
        private readonly array $velocity
    ) {
        $this->position = $startingPosition;
    }

    public function move(): void
    {
        $this->position[0] += $this->velocity[0];
        $this->position[1] += $this->velocity[1];
        if ($this->position[0] < 0) {
            $this->position[0] += self::WIDTH;
        }
        if ($this->position[1] < 0) {
            $this->position[1] += self::HEIGHT;
        }
        if ($this->position[0] > self::WIDTH-1) {
            $this->position[0] -= self::WIDTH;
        }
        if ($this->position[1] > self::HEIGHT-1) {
            $this->position[1] -= self::HEIGHT;
        }
    }
    public function getPosition(): array
    {
        return $this->position;
    }
}
