# ShrinkPress TODO

* use PhpParser: https://github.com/nikic/PHP-Parser/blob/master/doc/2_Usage_of_basic_components.markdown#node-traversation

* clone from this WordPress repo: https://github.com/WordPress/WordPress/tree/5.4-branch

* try the shrinking on all releases since 4.9

* function stats: 2412 functions! (without wp-admin), 2834 functions! (total)

* remove "wp_" prefix, when in namespaces there is little chance of name clashes

* WordPress' own component organisation: https://make.wordpress.org/core/components/

* Load default-filters only when they are needed, not always; perhaps add a "DefaultFilters" class in each package ?

* write manifest/roadmap with the goals you want to meet with this project

* create list of bundled libs/packages and their versions in each WP release

* WTF?! there are require/require_once from inside functions, like require_wp_db()

* Phase 2 thing: cook the doccomment with the new stuff, so that the comments reflect the new changed code, e.g. instead of `@globalvar $wpdb"` make it `@see ShrinkPress/DB/Last::$db`

* look for performance-related ideas: https://petitions.classicpress.net/?view=most-wanted

* Autoloading WordPress:
	https://core.trac.wordpress.org/ticket/36335
	https://github.com/mikeschinkel/autoloading-wordpress

* https://composer.rarst.net/
* https://github.com/Modius22/FreshPress/tree/master

## Types of PHP files in WordPress

Different files have different roles, and then need to be parsed and unparsed
differently. There are files with similar characteristics, which can be treated
the same, and then there are special files, one of their kind, that need special
treatment.

Being backwards-compatible WordPress does not seem to delete old files, so a
file-based process would seem to work on different versions of the project.

* wp-admin/ controllers: pages loaded in the admin panel;
* external classes
* wp-includes/default-filters.php where filters are added in bulk
* wp-includes/pluggable.php with fallback function definitions
* wp-includes/pluggable-deprecated.php: fallback function definitions that are deprecated
* wp-includes/blocks/ block definitions
* wp-includes/compat.php: polyfills for different PHP versions
* wp-includes/widgets/ widget definitions

## Shrinking: Classes

- see https://getcomposer.org/doc/04-schema.md#classmap

- put all of the classes in a folder and let composer scan for them when "dumpautoload"-ing; in this way any reference to that class will be served by composer autoload_classmap.php map.

- perhaps use multiple folders, so that still the classes are organised by their purpose;

- explore if it is a good idea to rename the old classes under the new namespace composer packages schema, and then just use class_alias() as compatibility for the old classes.

## Shrinking: Functions  

1. find all functions, plus

	+ files in which they are defined
	+ starting and ending line
	+ what other functions they are calling (exclude internal)
	+ where are these functions called from (file, line, caller)
	+ doccomment of the function

2. create the shrink map by assigning:

	* namespace (package)
	* className (+ classFile)
	* method

3. sort functions by number of other functions they call, ascending

	- in this way there will be no functions left behind, we are
	always going to shrink only functions that have 0 other
	not-yet-shrank functions called;

	- because of this add_action and do_filter and other popular
	functions will be shrank last

4. replace functions from sorted list

	- insert them to the new class (and file)
	- remove them from the original file
	- add them to the compatibility file
	- replace all occurrences with the new class method
