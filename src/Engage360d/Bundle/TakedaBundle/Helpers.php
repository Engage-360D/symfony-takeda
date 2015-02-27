<?php

namespace Engage360d\Bundle\TakedaBundle;

class Helpers
{
    public static function roundToNearest($value, $points) {
        $distances = array_map(function ($point) use ($value) {
            return array(
                "distance" => abs($point - $value),
                "point" => $point,
            );
        }, $points);
        usort($distances, function($a, $b) {
            return $a["distance"] - $b["distance"];
        });
        return $distances[0]["point"];
    }
    
    public static function roundToMax($value, $points)
    {
        sort($points);

        foreach ($points as $point) {
            $result = $point;
            if ($point > $value) {
                break;
            }
        }

        return $result;
    }

    public static function inclineNounByNumber($n, $options = ["слово", "слова", "слов"])
    {
        $n = $n % 100;

        if ($n > 10 && $n < 20) {
            return $options[2];
        } else if ($n % 10 === 1) {
            return $options[0];
        } else if ($n % 10 > 1 && $n % 10 < 5) {
            return $options[1];
        } else {
            return $options[2];
        }
    }
}
