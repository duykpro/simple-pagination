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

Example of using "get_query" figure
-----------------------------------
If "get_query" is "FALSE", meaning that we'll use a segment in your URI path to set the current page
```<?php
// If setting "get_query" => false
$config = array(
	...YOUR OTHER CONFIG...
	'uri_path' => 'this/is/an/example/123/blah/blah',
	'get_query' => false,
	'uri_segment_position' => 4, // Here is 4 in this case, we'll detect it's "123"
);
```

Result will be like this:
...Continuing...