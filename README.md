# Craft User Manual plugin for Craft CMS 3.x

Craft User Manual allows developers (or even content editors) to provide CMS documentation using Craft's built-in sections (singles, channels, or structures) to create a "User Manual" or "Help" section directly in the control panel.

![Screenshot](resources/img/screenshot.jpg)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require hillholliday/craft-user-manual

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for usermanual.

4. Select the section the plugin should use as the **User Manual** page in the CP.
    * (Optional) - Replace the plugin's name to something your user's will understand.
    * (Optional) - Use more than the default `body` fieldhandle by setting up custom template overrides.

5. Click the **User Manual** link in the CP nav.

## Configuration

* All settings may be optionally configured using a [config file](http://buildwithcraft.com/docs/plugins/plugin-settings#config-file). The values, contained in [`config.php`](https://github.com/hillholliday/Craft-User-Manual/blob/master/src/config.php), are described below:

<a id="config-settings-pluginNameOverride"></a>
### pluginNameOverride
Intuitive, human-readable plugin name for the end user.

<a id="config-settings-templateOverride"></a>
### templateOverride
For more control over the output, you may optionally override the default template.

Path is relative to ../craft/templates/.

<a id="config-settings-section"></a>
### section
Entries in this section must have associated urls.

## Some notes
* The plugin currently only pulls in the `body` field from each entry in the selected section, unless you're using a template override.
* While the **User Manual** section works best with `Structures`, you can certainly get away with using a one-off `Single`.
* If you're running _Craft Client_ or _Craft Pro_ make sure your content editors don't have permission to edit whatever section you've selected to use as your **User Manual**
* Only sections with entry URLs may be used as your **User Manual** section.

## Thanks
This plugin was inspired by the team over at [70kft](http://70kft.com/) for their work on [Craft-Help](https://github.com/70kft/craft-help). While their plugin is definitely more flexible in terms of writing custom markdown in separate files, we wanted to create something that would make it easier for anyone to edit documentation without making any changes to the server. This works particularly well for larger projects where more than one person (especially non-devs) are writing documentation for how to use the CMS.

## Releases
* **2.0.1** - Merging in @mgburns fix for deprecation warnings.
* **2.0.0** - Merging in @jcdarwin's Craft3 port. Craft3 version of Craft User Manual.
* **1.1.1** - Adding in RTL language support
* **1.1.0** - Merging in @timkelty's work which includes template overrides, updated error prompts, and other misc improvements
* **1.0.1** - Adding support for plugin custom icons in Craft 2.5
* **1.0.0** - Initital release of Craft User Manual.

We hope this plugin is useful, and we'd love to hear any suggestions or issues you may have. [@erskinerob](https://twitter.com/erskinerob).

Brought to you by [Rob Erskine](https://twitter.com/erskinerob)
