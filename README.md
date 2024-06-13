# Cloudflare WAF Rules Wizard
A simple plugin to create Cloudflare WAF custom rules based on account ID (based on Troy Glancy's superb CF WAF v3 rules)

It takes your CF API key, email, and account ID, and then gets all the domains in that account, and displays a checkbox list, and you can choose the domains you want to add Troy’s WAF rules to, and bulk update all the domains with one click. Please see the notes and security tips in the plugin settings page, use at your own risk

First add you credentials to the settings page in the plugin:
![cf-waf-wizard-settings](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/c7b5adf7-1f85-4c0f-9794-9d139a6f45c2)

Next, select which domains you'd like to apply the WAF rules to:
![cf-waf-wizard-settings-2](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/480e3cb1-ef46-4114-b4f8-ba89521858f0)

Last, check your Cloudflare WAF Rules to see if they have applied:
![cf-waf-rules](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/59c371dd-de0b-42b8-bfb5-ab038ba2d88c)
