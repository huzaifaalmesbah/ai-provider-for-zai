=== AI Provider for Z.AI ===
Contributors: huzaifaalmesbah
Tags: ai, zai, glm, artificial-intelligence, connector
Requires at least: 7.0
Tested up to: 7.0
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI Provider for Z.AI (GLM models) for the WordPress AI Client.

== Description ==

This plugin provides Z.AI integration for the WP AI Client SDK. It enables WordPress sites to use Z.AI GLM models for text generation and related AI capabilities.

**Features:**

* Text generation with Z.AI GLM models (glm-4.5, glm-4.6, glm-4.7, glm-5, and more)
* Chat history support
* Function calling support
* JSON output support
* Automatic provider registration

Available models are dynamically discovered from the Z.AI API.

**Requirements:**

* PHP 7.4 or higher
* WP AI Client plugin must be installed and activated
* Z.AI API key from z.ai

== Installation ==

1. Ensure the WP AI Client plugin is installed and activated
2. Upload the plugin files to `/wp-content/plugins/ai-provider-for-zai/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Open the WP AI Client credentials screen (the AI Credentials / "Connectors" settings provided by the WP AI Client plugin) and enter your Z.AI API key. The correct API type is auto-detected from the key.

== Screenshots ==

1. AI Client Credentials settings page where you enter your Z.AI API key.

== Usage ==

The provider automatically registers itself on the `init` hook. Once both plugins are active and your API key is configured, you can start generating text:

= Basic Text Generation =

`$text = AI_Client::prompt( 'Explain quantum computing.' )->using_provider( 'zai' )->generate_text();`

= With System Instructions =

`$text = AI_Client::prompt( 'Summarize the history of WordPress.' )->using_provider( 'zai' )->using_system_instruction( 'Be concise and accurate.' )->using_temperature( 0.2 )->using_max_tokens( 500 )->generate_text();`

= JSON Output =

`$json = AI_Client::prompt( 'Analyze this topic: WordPress plugins' )->using_provider( 'zai' )->as_json_response( $schema )->generate_text();`

== Frequently Asked Questions ==

= How do I get a Z.AI API key? =

Visit [z.ai](https://z.ai) to create an account and generate an API key.

= Which models are available? =

Available models include glm-4.5, glm-4.5-air, glm-4.6, glm-4.7, glm-5, and glm-5-turbo. Models are dynamically fetched from the Z.AI API, so new models will appear automatically.

= Does this plugin work without the WP AI Client? =

No, this plugin requires the WP AI Client plugin to be installed and activated. It provides the Z.AI-specific implementation that the WP AI Client uses.

== External services ==

This plugin connects to the Z.AI API to provide AI-powered text generation capabilities within WordPress. Z.AI is a third-party service.

= What data is sent and when =

* **API key**: Your Z.AI API key is sent with every request for authentication.
* **API type auto-detection**: When you save or change your API key, the plugin sends one minimal test request (a one-word prompt) to each Z.AI endpoint to determine which API type your key belongs to. This happens only at the moment the key is saved.
* **Model listing**: When the plugin checks provider availability or lists available models, it sends a request to the Z.AI API to retrieve the current list of GLM models.
* **Text generation prompts**: When your site uses the plugin to generate text, the prompt text (and any system instructions and conversation history) is sent to the Z.AI API for processing.

Communication is sent to the Z.AI API at one or both of the following base URLs (the API-type auto-detection contacts both; all other requests use the detected one):

* General API: [https://api.z.ai/api/paas/v4](https://api.z.ai/api/paas/v4)
* Coding API: [https://api.z.ai/api/coding/paas/v4](https://api.z.ai/api/coding/paas/v4)

Data is only sent when the plugin is actively used to generate text or when checking model availability. No data is sent passively or in the background.

= Service links =

* Z.AI website: [https://z.ai](https://z.ai)
* Z.AI Terms of Service: [https://z.ai/terms](https://z.ai/terms)
* Z.AI Privacy Policy: [https://z.ai/privacy](https://z.ai/privacy)

== Changelog ==

= 1.0.0 =
* Initial release
* Support for Z.AI GLM text generation models (glm-4.5, glm-4.6, glm-4.7, glm-5, and more)
* Chat history support
* Function calling support
* JSON output support

== Upgrade Notice ==

= 1.0.0 =
Initial release.
