`rcache` is a redis-based cache that supports grouped cache keys.

```php
<?php
require __DIR__."/rcache.php";

use noodlehaus\rcache;

// setup your redis conn
$conn = rcache_init('localhost', 6379);

// plain key
rcache_set($conn, 'topic-001-title', 'this is rc!');

// grouped keys, colon-separated path (under topic-001)
rcache_set($conn, 'topic-001:page-01', 'some text here');
rcache_set($conn, 'topic-001:page-02', 'some text here');

// get a value
$page_01 = rcache_get($conn, 'topic-001:page-01');

// get all values for group
$topic_001 = rcache_get($conn, 'topic-001');

// invalidate an entire group of keys
rcache_del($conn, 'topic-001');
```

`rcache` uses the MIT license <http://noodlehaus.mit-license.org>
