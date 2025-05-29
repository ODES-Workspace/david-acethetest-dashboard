export interface IBooking {
    nice_names: {
        service_id: string; // e.g., "Class"
        agent_id: string;   // e.g., "Instructor"
    };
    data_vars: any[]; // Can refine if structure is known
    first_level_data_vars: any[];
    form_id: boolean;
    last_query: string;
    meta_class: string; // e.g., "OsBookingMetaModel"
    meta: boolean;
    table_name: string; // e.g., "wp_latepoint_bookings"
    join_attributes: any[];
    id: string;
    booking_code: string;
    service_id: string;
    customer_id: string;
    agent_id: string;
    location_id: string;
    recurrence_id: string | null;
    buffer_before: string;
    buffer_after: string;
    status: 'pending' | 'approved' | 'cancelled' | 'no_show' | string; // extend as needed
    start_date: string; // ISO format date string
    end_date: string;
    start_time: string; // minutes since midnight (e.g., "480")
    end_time: string;
    start_datetime_utc: string; // full datetime string
    end_datetime_utc: string;
    duration: string;
    total_attendees: string;
    total_attendees_sum: number;
    total_customers: number;
    cart_item_id: string | null;
    order_item_id: string;
    server_timezone: string | null;
    customer_timezone: string | null;
    keys_to_manage: any[];
    generate_recurrent_sequence: any[];
    updated_at: string;
    created_at: string;
    custom_fields: any[];
}
