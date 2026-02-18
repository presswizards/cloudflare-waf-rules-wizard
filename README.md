# Cloud Maestro - WAF Security Suite for Cloudflare

It is now in the WP Repo here:
<a target="_blank" href="https://wordpress.org/plugins/waf-security-suite-for-cloudflare/">https://wordpress.org/plugins/waf-security-suite-for-cloudflare/</a>

Cloud Maestro brings centralized Cloudflare Web Application Firewall (WAF) controls directly into WordPress.

**Why would I use a plugin when I can create rules in Cloudflare?**
If you manage multiple Cloudflare-connected sites, Cloud Maestro is a productivity tool that helps oversee several domains from a central dashboard using WordPress. If you only manage one domain in Cloudflare, you wouldn't benefit from this plugin.

It’s useful for someone managing:
- Their own sites and client sites
- Multiple businesses
- Separate Cloudflare accounts

People like using Cloud Maestro because configuring security rules one domain at a time is inefficient and error-prone. It allows you to configure WAF rules once and deploy them consistently across all domains in your Cloudflare account — instantly.

The free version supports one Cloudflare account with multiple domains.

An optional premium version is available for managing unlimited domains across multiple Cloudflare accounts at once. 

### 🛡️ Why Use Cloud Maestro - WAF Security Suite for Cloudflare?

Managing security rules across multiple Cloudflare domains is tedious and time-consuming. This plugin streamlines the process, allowing you to:

* **Deploy in One Click** - Apply comprehensive WAF rules to multiple domains simultaneously
* **Save Time** - No more manually configuring rules on each domain, one at a time
* **Enterprise Security** - Protect against bots, aggressive crawlers, malicious IPs, and common threats
* **Reduce Mistakes** - Maintain consistent security rules across domains

### ✅ Free Standard Features
* One Cloudflare account
* Multiple domains
* One-click WAF rule deployment
* Centralized Cloudflare controls
* Secure API credential storage (AES-256-CBC encryption)
* Plugin updates
The free plugin does not require an upgrade.

### 🔥 What Gets Protected

The plugin deploys **3 optimized trusted security rules** (prior versions used 5) that work together to protect your sites:

* **Good Bot Allowlist** - Ensures legitimate bots (Google, Bing, monitoring tools) can access your site
* **Managed Challenges for Suspicious Traffic** - Automatically challenges requests from certain ASNs and non-US traffic
* **Aggressive Crawler Protection** - Blocks unauthorized crawlers and bots (Yandex, Semrush, Ahrefs, etc.)
* **VPN & Login Protection** - Adds extra challenges for VPN traffic and WordPress login attempts
* **Block Known Threats** - Automatically blocks web hosts, malicious IPs, TOR nodes, and attack vectors

### ✨ Premium Upgrade (Optional)

For agencies and professionals managing multiple Cloudflare accounts, a Premium version is available with expanded functionality and tech support. **[Check out our free trial](https://5starplugins.com/cloud-maestro-cloudflare-waf-rules/)** for these features:

* **Multi-Account Management** - Automatically manage domains across ALL your Cloudflare accounts
* **Easy Bot Whitelisting** - Built-in checkboxes for 50+ trusted services across 8 categories
* **Custom User Agents** - Add your own user agent strings to the Good Bot Rule
* **Custom IP Whitelisting** - Add trusted IP addresses to the Goot Bot Rule
* **IP Rules management** - View and edit Cloudflare's IP Rules that block or allow access even before hitting WAF rules (and we are working on connecting to fail2ban and Wordfence blocks)
* **Priority Support** - Get expert help when you need it
* **Advanced Customization** - Fine-tune rules to match your exact requirements
* **Multi-Account Management** – Centrally manage unlimited domains across all your Cloudflare accounts

### 📋 Important Information

**Rule Replacement:** This plugin replaces existing custom WAF rules on targeted domains. Make sure to back up any custom rules you want to keep.

**Compatibility:** Works with Cloudflare Free, Pro, and Business plans. Not compatible with Enterprise plans managed by hosting providers.

**Service Monitoring:** These rules might challenge some monitoring or uptime services. Check Cloudflare's Events log if services stop connecting, and add exceptions as needed.

### Getting Started

1. After activation, navigate to **Cloud Maestro** in your WordPress admin menu
2. Enter your Cloudflare API details:
   * **API Token** - Paste in your existing API Token, or click the Generate New Token button.
   * **Account ID** - Choose your account ID from the drop-down.
   * Global API Key is also supported but NOT recommended.
3. Click **Save Settings** to retrieve your domains
4. Select the domains you want to protect
5. Click **Create/Overwrite All WAF Rules**
6. Verify the rules in your Cloudflare account to ensure it's working as it should the first time.

That's it! Your sites are now protected.

The Premium version offers easy checkbox selection of common service user agents, and type in custom user agents or IPs.

