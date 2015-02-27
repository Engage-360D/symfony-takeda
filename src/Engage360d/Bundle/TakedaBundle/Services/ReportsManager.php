<?php

namespace Engage360d\Bundle\TakedaBundle\Services;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Engage360d\Bundle\TakedaBundle\Helpers;

/**
 * Class ReportsManager
 *
 * TODO decompose it!!!
 *
 * @package Engage360d\Bundle\TakedaBundle\Services
 */
class ReportsManager
{
    const TYPE_EXERCISE          = 'exercise';
    const TYPE_DIET              = 'diet';
    const TYPE_PILLS             = 'pills';
    const TYPE_ISR               = 'isr';
    const TYPE_SCORE             = 'score';
    const TYPE_ARTERIAL_PRESSURE = 'arterialPressure';
    const TYPE_WEIGHT            = 'weight';
    const TYPE_CHOLESTEROL_LEVEL = 'cholesterolLevel';

    const STATUS_OK              = 'icon-ok-circle-big-thin';
    const STATUS_BAD             = 'icon-close-circle';
    const STATUS_NULL            = 'icon-dash-circle-big';

    const PERIOD_FORMAT_WEEK     = 'W';
    const PERIOD_FORMAT_MONTH    = 'M';

    private $container;
    private $user;
    private $lastTestResult;
    private $beforeLastTestResult;
    private $timelineManager;
    private $timeline;

    /**
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param UserInterface $user
     */
    public function init(UserInterface $user)
    {
        $this->user = $user;

        $testResults = $this->user->getTestResults();
        $this->lastTestResult = $testResults->get(count($testResults) - 1);
        $this->beforeLastTestResult = $testResults->get(count($testResults) - 2);

        $timelineManager = $this->container->get('engage360d_takeda.timeline_manager');
        $timelineManager->setUser($user);
        $this->timelineManager = $timelineManager;

        $this->timeline = $timelineManager->getTimeline();
    }

    /**
     * @param $reportType
     * @param bool $isList
     * @return string
     */
    public function getReportItemTitle($reportType, $isList = true)
    {
        $titles = [
            self::TYPE_SCORE => $isList ?
                'SCORE<p>результаты анализа</p>' :
                '<strong>SCORE</strong> результаты анализа',
            self::TYPE_ISR => $isList ?
                'ИСР<p>Индекс соблюдения рекомендаций</p>' :
                '<strong>ИСР</strong> Индекс соблюдения рекомендаций',
            self::TYPE_EXERCISE => 'Физическая нагрузка',
            self::TYPE_PILLS => 'Соблюдение режима приема лекарств',
            self::TYPE_DIET => 'Соблюдение диеты',
            self::TYPE_ARTERIAL_PRESSURE => 'Давление',
            self::TYPE_WEIGHT => 'Масса',
            self::TYPE_CHOLESTEROL_LEVEL => 'Холестерин',
        ];

        return isset($titles[$reportType]) ? $titles[$reportType] : '' ;
    }

    public function getReportItemUnit($reportType, $value = null)
    {
        $titles = [
            self::TYPE_SCORE             => '',
            self::TYPE_ISR               => '%',
            self::TYPE_EXERCISE          => ' ' . Helpers::inclineNounByNumber($value, ["минута", "минуты", "минут"]),
            self::TYPE_PILLS             => '%',
            self::TYPE_DIET              => '%',
            self::TYPE_ARTERIAL_PRESSURE => '',
            self::TYPE_WEIGHT            => ' кг',
            self::TYPE_CHOLESTEROL_LEVEL => '',
        ];

        return isset($titles[$reportType]) ? $titles[$reportType] : '' ;
    }

    /**
     * @return array
     */
    public function getReportsList()
    {
        $list = [
            [
                "title" => $this->getReportItemTitle(self::TYPE_SCORE),
                "uri" => self::TYPE_SCORE,
                "value" => $this->lastTestResult->getScore(),
                "statusClass" => $this->getStatusClass(self::TYPE_SCORE),
            ],
            [
                "title" => $this->getReportItemTitle(self::TYPE_ISR),
                "uri" => self::TYPE_ISR,
                "value" => $this->getIsrWeekValue(date("W")) . $this->getReportItemUnit(self::TYPE_ISR),
                "statusClass" => $this->getStatusClass(self::TYPE_ISR),
            ],
            [
                "title" => $this->getReportItemTitle(self::TYPE_EXERCISE),
                "uri" => self::TYPE_EXERCISE,
                "value" => sprintf(
                    "%s%s",
                    $this->getExerciseWeekValue(date("W")),
                    $this->getReportItemUnit(self::TYPE_EXERCISE, $this->getExerciseWeekValue(date("W")))
                ),
                "statusClass" => $this->getStatusClass(self::TYPE_EXERCISE),
            ],
            [
                "title" => $this->getReportItemTitle(self::TYPE_PILLS),
                "uri" => self::TYPE_PILLS,
                "value" => $this->getPillsWeekValue(date("W")) . $this->getReportItemUnit(self::TYPE_PILLS),
                "statusClass" => $this->getStatusClass(self::TYPE_PILLS)
            ],
            [
                "title" => $this->getReportItemTitle(self::TYPE_DIET),
                "uri" => self::TYPE_DIET,
                "value" => $this->getDietWeekValue(date("W")) . $this->getReportItemUnit(self::TYPE_DIET),
                "statusClass" => $this->getStatusClass(self::TYPE_DIET),
            ],

            // TODO when weekly task will be ready, get arterialPressure and weight
            // from user's properties which will hold its current values
            [
                "title" => $this->getReportItemTitle(self::TYPE_ARTERIAL_PRESSURE),
                "uri" => self::TYPE_ARTERIAL_PRESSURE,
                "value" => $this->lastTestResult->getArterialPressure(),
                "statusClass" => $this->getStatusClass(self::TYPE_ARTERIAL_PRESSURE),
            ],
            [
                "title" => $this->getReportItemTitle(self::TYPE_WEIGHT),
                "uri" => self::TYPE_WEIGHT,
                "value" => $this->lastTestResult->getWeight() . $this->getReportItemUnit(self::TYPE_WEIGHT),
                "statusClass" => $this->getStatusClass(self::TYPE_WEIGHT),
            ],

            [
                "title" => $this->getReportItemTitle(self::TYPE_CHOLESTEROL_LEVEL),
                "uri" => self::TYPE_CHOLESTEROL_LEVEL,
                "value" => $this->lastTestResult->getCholesterolLevel(),
                "statusClass" => $this->getStatusClass(self::TYPE_CHOLESTEROL_LEVEL),
            ],
        ];

        return $list;
    }

    /**
     * @param $reportType
     * @return array
     */
    public function getReport($reportType)
    {
        $value = call_user_func([$this, 'get' . ucfirst($reportType) . 'CurrentValue'], date("W"));
        $data = [
            "title" => $this->getReportItemTitle($reportType, $isList = false),
            "currentValue" => $value . $this->getReportItemUnit($reportType, $value),
            "statusClass" => $this->getStatusClass($reportType),
            "periodFormat" => $this->getPeriodFormat($reportType),
            "data" => $this->getData($reportType),
            "isWarningVisible" => $this->getIsWarningVisible($reportType),
            "valueNote" => $reportType === self::TYPE_ISR ? $this->getValueNote() : "",
        ];

        return $data;
    }

    public function getValueNote()
    {
        $valueNote = '';

        if (!$this->isThisWeekTheFirstWeek()) {
            $currentValue = $this->getIsrWeekValue(date("W"));
            $lastWeek = strtotime("-1 week");
            $previousValue = $this->getIsrWeekValue(date("W", $lastWeek), date("Y", $lastWeek));
            if ($currentValue != $previousValue) {
                $valueNote = sprintf(
                    "За прошулю неделю он %s на %s",
                    $currentValue > $previousValue ? "вырос" : "упал",
                    abs($currentValue - $previousValue)

                );
            } else {
                $valueNote = 'За прошулю неделю он не изменился';
            }
        }

        return $valueNote;
    }

    public function getIsWarningVisible($reportType)
    {
        $recommendations = $this->lastTestResult->getRecommendations();

        if ($this->getPeriodFormat($reportType) === self::PERIOD_FORMAT_WEEK) {
            return false;
        }

        return (
                isset($recommendations["banners"][$reportType])
                && $recommendations["banners"][$reportType]["state"] === "attention"
            )
            ||
            (
                isset($recommendations["scoreNote"]["state"])
                && $recommendations["scoreNote"]["state"] === "attention"
            );
    }

    public function getData($reportType)
    {
        if ($this->getPeriodFormat($reportType) === self::PERIOD_FORMAT_MONTH) {
            return $this->getTestData($reportType);
        } else {
            return $this->getTimelineData($reportType);
        }
    }

    public function getTestData($reportType)
    {
        $data = [];
        $lastValue = null;
        foreach ($this->user->getTestResults() as $testResult) {
            $value = call_user_func([$testResult, 'get' . ucfirst($reportType)]);
            $recommendations = $testResult->getRecommendations();
            if (isset($recommendations["banners"][$reportType]["state"])) {
                $isDynamicPositive =  $recommendations["banners"][$reportType]["state"] !== "attention";
            } else if (isset($recommendations["scoreNote"]["state"])) {
                $isDynamicPositive = $recommendations["scoreNote"]["state"] !== "attention";
            } else {
                $isDynamicPositive = $value <= $lastValue;
            }

            $data[] = [
                'date' => $testResult->getCreatedAt()->format('Y-m-d'),
                'value' => $value,
                'isDynamicPositive' => $isDynamicPositive
            ];

            $lastValue = $value;
        }

        return $data;
    }

    public function getTimelineData($reportType)
    {
        $timestamp = $this->getBeginningOfTimeline()->format('U');

        $data = [];
        while ($timestamp < time()) {
            $week = date('W', $timestamp);
            $year = date('Y', $timestamp);
            $value = call_user_func([$this, 'get' . ucfirst($reportType) . 'WeekValue'], $week, $year);

            $data[] = [
                'date' => date('Y-m-d', $timestamp),
                'value' => $value,
                'isDynamicPositive' => $reportType === self::TYPE_EXERCISE ?
                    $this->isEnoughtExercisePerWeek($value) : $value == 100,
            ];

            $timestamp = strtotime("+1 week", $this->getWeekTimestamp($week, $year));
        }

        return $data;
    }

    public function isEnoughtExercisePerWeek($exerciseMins)
    {
        return $exerciseMins >= 150;
    }

    /**
     * @return \DateTime
     */
    public function getBeginningOfTimeline()
    {
        return new \DateTime($this->timeline["data"][0]["date"]);
    }

    /**
     * @return bool
     */
    public function isThisWeekTheFirstWeek()
    {
        $startOfThisWeek = strtotime(date("Y\\WW"));

        return $this->getBeginningOfTimeline()->format('U') > $startOfThisWeek;
    }

    /**
     * @param $reportType
     * @return string
     */
    public function getPeriodFormat($reportType)
    {
        switch ($reportType) {
            case self::TYPE_SCORE:
            case self::TYPE_ARTERIAL_PRESSURE:
            case self::TYPE_WEIGHT:
            case self::TYPE_CHOLESTEROL_LEVEL:
                $period = self::PERIOD_FORMAT_MONTH;
                break;
            default:
                $period = self::PERIOD_FORMAT_WEEK;
                break;
        }

        return $period;
    }

    /**
     * @param $reportType
     * @return string
     */
    public function getStatusClass($reportType)
    {
        if ($this->getPeriodFormat($reportType) === self::PERIOD_FORMAT_MONTH) {
            $statusClass = $this->getTestResultStatusClass($reportType);
        } else {
            $statusClass = $this->getTimelineActivityStatusClass($reportType);
        }

        return $statusClass;
    }

    /**
     * @param $property
     * @return string
     */
    public function getTestResultStatusClass($property) {
        if (!$this->lastTestResult || !$this->beforeLastTestResult) {
            return self::STATUS_NULL;
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $lastValue = $accessor->getValue($this->lastTestResult, $property);
        $beforeLastValue = $accessor->getValue($this->beforeLastTestResult, $property);

        if ($lastValue === $beforeLastValue) {
            return self::STATUS_NULL;
        }

        return $lastValue < $beforeLastValue ? self::STATUS_OK : self::STATUS_BAD;
    }

    /**
     * @param $activityName
     * @return string
     */
    public function getTimelineActivityStatusClass($activityName)
    {
        $currentWeekNum = date("W");

        if ($this->isThisWeekTheFirstWeek()) {
            return self::STATUS_NULL;
        }

        $methodName = 'get' . ucfirst($activityName) . 'WeekValue';

        $currentValue = $this->$methodName($currentWeekNum);
        $startOfLastWeek = strtotime("-1 week", strtotime(date("Y\\WW")));
        $previousValue = $this->$methodName(date("W", $startOfLastWeek), date("Y", $startOfLastWeek));

        if ($currentValue == 0 && $previousValue == 0) {
            return self::STATUS_NULL;
        }

        return $currentValue >= $previousValue ? self::STATUS_OK : self::STATUS_BAD;
    }

    /**
     * @param $weekNum
     * @param $year
     * @return int
     */
    public function getWeekTimestamp($weekNum, $year)
    {
        return strtotime(sprintf(
            "%sW%s",
            $year,
            str_pad($weekNum, 2, "0", STR_PAD_LEFT)
        ));
    }

    public function getScoreCurrentValue()
    {
        return $this->lastTestResult->getScore();
    }

    public function getIsrCurrentValue()
    {
        return $this->getIsrWeekValue(date('W'));
    }

    public function getExerciseCurrentValue()
    {
        return $this->getExerciseWeekValue(date('W'));
    }

    public function getPillsCurrentValue()
    {
        return $this->getPillsWeekValue(date('W'));
    }

    public function getDietCurrentValue()
    {
        return $this->getDietWeekValue(date('W'));
    }

    public function getArterialPressureCurrentValue()
    {
        return $this->lastTestResult->getArterialPressure();
    }

    public function getWeightCurrentValue()
    {
        return $this->lastTestResult->getWeight();
    }

    public function getCholesterolLevelCurrentValue()
    {
        return $this->lastTestResult->getCholesterolLevel();
    }

    /**
     * @param $weekNum
     * @param null $year
     * @return int
     */
    public function getExerciseWeekValue($weekNum, $year = null)
    {
        if (!$year) {
            $year = date("Y");
        }

        $mondayTimestamp = $this->getWeekTimestamp($weekNum, $year);
        $nextMondayTimestamp = strtotime("+1 week", $mondayTimestamp);

        $exerciseValue = 0;
        foreach ($this->timeline["linked"]["tasks"] as $task) {
            if (
                $task["type"] === TimelineManager::TYPE_EXERCISE &&
                (new \DateTime(substr($task["id"], 0, 8)))->format('U') >= $mondayTimestamp &&
                (new \DateTime(substr($task["id"], 0, 8)))->format('U') < $nextMondayTimestamp
            ) {
                $exerciseValue += $task["exerciseMins"];
            }
        }

        return $exerciseValue;
    }

    /**
     * @param $weekNum
     * @param null $year
     * @return float|int
     */
    public function getDietWeekValue($weekNum, $year = null)
    {
        if (!$year) {
            $year = date("Y");
        }

        $mondayTimestamp = $this->getWeekTimestamp($weekNum, $year);
        $nextMondayTimestamp = strtotime("+1 week", $mondayTimestamp);

        $dietValue = 0;
        $daysCount = 0;
        foreach ($this->timeline["linked"]["tasks"] as $task) {
            if (
                $task["type"] === TimelineManager::TYPE_DIET &&
                (new \DateTime(substr($task["id"], 0, 8)))->format('U') >= $mondayTimestamp &&
                (new \DateTime(substr($task["id"], 0, 8)))->format('U') < $nextMondayTimestamp
            ) {
                $dietValue += (int) $task["isCompleted"];
                $daysCount += 1;
            }
        }

        if (!$dietValue || !$daysCount) {
            return 0;
        }

        return round($dietValue / $daysCount, 2) * 100;
    }

    /**
     * @param $weekNum
     * @param null $year
     * @return float|int
     */
    public function getPillsWeekValue($weekNum, $year = null)
    {
        if (!$year) {
            $year = date("Y");
        }

        $mondayTimestamp = $this->getWeekTimestamp($weekNum, $year);
        $nextMondayTimestamp = strtotime("+1 week", $mondayTimestamp);

        $pillsByDay = [];
        foreach ($this->timeline["linked"]["tasks"] as $task) {
            $dateStr = substr($task["id"], 0, 8);
            if (
                $task["type"] === TimelineManager::TYPE_PILL &&
                (new \DateTime($dateStr))->format('U') >= $mondayTimestamp &&
                (new \DateTime($dateStr))->format('U') < $nextMondayTimestamp
            ) {
                if (!isset($pillsByDay[$dateStr])) {
                    $pillsByDay[$dateStr] = 1;
                }
                // По ремайндерам ставится зачет, если все тикеты по таблеткам закрыты.
                $pillsByDay[$dateStr] *= (int) $task["isCompleted"];
            }
        }

        if (empty($pillsByDay)) {
            return 0;
        }

        return round(array_sum(array_values($pillsByDay)) / count($pillsByDay), 2) * 100;
    }

    /**
     * @param $weekNum
     * @param null $year
     * @return float|int
     */
    public function getIsrWeekValue($weekNum, $year = null)
    {
        if (!$year) {
            $year = date("Y");
        }

        $mondayTimestamp = $this->getWeekTimestamp($weekNum, $year);
        $nextMondayTimestamp = strtotime("+1 week", $mondayTimestamp);

        $tasksByDay = [];
        foreach ($this->timeline["linked"]["tasks"] as $task) {
            $dateStr = substr($task["id"], 0, 8);
            if (
                (new \DateTime($dateStr))->format('U') >= $mondayTimestamp &&
                (new \DateTime($dateStr))->format('U') < $nextMondayTimestamp
            ) {
                if (!isset($tasksByDay[$dateStr])) {
                    $tasksByDay[$dateStr] = [];
                }
                if ($task["type"] === TimelineManager::TYPE_PILL) {
                    if (!isset($tasksByDay[$dateStr][TimelineManager::TYPE_PILL])) {
                        $tasksByDay[$dateStr][TimelineManager::TYPE_PILL] = 1;
                    }
                    $tasksByDay[$dateStr][TimelineManager::TYPE_PILL] *= (int) $task["isCompleted"];
                } else {
                    $tasksByDay[$dateStr][$task["type"]] = (int) $task["isCompleted"];
                }
            }
        }

        if (empty($tasksByDay)) {
            return 0;
        }

        $isrValue = 0;
        foreach ($tasksByDay as $day) {
            $isrValue += array_sum(array_values($day)) / count($day);
        }

        return round($isrValue / count($tasksByDay), 2) * 100;
    }
}