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
        $manual_hours = get_user_meta(get_current_user_id(), $this->pre . 'study_hours', true);
        if (!is_array($manual_hours)) $manual_hours = [];
        ajax_return(true, 'Study Hours', ['manual' => $manual_hours]);
    }

    public function get_test_scores()
    {

//        echo LD_Helper::get_latest_test_scores_for_user(get_current_user_id(), [889]);

        ajax_return(true, 'Test Scores1', 11);
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
