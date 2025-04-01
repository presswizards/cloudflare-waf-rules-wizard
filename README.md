# Cloudflare WAF Rules Wizard
A simple plugin to create Cloudflare WAF custom rules using your Cloudflare API key. This plugin is based on the amazing work by Troy Glancy and his superb [Cloudflare WAF Rules](https://webagencyhero.com/cloudflare-waf-rules-v3/?utm=github-presswizards-cloudflare-waf-rules-wizard). Read through the WAF rules logic and details on his site.

<p/>
<a href="https://www.buymeacoffee.com/robwpdev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a><br>
If this plugin saves you time, helps your clients, or helps you do better work, I’d appreciate it.
</p>

## Summary
This plugin can be installed on any WordPress site you own, and then use it to bulk create the rules to as many domains in your Cloudflare account, including delegated member accounts you have access to.

It takes your Cloudflare API key, email, and account ID, and then gets all the domains in that account, and displays a checkbox list of them all, and you can choose the domains you want to add Troy’s WAF rules to, and bulk update all the domains with one click. Please see the notes and security tips in the plugin settings page.

## Added translations for the following languages:

Languages:
	•	English → en.po
	•	Spanish → es.po
	•	German → de.po
	•	French → fr.po
	•	Italian → it.po
	•	Portuguese → pt.po
	•	Dutch → nl.po
	•	Russian → ru.po
	•	Chinese (Simplified) → zh_CN.po
	•	Japanese → ja.po
	•	Korean → ko.po
	•	Arabic → ar.po
	•	Turkish → tr.po
	•	Hindi → hi.po
	•	Polish → pl.po
	•	Swedish → sv.po
	•	Danish → da.po
	•	Finnish → fi.po
	•	Greek → el.po
	•	Czech → cs.po
	•	Hungarian → hu.po
	•	Thai → th.po
	•	Hebrew → he.po

 Translated using my cool PHP script that uses OpenAI ChatGPT, check it out:
 https://github.com/presswizards/Translate-Plugins-OpenAI

## Some Important Notes
⚠️ **Please note that this plugin overwites the 5 WAF rules on all domains, it will erase the existing rules and create new ones.** These 5 rules should work with Cloudflare Free, Pro and Business plans. They do not work for Enterprise Cloudflare, which most likely your web hosting provider controls directly.

⚠️ **Use at your own risk.** These rules may block certain services such as monitoring, uptime, or CDN services, so you may need to add exclusions if those services suddenly can't connect to your domain(s), using the Events log in Cloudflare showing the user agent or other data to add to the first rule that allows requests to bypass the remaining rules.

## Configure Settings
On the plugin's option page: First, add you credentials to the Cloudflare WAF Rules Wizard settings page in the plugin. Your email is the email you log in with. You can retrieve your [API key here](https://dash.cloudflare.com/profile/api-tokens). And [here are instructions](https://developers.cloudflare.com/fundamentals/setup/find-account-and-zone-ids/)  for where you can find your Account ID, it should be the Overview page of one of the domains in the account. You can also add user agents and IPs to the Good Bots rule so they skip the other rules are are not challenged or blocked. 

![cf-waf-wizard-settings](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/c7b5adf7-1f85-4c0f-9794-9d139a6f45c2)

&nbsp;

![New user agent checkbox options](https://github.com/user-attachments/assets/7953b72d-8627-4cc6-b23c-942249178fc9)

&nbsp;

![New custom user agents and IP options](https://github.com/user-attachments/assets/016d447b-f5c6-4baa-bd41-249f2d123a70)

&nbsp;

This will pull in the domains from the Account ID you entered. Select which domains you'd like to apply the WAF rules to:

![cf-waf-wizard-settings-2](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/480e3cb1-ef46-4114-b4f8-ba89521858f0)

&nbsp;

Last, check your Cloudflare WAF Rules to see if they have applied:
![cf-waf-rules](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/59c371dd-de0b-42b8-bfb5-ab038ba2d88c)

&nbsp;

## Delete Settings and Deactivate/Delete
After you are done adding your shiny new WAF Rules: ⚠️ **don't forget to click the Delete Settings button** after you are done using this plugin to remove your credentials from the database, for best security practices. They are not encrypted when stored. Maybe future versions will encrypt, delete the options on deactivation, etc. Right now it is a quick and simple plugin for you to use, delete the settings, and then deactivate and delete the plugin. It is not recommended to keep the plugin settings long term, or to keep the plugin active. You can always repeat the above steps later for adding additional domains or deleting and recreating the rules for existing domains.

![cf-waf-wizard-delete-settings](https://github.com/zackpyle/cloudflare-waf-rules-wizard/assets/19413506/00a7ec48-c483-4017-a252-1adff80c600c)

The options page now hides the API key and Account ID, for Zoom training or overview video purposes, etc.

![Display security only](https://github.com/user-attachments/assets/7a5f4299-0b35-4767-ad31-81482f38d76d)

<p/>
<a href="https://www.buymeacoffee.com/robwpdev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/default-orange.png" alt="Buy Me A Coffee" height="41" width="174"></a><br>
If this plugin saves you time, helps your clients, or helps you do better work, I’d appreciate it.
</p>
