<?php

namespace AceTheTest_Dashboard\includes;

class CronSetup
{
    private $pre = '_acethetest_';

    public function __construct()
    {
        add_action('wp', array($this, 'schedule_exam_reminder'));
        add_action('exam_reminder', array($this, 'exam_reminder_callback'));
    }

    public function schedule_exam_reminder()
    {
        if (!wp_next_scheduled('exam_reminder')) {
            wp_schedule_event(time(), 'daily', 'exam_reminder');
        }
    }

    public function exam_reminder_callback()
    {
        $meta_key = $this->pre . 'exam_date';

        $now = current_time('timestamp');
        $target_time = strtotime('+2 days', $now);
        $target_date = date('Y-m-d', $target_time);

        $user_query = new \WP_User_Query([
            'meta_key' => $meta_key,
            'meta_compare' => 'EXISTS'
        ]);

        $users = $user_query->get_results();

        foreach ($users as $user) {
            $user_id = $user->ID;
            $user_email = $user->user_email;
            $full_name = $user->display_name;
            $exam_date_raw = get_user_meta($user_id, $meta_key, true);

            $exam_date = date('Y-m-d', $exam_date_raw);
            if ($exam_date === $target_date) {
                $this->send_sms($user_id);

                $message = $this->build_exam_reminder_email($full_name, $exam_date);

                $subject = '📘 Your Insurance Exam Reminder';
                $headers = ['Content-Type: text/html; charset=UTF-8'];

                wp_mail($user_email, $subject, $message, $headers);
            }
        }
    }

    function send_sms($user_id) {
        $user = get_userdata($user_id);

        $phone = get_user_meta( $user_id, 'mobile_number', true);
        if(empty($phone)) return;

        $webhook_url = 'https://hooks.zapier.com/hooks/catch/23812642/u2ia7x1/';

        $meta_key = $this->pre . 'exam_date';
        $exam_date_raw = get_user_meta($user_id, $meta_key, true);

        $exam_date = date('Y-m-d', $exam_date_raw);
        $body = [
            'id'    => $user_id,
            'name'  => $user->display_name,
            'phone'  => $phone,
            'date'  => $exam_date,
        ];

        $args = [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($body),
            'method'  => 'POST',
            'timeout' => 15,
        ];

        $response = wp_remote_post($webhook_url, $args);
        if (is_wp_error($response)) {
            error_log('Webhook error: ' . $response->get_error_message());
        }
    }


    private function build_exam_reminder_email($full_name, $exam_date): string
    {
        return '
<div style="padding: 20px; background-color: #f0f0f0; font-family: -apple-system, system-ui, BlinkMacSystemFont,;">
  <div style="background-color: #fff; padding: 30px; margin: 0px auto; max-width: 450px; box-shadow: 0px 2px 6px -1px rgba(0,0,0,0.2); border-radius: 6px;">
    <div style="margin: 0px auto 30px auto; border-bottom: 1px solid #eee; padding-bottom: 20px;">
      <table style="width: 100%;">
        <tbody>
          <tr>
            <td><img src="https://123acethetest.com/wp-content/uploads/2024/12/logo.png" style="height: 50px; width: auto"></td>
            <td style="text-align: right;">
              <span style="color: #7b7b7b;">Questions?</span><br />
              <strong>(817) 737-6161</strong>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div style="font-size: 16px; line-height: 1.5;">
      <div style="font-size: 16px; margin-bottom: 20px; line-height: 1.6;">
        Hello ' . esc_html($full_name) . ',
      </div>
      <div style="font-size: 16px; margin-bottom: 20px; line-height: 1.6;">
        Just a reminder—your scheduled Insurance Exam class is coming up! Your exam is scheduled for ' . esc_html(date('F j, Y', strtotime($exam_date))) . '
      </div>
      <div style="font-size: 16px; margin-bottom: 20px; line-height: 1.6;">
        Make sure to reach a few minutes early and bring any materials you may need.
      </div>
      <div style="margin-bottom: 20px;">We look forward to have a great success ahead!</div>
      <div>
        <div>Best regards,</div>
        <div>123AceTheTest</div>
        <div>David Offutt / Headmaster</div>
      </div>
    </div>
  </div>
  <div style="max-width: 450px; margin: 10px auto; text-align: center;">
    4528 W Vickery Blvd #100, Fort Worth, TX 76107
  </div>
</div>';
    }

}

return new CronSetup();
