<?php

namespace Engage360d\Bundle\TakedaTestBundle\Processing;

use Symfony\Component\Routing\RouterInterface;
use Engage360d\Bundle\TakedaTestBundle\Entity\TestResult;

function dbg()
{
  echo header('Content-Type: application/json');
  echo json_encode(func_get_args());die;
}

class RecommendationsMatcher
{
    private $testResult;
    private $declaration;
    private $router;
  
    public function __construct(TestResult $testResult, $declaration, RouterInterface $router)
    {
        $this->testResult = $testResult;
        $this->declaration = $declaration;
        $this->router = $router;
    }

    public function match()
    {
        $recommendations = array(
            'scoreDescription' => $this->choiceFirstVisible($this->declaration['scoreDescription']),
            'dangerAlert' => $this->choiceFirstVisible($this->declaration['dangerAlert']),
            'mainRecommendation' => $this->choiceFirstVisible($this->declaration['mainRecommendation']),
            'institutionsUrl' => $this->isVisible($this->declaration['institutionsUrl']),
            'banners' => array(
                'smoking' => $this->choiceFirstVisible($this->declaration['banners']['smoking']),
                'arterialPressure' => $this->choiceFirstVisible($this->declaration['banners']['arterialPressure']),
                'extraSalt' => $this->choiceFirstVisible($this->declaration['banners']['extraSalt']),
                'cholesterolLevel' => $this->choiceFirstVisible($this->declaration['banners']['cholesterolLevel']),
                'physicalActivity' => $this->choiceFirstVisible($this->declaration['banners']['physicalActivity']),
                'bmi' => $this->choiceFirstVisible($this->declaration['banners']['bmi']),
                'sugarProblems' => $this->choiceFirstVisible($this->declaration['banners']['sugarProblems']),
                'arterialPressureDrugs' => $this->choiceFirstVisible($this->declaration['banners']['arterialPressureDrugs']),
                'cholesterolDrugs' => $this->choiceFirstVisible($this->declaration['banners']['cholesterolDrugs']),
            ),
            'pages' => array(
                'smoking' => $this->choiceFirstVisible($this->declaration['pages']['smoking']),
                'arterialPressure' => $this->choiceFirstVisible($this->declaration['pages']['arterialPressure']),
                'extraSalt' => $this->choiceFirstVisible($this->declaration['pages']['extraSalt']),
                'cholesterolLevel' => $this->choiceFirstVisible($this->declaration['pages']['cholesterolLevel']),
                'physicalActivity' => $this->choiceFirstVisible($this->declaration['pages']['physicalActivity']),
                'bmi' => $this->choiceFirstVisible($this->declaration['pages']['bmi']),
            ),
        );

        foreach ($recommendations['banners'] as $key => $banner) {
            $recommendations['banners'][$key] = $this->normalizeBanner($key, $banner);
        }
        
        foreach ($recommendations['pages'] as $key => $page) {
            $recommendations['pages'][$key] = $this->normalizePage($key, $page);
        }

        return $recommendations;
    }

    private function normalizeBanner($key, $banner)
    {
        if (!is_array($banner)) {
            return $banner;
        } else if ($banner['pageUrl']) {
            $banner['pageUrl'] = $this->router->generate('engage360d_takeda_test_get_test_result_recommendation', array(
                'id' => $this->testResult->getId(),
                'type' => $key,
            ));
        } else {
            $banner['pageUrl'] = null;
        }
        
        return $banner;
    }
    
    private function normalizePage($key, $page)
    {
        if (!is_array($page)) {
            return $page;
        }

        $page['institutionsUrl'] = $this->isVisible($page['institutionsUrl']);
        $page['title'] = str_replace('?', $this->getPropertyValue($key), $page['title']);
        return $page;
    }

    private function choiceFirstVisible($items)
    {
        foreach ($items as $item) {
            if ($this->isVisible($item)) {
                $result = array_merge(array(), $item);
                unset($result['visible']);
                return $result;
            }
        }

        return null;
    }
    
    private function isVisible($item)
    {
        if (!isset($item['visible'])) {
            return false;
        }
        
        if (is_bool($item['visible'])) {
            return $item['visible'];
        }
        
        foreach ($item['visible'] as $rule) {
            if ($this->testRule($rule)) {
                return true;
            }
        }

        return false;
    }
    
    private function testRule($rule)
    {
        foreach ($rule as $property => $matcher) {
            $value = $this->getPropertyValue($property);
            $match = false;

            if (is_array($matcher)) {
                $match = $this->matchRange($value, $matcher);
            } else if (is_bool($matcher)) {
                $match = $this->matchBool($value, $matcher);
            } else {
                throw new \Exception();
            }

            if (!$match) {
                return false;
            }
        }

        return true;
    }

    private function matchRange($value, $range)
    {
        return ($range[0] === null || $range[0] <= $value) && ($range[1] === null || $range[1] >= $value);
    }
    
    private function matchBool($value, $match)
    {
        return $value === $match;
    }

    private function getPropertyValue($property)
    {
        $isAccessorMethod = 'is' . ucfirst($property);
        $getAccessorMethod = 'get' . ucfirst($property);
        
        if (is_callable(array($this->testResult, $isAccessorMethod))) {
            return $this->testResult->{$isAccessorMethod}();
        } else if (is_callable(array($this->testResult, $getAccessorMethod))) {
            return $this->testResult->{$getAccessorMethod}();
        }

        throw new \Exception();
    }
}
