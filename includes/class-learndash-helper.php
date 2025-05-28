<?php

namespace AceTheTest_Dashboard\includes;
class LD_Helper
{

    public static function get_latest_test_scores_for_user($user_id, array $course_ids)
    {

        $results = [];

        foreach ($course_ids as $course_id) {

            // 1. Find every quiz that belongs to this course.
            $quizzes = learndash_get_course_quiz_list($course_id); // Legacy but still supported
            if (count($quizzes)) {
                $scores = self::get_quiz_scores($user_id, $quizzes);
                $avg = array_sum($scores) / count($scores);
            }
            echo 'quizes for course: ' . $course_id;
            echo json_encode($quizzes);

            $lessons = learndash_get_lesson_list($course_id);
            echo 'lessons for course: ' . $course_id;
            foreach ($lessons as $lesson) {
                $quizzes = learndash_get_lesson_quiz_list($lesson->ID);
                echo 'quizes for lesson: ' . $lesson->ID;
                echo json_encode($quizzes);
                $scores = self::get_quiz_scores($user_id, $quizzes);
            }
        }

        return $results;
    }

    private static function get_quiz_scores($user_id, $quizzes)
    {
        $scores = [];
        foreach ($quizzes as $quiz) {
            $quiz_id = (int)$quiz['id'];     // key name per API docs
            // 2. Grab ALL attempt activity-IDs for this user+quiz.
            $attempts = learndash_get_user_quiz_attempts($user_id, $quiz_id);
            if (empty($attempts)) {
                continue; // user never attempted this quiz
            }
            // 3. Pick the last ID → latest attempt.
            $latest_attempt = end($attempts);
            $meta = learndash_get_activity_meta_fields($latest_attempt->activity_id);
            if (empty($meta['percentage'])) {
                continue;
            }
            $scores[] = (int)$meta['percentage'];
        }
        return $scores;
    }

    public static function get_study_hours_for_user()
    {

    }
}
