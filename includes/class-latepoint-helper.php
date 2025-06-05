<?php

namespace AceTheTest_Dashboard\includes;
class Latepoint_Helper
{
    public static function get_study_hours_for_user($user_id)
    {
        $customer = new \OsCustomerModel();
        $customer = $customer->where( [ 'wordpress_user_id' => $user_id ] )->set_limit( 1 )->get_results_as_models();
        if (!$customer->id) return [];

        // Get bookings for this customer
        $bookings = new \OsBookingModel();
        return $bookings->should_not_be_cancelled()->where(['customer_id' => $customer->id ,'status' => LATEPOINT_BOOKING_STATUS_COMPLETED])->get_results_as_models();
    }

    public static function get_lp_upcoming_zoom_classes($user_id, $service_ids){
        $results = [];
        foreach ($service_ids as $service_id) {
            $filter = new \LatePoint\Misc\Filter([
                'service_id'  => $service_id,
                'date_from' =>  date('Y-m-d'),
            ] );
            $bookings = \OsBookingHelper::get_bookings($filter,true);
            foreach ($bookings as $booking) {
            }
            $results[] = $bookings;
        }

        return $results;
    }

}
