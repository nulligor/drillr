Drillr :cyclone:
====

### Forget about for-each loops when iterating Arbitrary Markdown

Drillr is a pretty simple and straightforward library made to automate procedural for-each loops that iterates "view blocks" passing collections of data

### A little background  

The place I used to work had a pretty methodic workflow for deploying simple websites:
 * The Layout was made by the art guy
 * The Front-end guy translated it all to HTML code
 * And finally, me, had to inject code and functionality on top of dozen of static documents

I didn't have much contact with MVC, or any frameworks and templating engines concepts at all back in the day, so I had the idea to automate the for-each loops and encapsulate all the "blocks" into tiny template partials to make them reusable as iterating blocks.

Using Drillr
----------

Get the singleton instance of it:
```php
require 'path/to/Drillr.php';
$drillr = Drillr::getInstance();
```

Create some random templates lets say I put this simple html file called DrillrTest.html inside a random folder "public/templates":
```html
<span>{{dummy_data}}</span>
```

Declare (or fetch) a dummy (or an actual) collection then simply point where the html block is and use the drill() method to iterate over the collection  
```php
$collection = array(array('dummy_data' => 'foo'), array('dummy_data' => 'bar'));
$drillr->addToPath(__DIR__.'/public/templates/')->loadBlock('DrillrTest.html')->drill($collection);
```

Your output:
```html
<span>foo</span><span>bar</span>
```

You can even inject middlewares to model your data before drilling your collection:
```php
function testFilter($param) {
    return 'filtered '. $param;
}

$drillr->addFilter('testFilter', array('dummy_data'), 'dummy_data')->drill($collection);
```
Will output:
```html
<span>filtered foo</span><span>filtered bar</span>
```

You can also add HTML wrappers so your data gets output inside any arbitrary piece of HTML code you'd like and much more (not really much more). 

TODO:
 * Finish the testing suite
 * Add more consistent error handling
