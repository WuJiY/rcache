<?php
/**
 * simple caching functions backed by redis
 *
 * @author Jesus A. Domingo <jesus.domingo@gmail.com>
 * @license MIT
 */

// creates connection
function rcache_init($host = 'localhost', $port = 6379) {
  $conn = new Redis();
  $conn->pconnect($host, $port);
  return $conn;
}

// get a value
function rcache_get($conn, $key) {

  $type = $conn->type($key);

  if (!$type)
    return null;

  // we have a set, so return all values for each member
  if ($type == Redis::REDIS_SET) {
    $values = $conn->mget($conn->smembers($key));
    return array_map(function ($row) {
      return json_decode($row, true);
    }, $values);
  }

  // just a string
  return json_decode($conn->get($key));
}

// stores a key into the cache
function rcache_set($conn, $keys, $cval, $ttl = 0) {

  $keys = trim($keys, ':');
  $conn = rcache_init();

  // serialize then store
  $cval = json_encode($cval);
  $conn = rcache_init();

  // it's a set, check for ttl and use set or setex
  if ($ttl > 0)
    $conn->setex($keys, $ttl, $cval);
  else
    $conn->set($keys, $cval);

  // offsets for the groups
  $slen = strlen($keys);
  $cidx = strpos($keys, ':');

  // just a plain key
  if ($cidx === false)
    return;

  // add all sub groups necessary
  while ($cidx < $slen) {
    $nidx = strpos($keys, ':', $cidx + 1);
    $nidx = ($nidx === false ? $slen : $nidx);
    $curr = substr($keys, 0, $cidx);
    $next = substr($keys, 0, $nidx);
    $conn->sadd($curr, $next);
    $cidx = $nidx;
  }
}

// invalidates a key or keys from the cache
function rcache_del() {

  // coerce, assume array always
  $keys = func_get_args();
  $conn = array_shift($keys);

  // unset the key, and remove from groups if any
  foreach ($keys as $p) {
    $conn->del(array_merge(
      (array) $p,
      $conn->keys("{$p}:*")
    ));
  }
}
