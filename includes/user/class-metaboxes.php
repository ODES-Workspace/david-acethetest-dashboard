<?php


namespace AceTheTest_Dashboard\includes\user;

class Metaboxes
{

    /**
     * Post type.
     * @var string
     */
    public $post_type = 'user';


    /**
     * Metabox prefix.
     *
     * @since 1.0.0
     */
    private $pre = '_acethetest_';

    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Hook in to actions & filters.
     *
     * @since 1.0.0
     */
    public function hooks()
    {
        add_action('cmb2_admin_init', [$this, 'metabox_details']);
    }


    public function metabox_details()
    {
        $id = $this->pre . 'details_box';

        $box = new_cmb2_box(
            [
                'id' => $id,
                'title' => __('Ace The Test Meta', 'acethetest-dashboard'),
                'object_types' => [$this->post_type],
                'priority' => 'high',
                'show_in_rest' => true,
            ]
        );

        $fields = [];

        $fields[] = [
            'name' => 'Exam Date',
            'desc' => '',
            'id' => $this->pre . 'exam_date',
            'type' => 'text_date_timestamp',
            'default' => '',
            'attributes' => []
        ];

        $fields[] = [
            'name' => 'Study Hours',
            'desc' => '',
            'id' => $this->pre . 'study_hours',
            'type' => 'text',
            'default' => '',
            'attributes' => [
                'type' => 'number'
            ],
            'repeatable' => true
        ];


        // filter the fields
        $fields = apply_filters("acethetest_dashboard_metabox_{$id}", $fields, $id, $this->post_type);

        // sort numerically
        ksort($fields);

        // loop through ordered fields and add them to the metabox
        if ($fields) {
            foreach ($fields as $key => $value) {
                $fields[$key] = $box->add_field($value);
            }
        }
    }
}

return new Metaboxes();