<?php

namespace AceTheTest_Dashboard\includes;

function ajax_return($success, $message, $data = null)
{
    wp_send_json(['success' => $success, 'message' => $message, 'data' => $data]);
    die();
}



function get_ld_course_time_spent($user_id)
{
    if (!function_exists('learndash_user_get_enrolled_courses')) {
        return ['error' => 'LearnDash is not active'];
    }

    $user_data = ['courses' => []];

    // Get enrolled courses
    $course_ids = learndash_user_get_enrolled_courses($user_id);

    foreach ($course_ids as $course_id) {
        $course_title = get_the_title($course_id);
        $course_data = [
            'title' => $course_title,
            'topics' => [],
            'course_quizzes' => []
        ];

        // Get all lessons in the course
        $lessons = learndash_get_lesson_list($course_id, ['num' => 0]);

        foreach ($lessons as $lesson) {
            $lesson_id = $lesson->ID;

            // Get all topics under the lesson
            $topics = learndash_get_topic_list($lesson_id, $course_id);

            foreach ($topics as $topic) {
                $topic_id = $topic->ID;
                $topic_title = get_the_title($topic_id);

                // Get quizzes linked to the topic
                $topic_quizzes = get_posts([
                    'post_type' => 'sfwd-quiz',
                    'posts_per_page' => -1,
                    'meta_query' => [
                        [
                            'key' => 'topic',
                            'value' => $topic_id,
                        ]
                    ]
                ]);

                $topic_data = [
                    'title' => $topic_title,
                    'quizzes' => []
                ];

                foreach ($topic_quizzes as $quiz) {

                    if($quiz['status'] == "completed") {
                        $topic_data['quizzes'][] = [
                            'id' => $quiz['id'],
                            'title' => get_the_title($quiz['id']),
                        ];
                    }
                }

                $course_data['topics'][$topic_id] = $topic_data;
            }
        }

        // Get course-level quizzes
        $course_quizzes = learndash_get_course_quiz_list($course_id, $user_id);
        foreach ($course_quizzes as $quiz) {
            if($quiz['status'] == "completed") {
                $course_data['course_quizzes'][] = [
                    'id' => $quiz['id'],
                    'title' => get_the_title($quiz['id']),
                ];
            }
        }

        $user_data['courses'][$course_id] = $course_data;
    }

    return $user_data;
}

function get_late_point_class_time_spent($user_id){

    $enrolled_courses = learndash_user_get_enrolled_courses($user_id);
    $total_duration = 0;

    foreach ($enrolled_courses as $course_id) {
        $course_settings = get_post_meta($course_id, '_sfwd-courses', true);
        // Duration is typically in minutes and stored as 'course_duration'
        $duration = isset($course_settings['sfwd-courses_course_duration'])
            ? (int)$course_settings['sfwd-courses_course_duration']
            : 0;

        $total_duration += $duration;
    }

    // Optionally convert to hours:minutes format
    $hours = floor($total_duration / 60);
    $minutes = $total_duration % 60;

    return "Total Duration: {$hours} hours {$minutes} minutes";
};