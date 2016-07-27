Drillr
====

### Forget about for-each loops when iterating HTML code

Drillr is pretty simple and straightforward library made to automate procedural for-each loops that iterates passing collections of data
(which is a pretty common thing, even nowadays)

### A little background  

The place I used to work (as a spaghetti PHP developer) had a pretty methodic workflow for deploying simple websites:
 * The Layout was made by the art guy
 * The Front-end guy translated it all to HTML code
 * Me, the back-end was responsible to code afterwards, on top of dozens of static pages

Well, the thing is, I didn't have much contact with MVC, or any frameworks and templating engines at all back in the day, so I had the idea to automate the for-each loops
and encapsulate all the "blocks" into tiny template partials (which is what this class is all about).

Using Drillr
----------

Get the singleton instance of it and declare (or fetch) a dummy collection:
```php
$collection = array( array('dummy_data' => 'foo'), array('dummy_data' => 'bar') );
$drillr = Drillr::getInstance();
```

Create some random templates lets say I put this simple html file called DrillrTest.html inside a random, lets say, "public/templates" folder:
```html
<span>{{dummy_data}}</span>
```

Then simply point where the html block is and use the drill() method to iterate over the collection  
```php
$collection = array( array('dummy_data' => 'foo'), array('dummy_data' => 'bar') );
$drillr = Drillr::getInstance();
$drillr->addToPath(__DIR__.'/public/templates/')->loadBlock('DrillrTest.html')->drill($collection);
```

Your outputs gonna be:
```html
<span>foo</span><span>bar</span>
```

B-but I need to output 'filtered foo' and 'filtered bar' instead, WELL I GOT YOU COVERED:
```php
function testFilter($param)
{
    return 'filtered '. $param;
}
$drillr->addFilter('testFilter',array('dummy_data'), 'dummy_data')->drill($collection);
```

```html
<span>filtered foo</span><span>filtered bar</span>
```

Theres a lot of other silly features to it :) 

TODO:
 * Add perhaps demo folder with lots of demonstration and usage samples
 * Finish the testing suite ;P