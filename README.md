Description
-----------
This is a Simple PHP pagination class working perfectly with all PHP projects

Example of usage:
-----------------

```<?php

$config = array(
	'base_url' => 'http://example.com/',
	'uri_path' => '', // URI path (IE: controller/action/id), leave none for auto detection
    'num_links' => 3, // Number of page links to show
    
    'total_items' => 0, // Total items (ie: count($allRowsFromDatabase))
    'items_per_page' => 20, // Number of items per page
    
    'get_query' => false, // (Default is false). Using query string or uri segment? See example below
    'uri_segment_position' => 0, // (if "get_query" is "false") Count from 0, Exp: controller/action/id/{page} => 3
    
    // (Optional) Page prefix and suffix (Exp: ?page=prefix{page}suffix)
    'prefix' => '',
    'suffix' => '',

    // (Optional) Link text
    'first_link_text' => 'First',
    'last_link_text' => 'Last',

    'previous_link_text' => 'Previous',
    'next_link_text' => 'Next'
);
$pagination = new DuyK\Pagination($config);

// Pagination data
$paginationData = $pagination->generate(); // You can use var_dump($paginationData) to see the structure
```

HTML:
```
<!-- Using pagination data to render pagination -->
<?php if(count($paginationData)) : ?>
<div class="pagination">
	
	<?php foreach($paginationData as $link) : 

		// Check if this is current page
		$isCurrent = ($link['current']) ? true : false;
	?>
		<a href="<?php echo $link['url']; ?>" class="item <?php if($isCurrent) { echo 'active'; } ?>"><?php echo $link['text']; ?></a>

	<?php endforeach; ?>
	
</div>
<?php endif; ?>
```

Example of using "get_query" => true
-----------------------------------
If "get_query" is "true", meaning that we'll use a HTTP GET param as your current page
```<?php
// If setting "get_query" => true
$config = array(
	...YOUR OTHER CONFIG...
	'uri_path' => 'blog/something',
	'get_query' => 'page',
);
```

Result will be like this:
```
If URL is: http://example.com/blog
Then pagination links will be:
http://example.com/blog?page=1
http://example.com/blog?page=2
http://example.com/blog?page=3
http://example.com/blog?page=4
...

Don't worry if your URL contains other GET param. For example if your URL is: http://example.com/blog?param1=test&param2=something
Then pagination links will be:
http://example.com/blog?param1=test&param2=something&page=1
http://example.com/blog?param1=test&param2=something&page=2
http://example.com/blog?param1=test&param2=something&page=3
http://example.com/blog?param1=test&param2=something&page=4
...

```

Example of using "get_query" => false
-------------------------------------
If "get_query" is false, meaning that we'll use a segment in your URI path to detect your current page
```<?php

// If setting "get_query" => false
// Full URL in this example: http://example.com/blog/category-name/1/something/else (1 is the current page)
// Also available if the "page" is the last segment like: http://example.com/blog/category-name/{page} 
// If {page} is null, then its value will be treated as "1"
$config = array(
	...YOUR OTHER CONFIG...
	'get_query' => false,
	'uri_segment_position' => 2, // Will be 2 in this case because [0 => 'blog', 1 => 'category-name', 2 => {page-number-here}...]
);
```

Result will be like this
```
http://example.com/blog/category-name/1/something/else
http://example.com/blog/category-name/2/something/else
http://example.com/blog/category-name/3/something/else
http://example.com/blog/category-name/4/something/else
...
```