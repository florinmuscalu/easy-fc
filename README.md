### === Easy Flashcards ===
Contributors: florinmuscalu</br>
Tags: flashcads</br>
Donate link: http://www.florinm.ro</br>
Requires at least: 3.3</br>
Requires PHP: 5.2.4</br>
Tested up to: 6.0.3</br>
Stable tag: 1.0.0</br>
License: MIT License</br>
License URI: http://opensource.org/licenses/MIT</br>
</br>
Easy fc allows you to easily create and integrate flashcards in your page.

### == Description ==
Create a txt file containing the flashcards you wish to integrate. Example:</br>
genk.txt:
```json
	{
	"q1":{"q":"2 to the 10th?", "a":"1024"},
	"q2":{"q":"Value of PI?",   "a":"3.14"}
	}
```
Load the file to your website.</br>
Then, in the page, add the flashcard:</br>
```php
[easyfc title="General Knowledge" file="wp-content/uploads/2020/12/genk.txt"/]
```
And that's it. If you want to customize the look, modify easyfc.css.

### == Screenshots ==

1. Front of the card
2. Back of the card

### == Changelog ==
#### = 1.0.0 =
Initial upload

### == Frequently Asked Questions ==
N/A

### == Upgrade Notice ==
N/A
