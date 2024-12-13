<?php

enum Directions {
    case UP;
    case DOWN;
    case LEFT;
    case RIGHT;
    case UP_RIGHT;
    case DOWN_RIGHT;
    case UP_LEFT;
    case DOWN_LEFT;
}

class Solution extends AdventOfCode\Solution
{
    const TARGET_WORD = "XMAS";
    const TARGET_WORD_2 = "MAS";

    public function first(): int
    {
        $input = $this->input->load();
        $count = 0;
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[0]); $k++) {
                foreach (Directions::cases() as $direction) {
                    if ($this->getWord($input, $direction, [$i, $k], strlen(self::TARGET_WORD)) === self::TARGET_WORD) {
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    public function second(): int
    {
        $input = $this->input->load();
        // valid X-MAS words will share the positio of the 'A'
        $dict = [];

        $count = 0;
        for ($i = 0; $i < sizeof($input); $i++) {
            for ($k = 0; $k < strlen($input[0]); $k++) {
                foreach ([Directions::DOWN_LEFT, Directions::DOWN_RIGHT, Directions::UP_LEFT, Directions::UP_RIGHT] as $direction) {
                    if ($this->getWord($input, $direction, [$i, $k], strlen(self::TARGET_WORD_2)) === self::TARGET_WORD_2) {
                        $center = [$i, $k];
                        switch ($direction) {
                            case Directions::UP_RIGHT:
                                $center[0]--;
                                $center[1]++;
                                break;

                            case Directions::UP_LEFT:
                                $center[0]--;
                                $center[1]--;
                                break;

                            case Directions::DOWN_RIGHT:
                                $center[0]++;
                                $center[1]++;
                                break;

                            case Directions::DOWN_LEFT:
                                $center[0]++;
                                $center[1]--;
                                break;
                        }

                        $dict[] = [
                            "idx" => "{$i}:{$k}",
                            "center" => "{$center[0]}:{$center[1]}"
                        ];
                    }
                }
            }
        }
        print_r($dict);
        for ($i = 0; $i < sizeof($dict)-1; $i++) {
            for ($k = $i+1; $k < sizeof($dict); $k++) {
                if ($dict[$i]["center"] === $dict[$k]["center"]) {
                    $count++;
                }
            }
        }
        // count pairs

        return $count;
    }

    private function getWord(array &$input, Directions $direction, array $position, int $length): string
    {
        $word = "";
        for ($i = 0; $i < $length; $i++) {
            $char = $this->getChar($input, $position);
            $word .= $char;
            switch ($direction) {
                case Directions::UP:
                    $position[0]--;
                    break;

                case Directions::DOWN:
                    $position[0]++;
                    break;

                case Directions::RIGHT:
                    $position[1]++;
                    break;

                case Directions::LEFT:
                    $position[1]--;
                    break;

                case Directions::UP_RIGHT:
                    $position[0]--;
                    $position[1]++;
                    break;

                case Directions::UP_LEFT:
                    $position[0]--;
                    $position[1]--;
                    break;

                case Directions::DOWN_RIGHT:
                    $position[0]++;
                    $position[1]++;
                    break;

                case Directions::DOWN_LEFT:
                    $position[0]++;
                    $position[1]--;
                    break;

                default:
                    throw new \Exception("Unknown direction");
            }
        }

        return $word;
    }

    private function getChar(array $input, array $position): string
    {
        $row = $position[0];
        $col = $position[1];
        if ($row < 0 || $row >= sizeof($input)) {
            return "";
        }
        if ($col < 0 || $col >= strlen($input[0])) {
            return "";
        }
        return $input[$row][$col];
    }
}
