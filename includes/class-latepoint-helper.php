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

}
