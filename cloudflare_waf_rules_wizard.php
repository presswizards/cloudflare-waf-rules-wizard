<?php
/*
Plugin Name:    Cloudflare WAF Rules Wizard
Description:    A simple plugin to create Cloudflare WAF custom rules based on account ID. Based on Troy Glancy's superb CF WAF v3 rules.
Version:        2.3
Plugin URI:     https://github.com/presswizards/cloudflare-waf-rules-wizard/
Text Domain:    cloudflare-waf-rules-wizard
Domain Path:    /languages
Author:         Rob Marlbrough - PressWizards.com
Author URI:     https://presswizards.com/
License:        GPL v3 or later
License URI:    https://www.gnu.org/licenses/gpl-3.0.html

Requires at least: 5.2
Requires PHP:      7.4
*/

if (defined('WP_INSTALLING') && WP_INSTALLING) return;
if (!defined('ABSPATH')) exit; // Prevent direct access

add_action('admin_init', function () {
    // Ensure user has permission
    if (!is_admin() || !current_user_can('update_plugins')) {
        return;
    }

    $updater_path = plugin_dir_path(__FILE__) . 'updater.php';

    // Check if updater file exists and include itcloudflare-waf-rules-wizardcloudflare-waf-rules-wizard
    if (file_exists($updater_path)) {
        include_once $updater_path;
    } else {
        return;
    }

    // Ensure class exists before instantiating
    if (class_exists('WP_GitHub_Updater')) {
        $config = [
            'slug' => plugin_basename(__FILE__),
            'proper_folder_name' => 'cloudflare-waf-rules-wizard',
            'api_url' => 'https://api.github.com/repos/presswizards/cloudflare-waf-rules-wizard',
            'raw_url' => 'https://raw.github.com/presswizards/cloudflare-waf-rules-wizard/main',
            'github_url' => 'https://github.com/presswizards/cloudflare-waf-rules-wizard',
            'zip_url' => 'https://github.com/presswizards/cloudflare-waf-rules-wizard/zipball/main',
            'sslverify' => true,
            'requires' => '5.2',
            'tested' => '6.7.2',
            'readme' => 'README.md',
            'access_token' => '',
        ];
        new WP_GitHub_Updater($config);
    }
});

add_filter( 'plugins_api', 'cloudflare_waf_rules_wizard_plugin_info', 20, 3);
function cloudflare_waf_rules_wizard_plugin_info( $res, $action, $args ){


        // do nothing if this is not about getting plugin information
        if( 'plugin_information' !== $action ) {
                return $res;
        }

        // do nothing if it is not our plugin
        if( plugin_basename( __DIR__ ) !== $args->slug ) {
                return $res;
        }
global $cfwafversion;
global $cfwaflastmod;

$remote = '{
        "name" : "Cloudflare WAF Rules Wizard",
        "slug" : "cloudflare-waf-rules-wizard",
        "author" : "<a href=https://presswizards.com>Press Wizards</a>",
        "author_profile" : "https://profiles.wordpress.org/presswizards",
        "donate_link" : "https://buymeacoffee.com/robwpdev",
        "version" : "2.3",
        "download_url" : "https://github.com/presswizards/cloudflare-waf-rules-wizard/zipball/main",
        "requires" : "5.2",
        "tested" : "6.7.2",
        "requires_php" : "5.6",
        "added" : "2024-06-04 02:10:00",
        "last_updated" : "2025-03-25 10:07:00",
        "homepage" : "https://github.com//presswizards/cloudflare-waf-rules-wizard",
        "sections" : {
                "description" : "A simple plugin to create Cloudflare WAF custom rules using your Cloudflare API key. This plugin is based on the amazing work by Troy Glancy and his superb Cloudflare WAF Rules.",
                "installation" : "Click the activate button and go to the options page.",
                "changelog" : "<h4>For the latest changes, visit the <a target=_blank href=https://github.com/presswizards/cloudflare-waf-rules-wizard>GitHub page</a></h4><h4>v2.3</h4><ul><li>Improvement: Added translation support via .po files</li><li>Improvement: API and Account ID fields use password display</li><li>Improvement: added github update mechanism</li></ul><h4>v2.1</h4><ul><li>Added language translation support, with a bunch of languages included.</li></ul><h4>v2.0</h4><ul><li>Added easy user agent allowlist via checkboxes.</li></ul>"
        },
        "banners" : {
                "low" : "https://presswizards.com/wp-content/uploads/2025/04/cfwafwizard-772.webp",
                "high" : "https://presswizards.com/wp-content/uploads/2025/04/cfwafwizard.webp"
        }
}';

$remote = json_decode($remote);

$res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->version = $cfwafversion;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->requires_php = $remote->requires_php;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = date('Y-m-d H:i:s', strtotime('-1 day'));
        $res->sections = array(
                'description' => $remote->sections->description,
                'installation' => $remote->sections->installation,
                'changelog' => $remote->sections->changelog
                // you can add your custom sections (tabs) here
        );
        // in case you want the screenshots tab, use the following HTML format for its content:
        // <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
        if( ! empty( $remote->sections->screenshots ) ) {
                $res->sections[ 'screenshots' ] = $remote->sections->screenshots;
        }

        $res->banners = array(
                'low' => $remote->banners->low,
                'high' => $remote->banners->high
        );

        return $res;

}

// Load plugin text domain for translations
add_action('plugins_loaded', 'pw_load_textdomain');
function pw_load_textdomain() {
    load_plugin_textdomain('cloudflare-waf-rules-wizard', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Add a menu item for the plugin settings page
add_action('admin_menu','pw_cloudflare_ruleset_manager_menu');
function pw_cloudflare_ruleset_manager_menu() {
    if (current_user_can('manage_options')) {
        add_options_page(
            __('Cloudflare WAF Rules Wizard', 'cloudflare-waf-rules-wizard'),
            __('CF Rules Wizard', 'cloudflare-waf-rules-wizard'),
            'manage_options',
            'pw_cloudflare-ruleset-manager',
            'pw_cloudflare_ruleset_manager_options_page'
        );
    }
}

// Display the plugin settings page
function pw_cloudflare_ruleset_manager_options_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'cloudflare-waf-rules-wizard'));
    }
    ?>
    <div class="wrap">
        <h2><?php _e('Cloudflare WAF Rules Wizard', 'cloudflare-waf-rules-wizard'); ?></h2>
        <p><?php _e('Created by Rob Marlbrough at', 'cloudflare-waf-rules-wizard'); ?> <a target="_blank" href="https://presswizards.com/"><?php _e('Press Wizards - WordPress Design, Hosting, and Maintenance', 'cloudflare-waf-rules-wizard'); ?></a></p>

        <p>
        <a href="https://www.buymeacoffee.com/robwpdev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a><br>
        <?php _e('If this plugin saves you time, helps your clients, or helps you do better work, I\'d appreciate it.'); ?>
        </p>

        <?php if(get_option('pw_cloudflare_account_id') && get_option('pw_cloudflare_api_key') && get_option('pw_cloudflare_api_email')) { ?>
                <form method="post">
                        <?php
                        $zones = pw_get_cloudflare_zones(
                                get_option('pw_cloudflare_account_id'),
                                get_option('pw_cloudflare_api_key'),
                                get_option('pw_cloudflare_api_email')
                        );
                        ?>
                        <h3><?php _e('Now, select Domains to Reset WAF Custom Rules on:', 'cloudflare-waf-rules-wizard'); ?></h3>
                        <label>
                            <input type="checkbox" id="select-all-domains">
                            <strong><?php _e('Select All', 'cloudflare-waf-rules-wizard'); ?></strong>
                        </label><br>
                        <?php foreach ($zones as $zone): ?>
                                <label>
                                        <input type="checkbox" name="pw_zone_ids[]" value="<?php echo esc_attr($zone['id']); ?>" class="domain-checkbox">
                                        <?php echo esc_html($zone['name']); ?>
                                </label><br>
                        <?php endforeach; ?>
                        <br/>
                        <?php // Add nonce field for security
                        wp_nonce_field('pw_create_ruleset_action', 'pw_create_ruleset_nonce'); ?>
                        <input type="submit" class="button button-primary" name="pw_create_ruleset" value="<?php _e('Create/Overwrite All WAF Rules', 'cloudflare-waf-rules-wizard'); ?>">
                </form>
                <script>
                    document.getElementById('select-all-domains').addEventListener('change', function(e) {
                        const checkboxes = document.querySelectorAll('.domain-checkbox');
                        checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
                    });
                </script>
        <?php } ?>
                <p>&nbsp;</p>
        <form method="post" action="options.php">
                <?php
                settings_fields('pw_cloudflare_ruleset_manager_options');
                do_settings_sections('pw_cloudflare-ruleset-manager');
                // Add nonce field for security
                wp_nonce_field('pw_update_settings_action', 'pw_update_settings_nonce');
                submit_button(__('Save Settings', 'cloudflare-waf-rules-wizard'));
                ?>
        </form>

        <form method="post" action="">
            <input type="hidden" name="pw_delete_settings" value="1" />
            <?php wp_nonce_field('pw_delete_settings_action', 'pw_delete_settings_nonce'); ?>
            <input type="submit" class="button button-secondary" value="<?php _e('Delete Settings', 'cloudflare-waf-rules-wizard'); ?>" />
        </form>

        <p>
        <a href="https://www.buymeacoffee.com/robwpdev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a><br>
        <?php _e('If this plugin saves you time, helps your clients, or helps you do better work, I\'d appreciate it.'); ?>
        </p>

        <p>&nbsp;</p>
        <p><?php _e('Based on', 'cloudflare-waf-rules-wizard'); ?> <a target="_blank" href="https://webagencyhero.com/cloudflare-waf-rules-v3/"><?php _e('Troy Glancy\'s superb Cloudflare WAF Rules v3', 'cloudflare-waf-rules-wizard'); ?></a></p>

    </div>
    <?php
}

// Register and define the plugin settings
add_action('admin_init', 'pw_cloudflare_ruleset_manager_settings');
function pw_cloudflare_ruleset_manager_settings() {
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_api_key', 'sanitize_text_field');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_api_email', 'sanitize_email');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_account_id', 'sanitize_text_field');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_user_agents', 'pw_sanitize_user_agents');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_custom_user_agents', 'pw_sanitize_custom_user_agents');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_custom_allowed_ips', 'pw_sanitize_custom_allowed_ips');

    add_settings_section('pw_cloudflare_ruleset_manager_main', 'Cloudflare API Settings', 'pw_cloudflare_ruleset_manager_section_text', 'pw_cloudflare-ruleset-manager');

    add_settings_field('pw_cloudflare_api_key', 'API Key', 'pw_cloudflare_ruleset_manager_field_api_key', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_api_email', 'API Email', 'pw_cloudflare_ruleset_manager_field_api_email', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_account_id', 'Account ID', 'pw_cloudflare_ruleset_manager_field_account_id', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_user_agents', 'Allowlist User Agents', 'pw_cloudflare_ruleset_manager_field_user_agents', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_custom_allowed_ips', 'Custom Allowed IP Addresses', 'pw_cloudflare_ruleset_manager_field_custom_allowed_ips', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
}

add_action('admin_init', 'pw_handle_delete_settings');
function pw_handle_delete_settings() {
    if (isset($_POST['pw_delete_settings']) && check_admin_referer('pw_delete_settings_action', 'pw_delete_settings_nonce')) {
        delete_option('pw_cloudflare_api_key');
        delete_option('pw_cloudflare_api_email');
        delete_option('pw_cloudflare_account_id');
                
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Cloudflare WAF Rules Wizard settings have been deleted.', 'cloudflare-waf-rules-wizard') . '</p></div>';
        });
    }
}

function pw_cloudflare_ruleset_manager_section_text() {
    echo '<p>' . __('First, enter your Cloudflare API Key and Email address, and the Account ID. Also select the known good user agents you\'d like to add to the Good Bot Skip rule.', 'cloudflare-waf-rules-wizard') . '</p>';
    echo '<p>' . __('When you Save those settings, it will get all the domains under the account ID, and you can select the ones you want CF WAF custom rules created in.', 'cloudflare-waf-rules-wizard') . '</p>';
    echo '<p><a href="https://webagencyhero.com/cloudflare-waf-rules-v3/" target="_blank">' . __('Dig into Troy\'s WAF Rules v3 - each rule explained in detail', 'cloudflare-waf-rules-wizard') . '</a>. ' . __('You can further customize the rules after they are created directly in Cloudflare under Security > WAF > Custom rules.', 'cloudflare-waf-rules-wizard') . '</p>';
    echo '<p><strong>' . __('NOTE: It resets all WAF custom rules! If any other WAF Custom Rules exist for that domain, it will erase them, and create 5 new rules. IMPORTANT: Use at your own risk!', 'cloudflare-waf-rules-wizard') . '</strong></p>';
    echo '<p>' . __('Security Tip: Click the Delete Settings button after you are done using this plugin to remove your credentials. They are not encrypted when stored. Maybe future versions will encrypt, delete the options on deactivation, etc. Right now it is a quick and simple plugin for you to use and then remove.', 'cloudflare-waf-rules-wizard') . '</p>';
}

function pw_cloudflare_ruleset_manager_field_api_key() {
    $apiKey = get_option('pw_cloudflare_api_key');
    echo "<input type='password' name='pw_cloudflare_api_key' value='$apiKey' />";
}

function pw_cloudflare_ruleset_manager_field_api_email() {
    $apiEmail = get_option('pw_cloudflare_api_email');
    echo "<input type='email' name='pw_cloudflare_api_email' value='$apiEmail' />";
}

function pw_cloudflare_ruleset_manager_field_account_id() {
    $accountId = get_option('pw_cloudflare_account_id');
    echo "<input type='password' name='pw_cloudflare_account_id' value='$accountId' />";
}

function pw_cloudflare_ruleset_manager_field_user_agents() {
    $userAgents = get_option('pw_cloudflare_user_agents', []);
    $customUserAgents = get_option('pw_cloudflare_custom_user_agents', '');

    // Built-in options with headers and user agents
    $availableAgents = [
        ['name' => __('Security & Malware Scanners', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Wordfence/Central', 'value' => 'Wordfence'],
        ['name' => 'Sucuri', 'value' => 'SucuriScan'],
        ['name' => 'SiteLock', 'value' => 'SiteLockSpider'],
        ['name' => 'VirusTotal', 'value' => 'virustotal'],

        ['name' => __('Performance & Image Optimization Services', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Easy IO EWWW CDN', 'value' => 'ExactDN'],
        ['name' => 'EWWW Image Optimizer', 'value' => 'ewww'],
        ['name' => 'ShortPixel', 'value' => 'ShortPixel'],
        ['name' => 'Imagify', 'value' => 'Imagify'],
        ['name' => 'TinyPNG', 'value' => 'TinyPNG'],
        ['name' => 'Cloudflare Image Resizing', 'value' => 'Cloudflare-Image-Resizing'],

        ['name' => __('SEO Auditing & Crawlers', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Screaming Frog', 'value' => 'Screaming Frog SEO Spider'],
        ['name' => 'Ahrefs', 'value' => 'AhrefsBot'],
        ['name' => 'Semrush', 'value' => 'SemrushBot'],
        ['name' => 'SEO PowerSuite', 'value' => 'LinkAssistant'],
        ['name' => 'Majestic', 'value' => 'MJ12bot'],
        ['name' => 'Moz', 'value' => 'rogerbot'],
        ['name' => 'Serpstat', 'value' => 'SerpstatBot'],

        ['name' => __('WordPress Management & Monitoring', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'ManageWP', 'value' => 'ManageWP'],
        ['name' => 'MainWP', 'value' => 'MainWP'],
        ['name' => 'WP Umbrella', 'value' => 'WPUmbrella'],
        ['name' => 'WP Remote', 'value' => 'WP Remote'],
        ['name' => 'iThemes Security', 'value' => 'iThemesSecurity'],
        ['name' => 'InfiniteWP', 'value' => 'InfiniteWP'],
        ['name' => 'Jetpack', 'value' => 'Jetpack'],

        ['name' => __('Website Monitoring & Uptime Services', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Site24x7/ManageWP Uptime', 'value' => 'Site24x7'],
        ['name' => 'UptimeRobot', 'value' => 'UptimeRobot'],
        ['name' => 'Better Uptime', 'value' => 'Better Uptime'],
        ['name' => 'Pingdom', 'value' => 'Pingdom'],
        ['name' => 'GTmetrix', 'value' => 'GTmetrix'],
        ['name' => 'StatusCake', 'value' => 'StatusCake'],
        ['name' => 'Uptrends', 'value' => 'Uptrends'],
        ['name' => 'Hyperspin', 'value' => 'Hyperspin'],
        ['name' => 'NewRelic', 'value' => 'NewRelic'],
        ['name' => 'Datadog', 'value' => 'Datadog'],
        ['name' => 'Monitis', 'value' => 'Monitis'],

        ['name' => __('Backup & Website Management Services', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'UpdraftPlus', 'value' => 'UpdraftPlus'],
        ['name' => 'BackupBuddy', 'value' => 'BackupBuddy'],
        ['name' => 'VaultPress', 'value' => 'VaultPress'],
        ['name' => 'BlogVault', 'value' => 'BlogVault'],
        ['name' => 'WP Time Capsule', 'value' => 'WPTC'],
        ['name' => 'WP Vivid Backup', 'value' => 'WPVivid'],
        ['name' => 'Duplicator', 'value' => 'Duplicator'],
        ['name' => 'All-in-One WP Migration', 'value' => 'AllInOneWPMigration'],
        ['name' => 'WP-CLI', 'value' => 'wp-cli'],
        
        ['name' => __('Link Preview & Archiving Services', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Facebook Link Preview', 'value' => 'facebookexternalhit'],
        ['name' => 'Facebook', 'value' => 'meta-externalagent'],
        ['name' => 'Twitter Link Preview', 'value' => 'Twitterbot'],
        ['name' => 'LinkedIn Link Preview', 'value' => 'LinkedInBot'],
        ['name' => 'Wayback Machine', 'value' => 'ia_archiver'],

        ['name' => __('Other Site Utilities & Crawlers', 'cloudflare-waf-rules-wizard'), 'value' => 'header'],
        ['name' => 'Google Lighthouse', 'value' => 'Lighthouse'],
        ['name' => 'Cloudflare Page Speed Test', 'value' => 'CloudflareObservatory'],
        ['name' => 'Wappalyzer', 'value' => 'Wappalyzer'],
        ['name' => 'BuiltWith', 'value' => 'BuiltWith'],
        ['name' => 'Netcraft', 'value' => 'NetcraftSurveyAgent'],
        ['name' => 'W3C Validator', 'value' => 'W3C_Validator'],
        ['name' => 'HTML5 Validator', 'value' => 'Nu Html Checker'],
    ];

    echo '<p><strong>' . __('Choose Built-in User Agents That Skip All Rules', 'cloudflare-waf-rules-wizard') . '</strong></p>';
    echo '<p>' . __('Be selective in checking only those you know should be used on these sites.', 'cloudflare-waf-rules-wizard') . '</p>';
    foreach ($availableAgents as $agent) {
        if ($agent['value'] === 'header') {
            // Display header without a checkbox
            echo '<h4>' . esc_html($agent['name']) . '</h4>';
        } else {
            // Display checkbox for user agent
            $checked = in_array($agent['value'], $userAgents) ? 'checked' : '';
            echo "<label><input type='checkbox' name='pw_cloudflare_user_agents[]' value='" . esc_attr($agent['value']) . "' $checked> " . esc_html($agent['name']) . " (uses '" . esc_html($agent['value'])  . "')</label><br>";
        }
    }

    echo '<p><strong>' . __('Custom User Agents:', 'cloudflare-waf-rules-wizard') . '</strong></p>';
    echo '<p>' . __('Enter one User Agent per line. Only alphanumeric characters, spaces, underscores, and hyphens are allowed.', 'cloudflare-waf-rules-wizard') . '</p>';
    echo "<textarea name='pw_cloudflare_custom_user_agents' rows='5' cols='50' placeholder='" . __('Enter one User Agent per line', 'cloudflare-waf-rules-wizard') . "'>" . esc_textarea($customUserAgents) . "</textarea>";
}

function pw_cloudflare_ruleset_manager_field_custom_allowed_ips() {
    $customAllowedIps = get_option('pw_cloudflare_custom_allowed_ips', '');
    echo '<p>' . __('Enter one IP address per line. Only valid IPv4 or IPv6 addresses are allowed.', 'cloudflare-waf-rules-wizard') . '</p>';
    echo "<textarea name='pw_cloudflare_custom_allowed_ips' rows='5' cols='50' placeholder='" . __('Enter one IP address per line', 'cloudflare-waf-rules-wizard') . "'>" . esc_textarea($customAllowedIps) . "</textarea>";
}

function pw_sanitize_user_agents($input) {
    return array_map('sanitize_text_field', (array) $input);
}

function pw_sanitize_custom_user_agents($input) {
    $lines = array_filter(array_map('trim', explode("\n", $input))); // Split by lines and trim
    $sanitized = array_filter($lines, function($line) {
        return preg_match('/^[a-zA-Z0-9 _\-]+$/', $line); // Allow only alphanumeric, spaces, underscores, and hyphens
    });
    return implode("\n", $sanitized); // Rejoin valid lines
}

function pw_sanitize_custom_allowed_ips($input) {
    $lines = array_filter(array_map('trim', explode("\n", $input))); // Split by lines and trim
    $sanitized = array_filter($lines, function($line) {
        return filter_var($line, FILTER_VALIDATE_IP); // Validate as IPv4 or IPv6
    });
    return implode("\n", $sanitized); // Rejoin valid lines
}

// Unified function to make curl requests
function pw_makeCurlRequest($url, $method, $headers, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Get the list of all zones based on the account ID
function pw_get_cloudflare_zones($accountId, $apiKey, $apiEmail) {
    $allZones = []; // Array to hold all zones
    $page = 1;
    $per_page = 50; // The maximum items per page you can request from the Cloudflare API for this endpoint

    do {
        $url = "https://api.cloudflare.com/client/v4/zones?account.id=$accountId&page=$page&per_page=$per_page";
        $headers = [
            "X-Auth-Email: $apiEmail",
            "X-Auth-Key: $apiKey",
            "Content-Type: application/json",
        ];
        $response = pw_makeCurlRequest($url, 'GET', $headers); 

        if (isset($response['result'])) {
            // Merge the retrieved zones into the allZones array
            $allZones = array_merge($allZones, $response['result']);
        }

        // Check if there are more pages to fetch
        $totalPages = isset($response['result_info']['total_pages']) ? (int) $response['result_info']['total_pages'] : 1;
        $page++;

    } while ($page <= $totalPages);

    return $allZones;
}

if (isset($_POST['pw_create_ruleset'])) {
    // Process the form when "Create Rules" button is clicked
    add_action('admin_init', 'pw_process_create_ruleset');
}
function pw_process_create_ruleset() {
    check_admin_referer('pw_create_ruleset_action', 'pw_create_ruleset_nonce');
    pw_cloudflare_ruleset_manager_process_zones();
}

function pw_cloudflare_ruleset_manager_process_zones() {
    $apiKey = sanitize_text_field(get_option('pw_cloudflare_api_key'));
    $email = sanitize_email(get_option('pw_cloudflare_api_email'));
    $zoneIds = isset($_POST['pw_zone_ids']) ? $_POST['pw_zone_ids'] : [];

    if (empty($apiKey) || empty($email) || empty($zoneIds)) {
        echo '<div class="notice notice-error is-dismissible"><p>' . __('Please enter all the required fields.', 'cloudflare-waf-rules-wizard') . '</p></div>';
        return;
    }

    $headers = [
        "X-Auth-Email: $email",
        "X-Auth-Key: $apiKey",
        "Content-Type: application/json",
    ];

    $userAgents = get_option('pw_cloudflare_user_agents', []);
    $customUserAgents = get_option('pw_cloudflare_custom_user_agents', '');
    $customUserAgentsArray = array_filter(array_map('trim', explode("\n", $customUserAgents))); // Split by lines and trim

    $customAllowedIps = get_option('pw_cloudflare_custom_allowed_ips', '');
    $customAllowedIpsArray = array_filter(array_map('trim', explode("\n", $customAllowedIps))); // Split by lines and trim

    $allUserAgents = array_merge($userAgents, $customUserAgentsArray);
    $userAgentExpressions = array_map(function($agent) {
        return "(http.user_agent contains \"$agent\")";
    }, $allUserAgents);

    // Generate IP expressions without quotes
    $ipExpressions = array_map(function($ip) {
        return "(ip.src eq $ip)";
    }, $customAllowedIpsArray);

    $rules = [
        [
            'description' => 'Good Bots Allow',
            'expression' => '(cf.client.bot) or (cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher"}) or (http.user_agent contains "letsencrypt" and http.request.uri.path contains "acme-challenge")' . (count($userAgentExpressions) ? ' or ' . implode(' or ', $userAgentExpressions) : '') . (count($ipExpressions) ? ' or ' . implode(' or ', $ipExpressions) : ''),
            'action' => 'skip',
            'action_parameters' => [
                'ruleset' => 'current',
                'phases' => ['http_ratelimit', 'http_request_sbfm', 'http_request_firewall_managed'],
                'products' => ['uaBlock', 'zoneLockdown', 'waf', 'rateLimit', 'bic', 'hot', 'securityLevel'],
            ],
        ],
        [
            'description' => 'MC Providers and Countries',
            'expression' => '(ip.src.asnum in {7224 16509 14618 15169 8075 396982} and not cf.client.bot and not cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher" "Aggregator"}) or (not ip.src.country in {"US"} and not cf.client.bot and not cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher" "Aggregator"} and not http.request.uri.path contains "acme-challenge")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'MC Aggressive Crawlers',
            'expression' => '(http.user_agent contains "yandex") or (http.user_agent contains "sogou") or (http.user_agent contains "semrush") or (http.user_agent contains "ahrefs") or (http.user_agent contains "baidu") or (http.user_agent contains "python-requests") or (http.user_agent contains "neevabot") or (http.user_agent contains "CF-UC") or (http.user_agent contains "sitelock") or (http.user_agent contains "crawl" and not cf.client.bot) or (http.user_agent contains "bot" and not cf.client.bot) or (http.user_agent contains "Bot" and not cf.client.bot) or (http.user_agent contains "Crawl" and not cf.client.bot) or (http.user_agent contains "spider" and not cf.client.bot) or (http.user_agent contains "mj12bot") or (http.user_agent contains "ZoominfoBot") or (http.user_agent contains "mojeek") or (ip.src.asnum in {135061 23724 4808} and http.user_agent contains "siteaudit")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'MC VPNs and WP Login',
            'expression' => '(ip.src.asnum in {60068 9009 16247 51332 212238 131199 22298 29761 62639 206150 210277 46562 8100 3214 206092 206074 206164 213074}) or (http.request.uri.path contains "wp-login")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'Block Web Hosts / WP Paths / TOR',
            'expression' => '(ip.src.asnum in {26496 31815 18450 398101 50673 7393 14061 205544 199610 21501 16125 51540 264649 39020 30083 35540 55293 36943 32244 6724 63949 7203 201924 30633 208046 36352 25264 32475 23033 32475 212047 32475 31898 210920 211252 16276 23470 136907 12876 210558 132203 61317 212238 37963 13238 2639 20473 63018 395954 19437 207990 27411 53667 27176 396507 206575 20454 51167 60781 62240 398493 206092 63023 213230 26347 20738 45102 24940 57523 8100 8560 6939 14178 46606 197540 397630 9009 11878}) or (http.request.uri.path contains "xmlrpc") or (http.request.uri.path contains "wp-config") or (http.request.uri.path contains "wlwmanifest") or (cf.verified_bot_category in {"AI Crawler" "Other"}) or (ip.src.country in {"T1"})',
            'action' => 'block'
        ],
    ];

    function pw_getRulesetId($zoneId, $headers) {
        $url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/rulesets";
        $response = pw_makeCurlRequest($url, 'GET', $headers);

        if (!empty($response['result'])) {
            foreach ($response['result'] as $ruleset) {
                if ($ruleset['kind'] == 'zone' && $ruleset['phase'] == 'http_request_firewall_custom') {
                    return $ruleset['id'];
                }
            }
        }
        return null;
    }

    function pw_createRuleset($zoneId, $headers) {
        $url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/rulesets";
        $data = [
            'name' => 'Custom ruleset for http_request_firewall_custom phase',
            'kind' => 'zone',
            'phase' => 'http_request_firewall_custom',
        ];

        $response = pw_makeCurlRequest($url, 'POST', $headers, $data);
        return $response['result']['id'] ?? null;
    }

    function pw_replace_or_patch_ruleset($zoneId, $rulesetId, $headers, $rules) {
        $url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/rulesets/{$rulesetId}";

        // Always replace the entire ruleset using PUT
        $data = ['rules' => $rules];
        $response = pw_makeCurlRequest($url, 'PUT', $headers, $data);

        return $response;
    }

    $zones = pw_get_cloudflare_zones(get_option('pw_cloudflare_account_id'), $apiKey, $email);

    foreach ($zoneIds as $zoneId) {
        $zoneName = "";
        foreach ($zones as $zone) {
            if ($zone['id'] == $zoneId) {
                $zoneName = $zone['name'];
                break;
            }
        }

        $rulesetId = pw_getRulesetId($zoneId, $headers);

        if (!$rulesetId) {
            $rulesetId = pw_createRuleset($zoneId, $headers);
            if (!$rulesetId) {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Failed to create ruleset for domain:', 'cloudflare-waf-rules-wizard') . ' ' . esc_html($zoneName) . '</p></div>';
                continue;
            }
        }

        $response = pw_replace_or_patch_ruleset($zoneId, $rulesetId, $headers, $rules);
        if (isset($response['success']) && $response['success']) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Successfully updated ruleset for domain:', 'cloudflare-waf-rules-wizard') . ' ' . esc_html($zoneName) . '</p></div>';
        } else {
            $errorMsg = isset($response['errors'][0]['message']) ? $response['errors'][0]['message'] : __('Unknown error', 'cloudflare-waf-rules-wizard');
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Failed to update ruleset for domain:', 'cloudflare-waf-rules-wizard') . ' ' . esc_html($zoneName) . '. ' . __('Error:', 'cloudflare-waf-rules-wizard') . ' ' . esc_html($errorMsg) . '</p></div>';
        }
    }
}
