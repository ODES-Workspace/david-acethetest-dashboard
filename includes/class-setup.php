<?php

namespace AceTheTest_Dashboard\includes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Setup Class.
 */
class Setup
{

    public function __construct()
    {
        $this->hooks();
    }

    /**
     * Hook in to actions & filters
     *
     * @since 1.0.0
     */
    public function hooks()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_vite'));
    }


    public function enqueue_vite()
    {
        // js options and i18n
        $options = array(
            'ajax_url' => admin_url('admin-ajax.php'),
        );
        // Dev mode
        if (ACETHETEST_DASHBOARD_ENV === 'development') {
            echo '<script type="module" src="http://localhost:5173/@vite/client"></script>';
            echo '<script type="module" src="http://localhost:5173/src/main.ts"></script>';
            echo '<script id="acethetest-dashboard-script-js-extra">var acethetest_dashboard_script = ' . json_encode($options) . ';</script>';
            return;
        }

        $dir = ACETHETEST_DASHBOARD_DIR;
        $uri = ACETHETEST_DASHBOARD_URL;
        $version = ACETHETEST_DASHBOARD_VERSION;

        // Prod build
        $manifestPath = $dir . 'dist/.vite/manifest.json';
        if (!file_exists($manifestPath)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $entry = $manifest['src/main.ts'];

        wp_enqueue_style('acethetest-dashboard-style', $uri . 'dist/' . $entry['css'][0], [], $version);

        wp_enqueue_script('acethetest-dashboard-script', $uri . 'dist/' . $entry['file'], [], $version, true);


        wp_localize_script('acethetest-dashboard-script', 'acethetest_dashboard_script', $options);
    }

}


return new Setup();
