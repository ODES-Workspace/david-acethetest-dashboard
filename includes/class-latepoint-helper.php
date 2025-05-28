<?php

namespace AceTheTest_Dashboard\includes;
class Latepoint_Helper
{
    public static function get_study_hours_for_user($user_id)
    {

        $customer = new \OsCustomerModel();
        $customer->load_by_id($user_id);

        if (!$customer->id) return null;

        // Get bookings for this customer
        $bookings = new \OsBookingModel();
        return $bookings->should_not_be_cancelled()->where(['customer_id' => $customer->id])->get_results_as_models();
    }

}
