<?php

namespace AceTheTest_Dashboard\includes;
class Shortcodes
{
    public function __construct()
    {
        $shortcodes = array('acethetest-dashboard' => [$this, 'shortcode']);
        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }
    }


    public function shortcode($atts)
    {
        ob_start();
        ?>
        <div id="acethetest-dashboard"></div>
        <?php
        return ob_get_clean();
    }
}

return new Shortcodes();