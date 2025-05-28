<?php

namespace AceTheTest_Dashboard\includes;
class LD_Helper
{

    public static function get_latest_test_scores_for_user($user_id, array $course_ids)
    {

        $results = [];

        foreach ($course_ids as $course_id) {
            // Init scores in case no quizzes found
            $course_quiz_avg = 0;
            $lesson_quiz_avg = 0;
            $course_scores = [];
            $lesson_scores = [];

            // 1. Find every quiz that belongs to this course.
            $quizzes = learndash_get_course_quiz_list($course_id); // Legacy but still supported
            if (count($quizzes)) {
                $course_scores = self::get_quiz_scores($user_id, $quizzes);
            }

            // 2. Find every quiz that belongs to this course lessons
            $lessons = learndash_get_lesson_list($course_id);
            foreach ($lessons as $lesson) {
                $quizzes = learndash_get_lesson_quiz_list($lesson->ID);
                $scores = self::get_quiz_scores($user_id, $quizzes);
                $lesson_scores = array_merge($lesson_scores, $scores);
            }

            // Combine and calculate final average
            $all_scores = array_merge($course_scores, $lesson_scores);
            $final_avg = count($all_scores) ? array_sum($all_scores) / count($all_scores) : 0;

            // Append result
            $results[] = [
                'course_id'     => $course_id,
                'course_title'  => get_the_title($course_id),
                'course_url'    => get_permalink($course_id),
                'average_score' => round($final_avg, 2),
            ];
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
            foreach ($attempts as $attempt) {
                $meta = learndash_get_activity_meta_fields($attempt->activity_id);
                if (!isset($meta['percentage']) || $meta['percentage'] === '') {
                    continue;
                }
                $scores[] = (int)$meta['percentage'];
            }
        }
        return $scores;
    }

    public static function get_study_hours_for_user()
    {

    }
}
