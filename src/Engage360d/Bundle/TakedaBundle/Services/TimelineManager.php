<?php

namespace Engage360d\Bundle\TakedaBundle\Services;

use Elastica\Exception\NotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimelineManager
{
    const COLLECTION_NAME = 'timeline';

    const TYPE_EXERCISE = 'exercise';
    const TYPE_DIET = 'diet';
    const TYPE_PILL = 'pill';

    const EXERCISE_DAILY_RECOMMENDED_DURATION = 30;

    private $container = null;
    private $user = null;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function isExerciseCompleted($exerciseMins)
    {
        return $exerciseMins >= self::EXERCISE_DAILY_RECOMMENDED_DURATION;
    }

    public function getCollection()
    {
        $manager = $this->container->get('doctrine_mongodb.odm.default_connection');
        $db = $manager->selectDatabase(sprintf(
            "%s_%s",
            $this->container->getParameter('database_name'),
            $this->container->get('kernel')->getEnvironment()
        ));

        return $db->selectCollection(self::COLLECTION_NAME);
    }

    public function getTimeline()
    {
        $collection = $this->getCollection();

        $timeline = $collection->findOne(["_id" => new \MongoId($this->user->getTimelineId())]);
        if (!$timeline) {
            $timeline = $this->generateTimeline();
            $collection->insert($timeline);

            $this->user->setTimelineId($timeline["_id"]);

            $em = $this->container->get('doctrine')->getManager();
            $em->persist($this->user);
            $em->flush();
        } else {
            $timeline = $this->updateTimeline($timeline);
            $this->getCollection()->save($timeline);
        }

        return $timeline;
    }

    public function generateTimeline()
    {
        $firstTestResult = $this->user->getTestResults()->first();
        if (!$firstTestResult) {
            throw new NotFoundHttpException("Timeline will be created when you pass the test.");
        }

        $timelineDate = $firstTestResult->getCreatedAt();
        $timelineDate->setTime(0, 0, 0);

        $today = new \DateTime();

        $timeline = [
            "data" => [],
            "linked" => [
                "tasks" => []
            ]
        ];

        while ($timelineDate->format('U') <= $today->format('U')) {
            $date = [
                "date" => $timelineDate->format('Y-m-d'),
                "links" => [
                    "tasks" => []
                ]
            ];

            $taskId = $timelineDate->format('Ymd') . '01';
            $date["links"]["tasks"][] = $taskId;
            $timeline["linked"]["tasks"][] = [
                "id" => $taskId,
                "type" => "exercise",
                "exerciseMins" => 0,
                "isCompleted" => null,
            ];

            $taskId = $timelineDate->format('Ymd') . '02';
            $date["links"]["tasks"][] = $taskId;
            $timeline["linked"]["tasks"][] = [
                "id" => $taskId,
                "type" => "diet",
                "isCompleted" => null,
            ];

            foreach ($this->user->getPills() as $index => $pill) {
                if (
                    $timelineDate->format('U') >= $pill->getSinceDate()->format('U') &&
                    $timelineDate->format('U') <= $pill->getTillDate()->format('U')
                ) {
                    // Use pill ID as a suffix of a task ID,
                    // to make the latter unequivocally map to the former.
                    // For example, using index here may result in confusion,
                    // when we delete a pill, then add a new one, and then update the timeline.
                    $taskId = $timelineDate->format('Ymd') . str_pad(($pill->getId() + 2), 2, '0', STR_PAD_LEFT);
                    $date["links"]["tasks"][] = $taskId;
                    $timeline["linked"]["tasks"][] = [
                        "id" => $taskId,
                        "type" => "pill",
                        "isCompleted" => null,
                        "links" => [
                            "pill" => (string) $pill->getId()
                        ]
                    ];
                }
            }

            $timeline["data"][] = $date;

            $timelineDate->add(new \DateInterval('P1D'));
        }

        return $timeline;
    }

    private function getTaskDateStr($task)
    {
        return sprintf(
            "%s-%s-%s",
            substr($task["id"], 0, 4),
            substr($task["id"], 4, 2),
            substr($task["id"], 6, 2)
        );
    }

    private function updateTimeline($timeline)
    {
        $newTimeline = $this->generateTimeline();

        $newTaskIds = [];
        $newDateToIndexMapping = [];
        foreach ($newTimeline["data"] as $index => $day) {
            $newTaskIds = array_merge($newTaskIds, $day["links"]["tasks"]);
            $newTaskIds = array_unique($newTaskIds);
            $newDateToIndexMapping[$day["date"]] = $index;
        }

        $oldTaskIds = [];
        $oldDateToIndexMapping = [];
        foreach ($timeline["data"] as $index => $day) {
            $oldTaskIds = array_merge($oldTaskIds, $day["links"]["tasks"]);
            $oldTaskIds = array_unique($oldTaskIds);
            $oldDateToIndexMapping[$day["date"]] = $index;
        }

        // Delete old excessive tasks
        // Step 1
        $linkedTasksCopy = [];
        foreach ($timeline["linked"]["tasks"] as $task) {
            if (in_array($task["id"], $newTaskIds)) {
                $linkedTasksCopy[] = $task;
            }
        }
        $timeline["linked"]["tasks"] = $linkedTasksCopy;

        // Step 2
        // Warning! Don't use unset or array_splice within the loop,
        // it will cause side effects.
        for ($i = 0; $i < count($timeline["data"]); $i++) {
            $taskIdsCopy = [];
            for ($j = 0; $j < count($timeline["data"][$i]["links"]["tasks"]); $j++) {
                if (in_array($timeline["data"][$i]["links"]["tasks"][$j], $newTaskIds)) {
                    $taskIdsCopy[] = $timeline["data"][$i]["links"]["tasks"][$j];
                }
            }
            $timeline["data"][$i]["links"]["tasks"] = $taskIdsCopy;
        }

        // Add new tasks to old timeline
        for ($i = 0; $i < count($newTimeline["linked"]["tasks"]); $i++) {
            $task = $newTimeline["linked"]["tasks"][$i];
            if (!in_array($task["id"], $oldTaskIds)) {
                // Add missing task
                $timeline["linked"]["tasks"][] = $task;

                // Add task ID to data object
                $newDayIndex = $newDateToIndexMapping[$this->getTaskDateStr($task)];
                $oldDayIndex = isset($oldDateToIndexMapping[$this->getTaskDateStr($task)]) ?
                    $oldDateToIndexMapping[$this->getTaskDateStr($task)] : null;

                if (!is_null($oldDayIndex)) {
                    if (!in_array($task["id"], $timeline["data"][$oldDayIndex]["links"]["tasks"])) {
                        $timeline["data"][$oldDayIndex]["links"]["tasks"][] = $task["id"];
                    }
                } else {
                    $timeline["data"][] = $newTimeline["data"][$newDayIndex];
                    $oldDateToIndexMapping[$this->getTaskDateStr($task)] = count($timeline["data"]) - 1;
                }
            }
        }

        return $timeline;
    }

    public function updateTask($id, $data)
    {
        $timeline = $this->getTimeline();

        $selectedTask = null;

        $timeline["linked"]["tasks"] = array_map(function ($task) use($id, $data, &$selectedTask) {
            if ($task["id"] === $id) {
                // Handle invalid data
                if (isset($data->data->isCompleted) && $task["type"] === self::TYPE_EXERCISE) {
                    throw new HttpException(400, sprintf("Task w/ type '%s' requires only one field -- 'exerciseMins'", self::TYPE_EXERCISE));
                }
                if (isset($data->data->exerciseMins) && $task["type"] !== self::TYPE_EXERCISE) {
                    throw new HttpException(400, sprintf("Field 'exerciseMins' is allowed only in tasks w/ type '%s'", self::TYPE_EXERCISE));
                }
                if ($task["isCompleted"] !== null) {
                    throw new HttpException(409, "You can't change the task that's already been completed");
                }

                // Write data
                if (isset($data->data->isCompleted)) {
                    $task["isCompleted"] = $data->data->isCompleted;
                }
                if (isset($data->data->exerciseMins)) {
                    $task["exerciseMins"] = $data->data->exerciseMins;
                    $task["isCompleted"] = $this->isExerciseCompleted($data->data->exerciseMins);
                }

                $selectedTask = $task;
            }

            return $task;

        }, $timeline["linked"]["tasks"]);

        if (!$selectedTask) {
            throw new NotFoundException(sprintf("No task with id %s found.", $id));
        }

        $this->getCollection()->save($timeline);

        return $selectedTask;
    }
}