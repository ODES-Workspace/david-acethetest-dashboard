<?php

namespace AceTheTest_Dashboard\includes;
class Latepoint_Helper
{
    public static function get_customer_for_wp_user($user_id){
        $customer = new \OsCustomerModel();
        $customer = $customer->where( [ 'wordpress_user_id' => $user_id ] )->set_limit( 1 )->get_results_as_models();
        if (!$customer->id) return false;
        return $customer;
    }
    public static function get_study_hours_for_user($user_id)
    {
        $customer = self::get_customer_for_wp_user($user_id);
        if(!$customer) return [];

        // Get bookings for this customer
        $bookings = new \OsBookingModel();
        return $bookings->should_not_be_cancelled()->where(['customer_id' => $customer->id ,'status' => LATEPOINT_BOOKING_STATUS_COMPLETED])->get_results_as_models();
    }

    public static function get_lp_upcoming_zoom_classes($user_id, $service_ids){
        $customer = self::get_customer_for_wp_user($user_id);
        if(!$customer) return [];
        $bookings =  (new \OsBookingModel())->where(['customer_id'=> $customer->id,'start_date >=' => date('Y-m-d')])->where_in('service_id', $service_ids)->get_results_as_models();
        foreach ($bookings as $booking){
            $booking->service = $booking->service;
            $booking->service->attachment = $booking->service->get_meta_by_key('attachment');
            $booking->zoom_url = $booking->get_meta_by_key('zoom_meeting_join_url');
        }
        return $bookings;
    }

}