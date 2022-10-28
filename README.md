# easy-fc
Easy Flashcard plugin for Wordpress

=== Easy Flashcards ===
Contributors: florinmuscalu
Tags: flashcads
Donate link: http://www.florinm.ro
Requires at least: 3.3
Requires PHP: 5.2.4
Tested up to: 5.5.3
Stable tag: 1.0.0
License: MIT License
License URI: http://opensource.org/licenses/MIT

Easy fc allows you to easily create and integrate flashcards in your page.

== Description ==
Create a txt file containing the flashcards you wish to integrate. Example:

genk.txt:
	{
	"q1":{"q":"2 to the 10th?", "a":"1024"},
	"q2":{"q":"Value of PI?",   "a":"3.14"}
	}
	
Load the file to your website.
Then, in the page, add the flashcard:

[easyfc title="General Knowledge" file="wp-content/uploads/2020/12/genk.txt"/]

And that's it. If you want to customize the look, modify easyfc.css.

== Screenshots ==

1. Front of the card
2. Back of the card

== Changelog ==

= 1.0.0 =
Initial upload

== Frequently Asked Questions ==
N/A

== Upgrade Notice ==
N/A
