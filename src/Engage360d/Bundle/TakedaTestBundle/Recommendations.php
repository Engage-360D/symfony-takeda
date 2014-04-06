<?php

namespace Engage360d\Bundle\TakedaTestBundle;

use Symfony\Component\Yaml\Parser;
use Engage360d\Bundle\TakedaTestBundle\Entity\TestResult;
use Engage360d\Bundle\TakedaTestBundle\Helpers;


class Recommendations
{
    private static $cfg = null;

    public static function match($id, $smoking, $arterialPressure, $extraSalt, $cholesterolLevel, $physicalActivity, $bmi)
    {
        if (!self::$cfg) {
            $yaml = new Parser();
            self::$cfg = $yaml->parse(file_get_contents(__DIR__ . '/Resources/recommendations.yml'));
        }

        $res = array();

        $res['smoking'] = self::$cfg['smoking'][$smoking ? 'yes' : 'no'];

        $res['arterialPressure'] = self::$cfg['arterialPressure'][Helpers::roundToMax($arterialPressure, array_keys(self::$cfg['arterialPressure']))];

        $res['extraSalt'] = self::$cfg['extraSalt'][$extraSalt ? 'yes' : 'no'];

        $res['cholesterolLevel'] = self::$cfg['cholesterolLevel'][Helpers::roundToMax($cholesterolLevel, array_keys(self::$cfg['cholesterolLevel']))];

        $res['physicalActivity'] = self::$cfg['physicalActivity'][Helpers::roundToMax($physicalActivity, array_keys(self::$cfg['physicalActivity']))];

        $res['bmi'] = self::$cfg['bmi'][Helpers::roundToMax($bmi, array_keys(self::$cfg['bmi']))];
        
        foreach (array_keys($res) as $key) {
            if ($res[$key]) {
                $res[$key]['url'] = "/tests/" . $id . "/" . preg_replace_callback('/([A-Z])/', function ($matches) {
                    return '-' . strtolower($matches[0]);
                }, $key);

                $res[$key]['pageTitle'] = str_replace('?', $$key, $res[$key]['pageTitle']);
                $res[$key]['pageSubtitle'] = str_replace('?', $$key, $res[$key]['pageSubtitle']);
            }
        }

        return $res;
    }
}
