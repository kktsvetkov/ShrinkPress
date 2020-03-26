<?php
include __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler(function($severity, $message, $file, $line)
{
	throw new ErrorException($message, 0, $severity, $file, $line);
}, E_ALL);

/////////////////////////////////////////////////////////////////////////////

// $ext = get_loaded_extensions();
$ext = ["Core","standard","SPL","date","libxml","openssl","pcre","sqlite3",
"zlib","bcmath","bz2","calendar","ctype","curl","dba","dom","hash","fileinfo",
"filter","ftp","gd","iconv","json","ldap","mbstring","session","mysqlnd","PDO",
"pdo_mysql","pdo_pgsql","pdo_sqlite","pgsql","Phar","posix","readline",
"Reflection","mysqli","shmop","SimpleXML","snmp","soap","sockets","exif",
"sysvmsg","sysvsem","sysvshm","tidy","tokenizer","wddx","xml","xmlreader",
"xmlrpc","xmlwriter","xsl","zip","xdebug",

"mysql", "mcrypt", "xdiff", "FPM", "OPcache", "Apache", "Ignore"];

$all = [];
foreach ($ext as $ex)
{
	$fx = get_extension_funcs($ex);
	if (!is_array($fx))
	{
		continue;
	}

	foreach ($fx as $f)
	{
		$all[ $f ] = $ex;
	}
}

$all['opcache_invalidate'] = 'OPcache';
$all['fastcgi_finish_request'] = 'FPM';

$all['apache_get_modules'] = 'Apache';
$all['mcrypt_create_iv'] = 'mcrypt';

$all['xdiff_string_diff'] = 'xdiff';

$all['mysql_set_charset'] = 'mysql';
$all['mysql_query'] = 'mysql';
$all['mysql_result'] = 'mysql';
$all['mysql_select_db'] = 'mysql';
$all['mysql_real_escape_string'] = 'mysql';
$all['mysql_error'] = 'mysql';
$all['mysql_free_result'] = 'mysql';
$all['mysql_connect'] = 'mysql';
$all['mysql_ping'] = 'mysql';
$all['mysql_errno'] = 'mysql';
$all['mysql_affected_rows'] = 'mysql';
$all['mysql_insert_id'] = 'mysql';
$all['mysql_fetch_object'] = 'mysql';
$all['mysql_client_encoding'] = 'mysql';
$all['mysql_num_fields'] = 'mysql';
$all['mysql_fetch_field'] = 'mysql';
$all['mysql_close'] = 'mysql';
$all['mysql_get_client_info'] = 'mysql';
$all['mysql_get_server_info'] = 'mysql';

$all['debug'] = 'Ignore';
$all['wp_cache_postload'] = 'Ignore';

$internal = ShrinkPress\Build\Find\Internal::internal;
$sorted = [];

foreach (array_keys($internal) as $f)
{
	$sorted[ $all[ $f ] ][] = $f;
}

$sorted['standard'][] = 'bin2hex';

$output = '';
$i = 1;
foreach ($ext as $ex)
{
	if (empty($sorted[ $ex ]))
	{
		continue;
	}
	asort($sorted[ $ex ]);

	$output .= "\n\n/* {$ex} extension */";
	foreach ($sorted[ $ex ] as $f)
	{
		$output .= "\n'{$f}' => {$i},";
		$i++;
	}
}

echo $output , "\n\n";
