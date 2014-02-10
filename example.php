<?php
require __DIR__."/rcache.php";

use noodlehaus\rcache;

// setup your redis conn
$conn = rcache\init('localhost', 6379);

// plain key
rcache\set($conn, 'topic-001-title', 'this is rc!');

// grouped keys, colon-separated path (under topic-001)
rcache\set($conn, 'topic-001:page-01', 'some text here');
rcache\set($conn, 'topic-001:page-02', 'some text here');

// get a value
$page_01 = rcache\get($conn, 'topic-001:page-01');

// get all values for group
$topic_001 = rcache\get($conn, 'topic-001');

// invalidate an entire group of keys
rcache\del($conn, 'topic-001');
