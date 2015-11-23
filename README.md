NOTE: Major rework coming between version 0.2 and 0.3 to follow better standards

# WordPress Split Testing Plugin - MVTS

This plugin provides an interface to easily configure split tests on your WordPress site. 

Defining the test group, handling consistency between visits and sending the results to an analytics platform is taken care of.

## Instalation, Activation, Basic Usage

* Download the plugin, unzip it and upload the folder to your `wp-content/plugins` directory.
* Visit the plugins page in your WordPress Dashboard and activate the plugin.
* Go to the new 'MVTS' tab in the sidebar and fill in your test details.

### Using the Plugin


The plugin needs a few details filled in to work correctly.

* A Name for the test - this gets reported to GA so you can isolate test results.
* The target selector of the element your testing - this is the CSS `class` or `id`.
* The type of test to run - the plugin offers _style tests_ or _content tests_.

_More advanced users are welcome to chain their target selector. For example: `#main_box .content.post > p`_

#### Style Tests

Style tests are performed by making changes to the CSS styles of the element. The plugin offers some pre-defined css options such as colors, margins and font-sizes. 

Select your desired change and enter the value to change it to (the values are the same values you would specify via CSS).

TODO: ADD ABILITY TO ADD OWN STYLE RULES. 
TODO: ADD ABILITY TO ADD/REMOVE CSS CLASSES

#### Content Tests

A content test essentially changes EVERYTHING within the element that you are wanting to test. This is extremely useful when you want to test different Call To Action phrases or Button Text.

Simply enter what you want to change the content to.

##### Version Log

Version 0.2:

* Added ability to run content based changes - change text in buttons, taglines or whole chunks of markup
* Improved validation on the data
* Slightly improved interface
* 2 bugfixes with unescaped (and escaped) html 
  * unescaped html would cause admin page to break layout if single quotes were entered
  ** excaped html would cause the JavaScript test code to fail during content tests

Version 0.1:

* First functional version
* Ability to test

Version 0.0.1

* Plugin Creation

##### Credits & Thanks

Without certain things this plugin wouldn't be possible. Here's a non-exclusive list of credits and thanks.

1. WordPress
2. jQuery
3. Cohorts


