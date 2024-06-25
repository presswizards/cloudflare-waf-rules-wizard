<?php
/*
Plugin Name: Cloudflare WAF Custom Rules Wizard
Description: A simple plugin to create Cloudflare WAF custom rules based on account ID (based on Troy Glancy's superb CF WAF v3 rules)
Version: 1.7
Author: Rob Marlbrough - PressWizards.com
Author URI:        https://presswizards.com/
License:           GPL v3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 5.2
Requires PHP:      7.4
*/

// Add a menu item for the plugin settings page
add_action('admin_menu','pw_cloudflare_ruleset_manager_menu');
function pw_cloudflare_ruleset_manager_menu() {
    if (current_user_can('manage_options')) {
        add_options_page(
            'Cloudflare WAF Rules Wizard',
            'CF Rules Wizard',
            'manage_options',
            'pw_cloudflare-ruleset-manager',
            'pw_cloudflare_ruleset_manager_options_page'
        );
    }
}

// Display the plugin settings page
function pw_cloudflare_ruleset_manager_options_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?>
    <div class="wrap">
        <h2>Cloudflare WAF Custom Rules Wizard</h2>
        <p>Created by Rob Marlbrough at <a target="_blank" href="https://presswizards.com/">Press Wizards - WordPress Design, Hosting, and Maintenance</a></p>

        <?php if(get_option('pw_cloudflare_account_id') && get_option('pw_cloudflare_api_key') && get_option('pw_cloudflare_api_email')) { ?>
                <form method="post">
                        <?php
                        $zones = pw_get_cloudflare_zones(
                                get_option('pw_cloudflare_account_id'),
                                get_option('pw_cloudflare_api_key'),
                                get_option('pw_cloudflare_api_email')
                        );
                        ?>
                        <h3>Select Domains to Reset WAF Custom Rules on:</h3>
                        <?php foreach ($zones as $zone): ?>
                                <label>
                                        <input type="checkbox" name="pw_zone_ids[]" value="<?php echo esc_attr($zone['id']); ?>">
                                        <?php echo esc_html($zone['name']); ?> <?php // echo esc_html($zone['id']); ?>
                                </label><br>
                        <?php endforeach; ?>
                        <br/>
                        <?php // Add nonce field for security
                        wp_nonce_field('pw_create_ruleset_action', 'pw_create_ruleset_nonce'); ?>
                        <input type="submit" class="button button-primary" name="pw_create_ruleset" value="Create/Overwrite All WAF Rules">
                </form>
        <?php } ?>
                <p>&nbsp;</p>
        <form method="post" action="options.php">
                <?php
                settings_fields('pw_cloudflare_ruleset_manager_options');
                do_settings_sections('pw_cloudflare-ruleset-manager');
                // Add nonce field for security
                wp_nonce_field('pw_update_settings_action', 'pw_update_settings_nonce');
                submit_button('Save Settings');
                ?>
        </form>

        <form method="post" action="">
            <input type="hidden" name="pw_delete_settings" value="1" />
            <?php wp_nonce_field('pw_delete_settings_action', 'pw_delete_settings_nonce'); ?>
            <input type="submit" class="button button-secondary" value="Delete Settings" />
        </form>

                <p>&nbsp;</p>
                <p>Based on <a target="_blank" href="https://webagencyhero.com/cloudflare-waf-rules-v3/">Troy Glancy's superb Cloudflare WAF Rules v3</a></p>

    </div>
    <?php
}

// Register and define the plugin settings
add_action('admin_init', 'pw_cloudflare_ruleset_manager_settings');
function pw_cloudflare_ruleset_manager_settings() {
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_api_key', 'sanitize_text_field');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_api_email', 'sanitize_email');
    register_setting('pw_cloudflare_ruleset_manager_options', 'pw_cloudflare_account_id', 'sanitize_text_field');

    add_settings_section('pw_cloudflare_ruleset_manager_main', 'Cloudflare API Settings', 'pw_cloudflare_ruleset_manager_section_text', 'pw_cloudflare-ruleset-manager');

    add_settings_field('pw_cloudflare_api_key', 'API Key', 'pw_cloudflare_ruleset_manager_field_api_key', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_api_email', 'API Email', 'pw_cloudflare_ruleset_manager_field_api_email', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
    add_settings_field('pw_cloudflare_account_id', 'Account ID', 'pw_cloudflare_ruleset_manager_field_account_id', 'pw_cloudflare-ruleset-manager', 'pw_cloudflare_ruleset_manager_main');
}

add_action('admin_init', 'pw_handle_delete_settings');
function pw_handle_delete_settings() {
    if (isset($_POST['pw_delete_settings']) && check_admin_referer('pw_delete_settings_action', 'pw_delete_settings_nonce')) {
        delete_option('pw_cloudflare_api_key');
        delete_option('pw_cloudflare_api_email');
        delete_option('pw_cloudflare_account_id');
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Cloudflare WAF Rules Wizard settings have been deleted.</p></div>';
        });
    }
}

function pw_cloudflare_ruleset_manager_section_text() {
    echo '<p>Enter your Cloudflare API Key and Email address, and the Account ID.</p>';
    echo '<p>When you Save the settings, it will get all the domains under the account ID, and you can select the ones you want CF WAF custom rules created in.</p>';
    echo '<p><a href="https://webagencyhero.com/cloudflare-waf-rules-v3/" target="_blank">Dig into Troy\'s WAF Rules v3 - each rule explained in detail</a>. You can further customize the rules after they are created directly in Cloudflare under Security > WAF > Custom rules.</p>';
    echo '<p><strong>NOTE: It resets all WAF custom rules! If any other WAF Custom Rules exist for that domain, it will erase them, and create 5 new rules. IMPORTANT: Use at your own risk!</strong></p>';
    echo '<p>Security Tip: Click the Delete Settings button after you are done using this plugin to remove your credentials. They are not encrypted when stored. Maybe future versions will encrypt, delete the options on deactivation, etc. Right now it is a quick and simple plugin for you to use and then remove.</p>';
}

function pw_cloudflare_ruleset_manager_field_api_key() {
    $apiKey = get_option('pw_cloudflare_api_key');
    echo "<input type='text' name='pw_cloudflare_api_key' value='$apiKey' />";
}

function pw_cloudflare_ruleset_manager_field_api_email() {
    $apiEmail = get_option('pw_cloudflare_api_email');
    echo "<input type='email' name='pw_cloudflare_api_email' value='$apiEmail' />";
}

function pw_cloudflare_ruleset_manager_field_account_id() {
    $accountId = get_option('pw_cloudflare_account_id');
    echo "<input type='text' name='pw_cloudflare_account_id' value='$accountId' />";
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
        echo '<div class="notice notice-error is-dismissible"><p>Please enter all the required fields.</p></div>';
        return;
    }

    $headers = [
        "X-Auth-Email: $email",
        "X-Auth-Key: $apiKey",
        "Content-Type: application/json",
    ];

    $rules = [
        [
            'description' => 'Good Bots Allow',
            'expression' => '(cf.client.bot) or (cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher"}) or (http.user_agent contains "letsencrypt" and http.request.uri.path contains "acme-challenge") or (http.user_agent contains "ExactDN")',
            'action' => 'skip',
            'action_parameters' => [
                'ruleset' => 'current',
                'phases' => ['http_ratelimit', 'http_request_sbfm', 'http_request_firewall_managed'],
                'products' => ['uaBlock', 'zoneLockdown', 'waf', 'rateLimit', 'bic', 'hot', 'securityLevel'],
            ],
        ],
        [
            'description' => 'MC Providers and Countries',
            'expression' => '(ip.geoip.asnum in {7224 16509 14618 15169 8075 396982} and not cf.client.bot and not cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher" "Aggregator"}) or (not ip.geoip.country in {"US"} and not cf.client.bot and not cf.verified_bot_category in {"Search Engine Crawler" "Search Engine Optimization" "Monitoring & Analytics" "Advertising & Marketing" "Page Preview" "Academic Research" "Security" "Accessibility" "Webhooks" "Feed Fetcher" "Aggregator"} and not http.request.uri.path contains "acme-challenge")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'MC Aggressive Crawlers',
            'expression' => '(http.user_agent contains "yandex") or (http.user_agent contains "sogou") or (http.user_agent contains "semrush") or (http.user_agent contains "ahrefs") or (http.user_agent contains "baidu") or (http.user_agent contains "python-requests") or (http.user_agent contains "neevabot") or (http.user_agent contains "CF-UC") or (http.user_agent contains "sitelock") or (http.user_agent contains "crawl" and not cf.client.bot) or (http.user_agent contains "bot" and not cf.client.bot) or (http.user_agent contains "Bot" and not cf.client.bot) or (http.user_agent contains "Crawl" and not cf.client.bot) or (http.user_agent contains "spider" and not cf.client.bot) or (http.user_agent contains "mj12bot") or (http.user_agent contains "ZoominfoBot") or (http.user_agent contains "mojeek") or (ip.geoip.asnum in {135061 23724 4808} and http.user_agent contains "siteaudit")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'MC VPNs and WP Login',
            'expression' => '(ip.geoip.asnum in {60068 9009 16247 51332 212238 131199 22298 29761 62639 206150 210277 46562 8100 3214 206092 206074 206164 213074}) or (http.request.uri.path contains "wp-login")',
            'action' => 'managed_challenge'
        ],
        [
            'description' => 'Block Web Hosts / WP Paths / TOR',
            'expression' => '(ip.geoip.asnum in {26496 31815 18450 398101 50673 7393 14061 205544 199610 21501 16125 51540 264649 39020 30083 35540 55293 36943 32244 6724 63949 7203 201924 30633 208046 36352 25264 32475 23033 32475 212047 32475 31898 210920 211252 16276 23470 136907 12876 210558 132203 61317 212238 37963 13238 2639 20473 63018 395954 19437 207990 27411 53667 27176 396507 206575 20454 51167 60781 62240 398493 206092 63023 213230 26347 20738 45102 24940 57523 8100 8560 6939 14178 46606 197540 397630 9009 11878}) or (http.request.uri.path contains "xmlrpc") or (http.request.uri.path contains "wp-config") or (http.request.uri.path contains "wlwmanifest") or (cf.verified_bot_category in {"AI Crawler" "Other"}) or (ip.geoip.country in {"T1"})',
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

    function pw_replaceRuleset($zoneId, $rulesetId, $headers, $rules) {
        $url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/rulesets/{$rulesetId}";
        $data = [
            'rules' => $rules,
        ];
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
                echo '<div class="notice notice-error is-dismissible"><p>Failed to create ruleset for domain: ' . esc_html($zoneName) . '</p></div>';
                continue;
            }
        }

        $response = pw_replaceRuleset($zoneId, $rulesetId, $headers, $rules);
        if (isset($response['success']) && $response['success']) {
            echo '<div class="notice notice-success is-dismissible"><p>Successfully updated ruleset for domain: ' . esc_html($zoneName) . '</p></div>';
        } else {
            $errorMsg = isset($response['errors'][0]['message']) ? $response['errors'][0]['message'] : 'Unknown error';
            echo '<div class="notice notice-error is-dismissible"><p>Failed to update ruleset for domain: ' . esc_html($zoneName) . '. Error: ' . esc_html($errorMsg) . '</p></div>';
        }
    }
}
?>
