<?php

namespace AceTheTest_Dashboard\includes;

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
        add_action('wp_ajax_attd_get_test_activities', array($this, 'get_test_activities'));

        add_action('wp_ajax_attd_get_on_demand_courses_html', array($this, 'get_on_demand_courses_html'));
        add_action('wp_ajax_attd_get_upcoming_zoom_classes', array($this, 'get_upcoming_zoom_classes'));
    }

    function get_upcoming_zoom_classes()
    {
        $results = Latepoint_Helper::get_lp_upcoming_zoom_classes(get_current_user_id(), ACETHETEST_ZOOM_COURSES_ID);
        ajax_return(true, 'zoom classes', $results);
    }

    function get_on_demand_courses_html()
    {
        $courses = ld_get_mycourses(get_current_user_id());
        if (count($courses) == 0) {
            ajax_return(true, 'No on demand courses found', 'No on demand courses found');
        } else {
            ob_start();
            foreach ($courses as $id) {
                if ((int)$id === 889) {
                    continue; // Skip course with ID 889
                }
                $post = get_post($id);
                $permalink = get_permalink($id);

                echo '<div>';
                echo '<a href="' . esc_url($permalink) . '" target="_blank">' . esc_html($post->post_title) . '</a>';
                echo do_shortcode('[learndash_course_progress user_id="' . get_current_user_id() . '" course_id="' . $id . '"]');
                echo '</div>';
            }
            $html = ob_get_clean();
            ajax_return(true, 'No on demand courses found', $html);
            // Return the learndash_course_progress shortcode with dynamic user_id
        }
    }

    public function ajax_get_study_hours()
    {
        $user_id = get_current_user_id();
        if (!$user_id) {
            ajax_return(false, 'User not logged in.');
        } else {
            $manual_hours = get_user_meta(get_current_user_id(), $this->pre . 'study_hours', true);
            $bookings = Latepoint_Helper::get_study_hours_for_user($user_id);
            if (!is_array($manual_hours)) $manual_hours = [];
            $on_demand_hours = LD_Helper::get_study_hours_for_user($user_id);
            ajax_return(true, 'Study Hours', ['manual' => $manual_hours, 'study_hours' => $bookings, 'on_demand' => $on_demand_hours]);
        }
    }

    public function get_test_scores()
    {
        $results = LD_Helper::get_latest_test_scores_for_user(get_current_user_id(), [889]);
        ajax_return(true, 'Quiz Stats Fetched', $results);
    }

    public function get_test_activities()
    {
        $results = LD_Helper::get_quiz_activities_for_user(get_current_user_id(), ACETHETEST_LEARNDASH_ONDEMAND_COURSES_ID);
        ajax_return(true, 'Activities Fetched', $results);
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
