<?php

namespace AceTheTest_Dashboard\includes;

use LatePoint\Misc\BookingRequest;
use OsBookingModel;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Controller Class.
 */
class Controller
{
    private $pre = '_acethetest_';

    public function __construct()
    {
        add_action('wp_ajax_attd_set_exam_date', array($this, 'ajax_set_exam_date'));
        add_action('wp_ajax_attd_get_exam_date', array($this, 'ajax_get_exam_date'));

        add_action('wp_ajax_attd_get_study_hours', array($this, 'ajax_get_study_hours'));
        add_action('wp_ajax_attd_set_study_hours', array($this, 'set_study_hours'));


        add_action('wp_ajax_attd_get_test_scores', array($this, 'get_test_scores'));
    }

    public function ajax_get_study_hours()
    {
        $user_id = get_current_user_id();
        if (!$user_id){
            ajax_return(false, 'User not logged in.');
        }else {
            $manual_hours = get_user_meta(get_current_user_id(), $this->pre . 'study_hours', true);
            $bookings = Latepoint_Helper::get_study_hours_for_user($user_id);
            if (!is_array($manual_hours)) $manual_hours = [];
            ajax_return(true, 'Study Hours', ['manual' => $manual_hours,'study_hours' => $bookings]);
        }
    }

    public function get_test_scores()
    {
//        $user_id = get_current_user_id();
//        $test_score = 0;
//        $course_ids = learndash_user_get_enrolled_courses($user_id);
//        $attemps = learndash_get_user_quiz_attempt($user_id);
//        foreach ($course_ids as $course_id) {
//            $course = get_post($course_id);
//            // Get course-level quizzes
//            $course_quizzes = learndash_get_course_quiz_list($course, $user_id);
//            foreach ($course_quizzes as $quiz) {
//                if ($quiz['status'] == "completed") {
//                    $course_data['course_quizzes'][] = [
//                        'id' => $quiz['id'],
//                        'title' => get_the_title($quiz['id']),
//                    ];
//                }
//            }
//
//            // Get all lessons in the course
//            $lessons = learndash_get_lesson_list($course_id);
//            foreach ($lessons as $lesson) {
//                $lesson_quizzes = learndash_get_lesson_quiz_list($lesson->ID, $user_id);
//                foreach ($lesson_quizzes as $quiz){
//                    $a = $quiz;
//                }
//            }
//        }

        ajax_return(true, 'Test Scores', 11);
    }


    function set_study_hours()
    {
        update_user_meta(get_current_user_id(), $this->pre . 'study_hours', $_POST['manual_hours']);
        ajax_return(true, 'Study Hours', $_POST['manual_hours']);
    }

    public
    function ajax_set_exam_date()
    {
        update_user_meta(get_current_user_id(), $this->pre . 'exam_date', $_POST['date']);
        ajax_return(true, 'Exam Date', $_POST['date']);
    }

    public
    function ajax_get_exam_date()
    {
        ajax_return(true, 'Exam Date', get_user_meta(get_current_user_id(), $this->pre . 'exam_date', true));
    }
}


return new Controller();
