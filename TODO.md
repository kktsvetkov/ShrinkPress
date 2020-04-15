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
* https://github.com/codepotent/update-manager

* is it possible to make plugins have composer.json dependencies ? and install them upon installing the plugins.

* create a shrinkpress_pudding plugin for reporting number of included files, declared classes and functions 

## Phase 1

* reduced, limited set of changes
* still do all of the main changes: functions, classes, globals
* no core plugin extraction
* classes are only moved to composer packages, class-map loaded, NOT renamed
* functions are moved as static methods to classes, NOT renamed
* globals are copied to static properties, with same name, NOT removed
* leave includes "as is", we are going to rely just on them being reduced
* exclude wp-admin/ !
* only wp-includes and root folder files
* no need for migration plugin

## Phase 2

* rename methods
* rename classes
* remove globals
* extract internal components as separate libs

## Phase 3

* extract core parts as plugins
* plugins dependencies
* alternative updates/downloads
* composer dependencies downloads

## Types of PHP files in WordPress

Different files have different roles, and then need to be parsed and unparsed
differently. There are files with similar characteristics, which can be treated
the same, and then there are special files, one of their kind, that need special
treatment.

Being backwards-compatible WordPress does not seem to delete old files, so a
file-based process would seem to work on different versions of the project.

* wp-admin/ controllers: pages loaded in the admin panel;
* external classes
* wp-includes/default-constants.php
* wp-includes/default-filters.php where filters are added in bulk
* wp-includes/pluggable.php with fallback function definitions
* wp-includes/pluggable-deprecated.php: fallback function definitions that are deprecated
* wp-includes/blocks/ block definitions
* wp-includes/compat.php: polyfills for different PHP versions
* wp-includes/widgets/ widget definitions
* deprecated file

## Types of operations

When starting to shrink, what type of operations will be needed

* define a package: create composer package inside the project for hosting moved classes, migrated functions into class methods, and migrated globals into static vars

* replace class: replace a class name with a new one for all original class occurrences; files will receive "use" statements for the class so that only the basic className is used inside the code

* move class: out of wp-includes or wp-admin/wp-includes, without renaming it, and put it under a package (library) WITHOUT using a new namespace;

* migrate class: like moving a class, but use a new className and put it under a namespace; afterwards replace all class occurrences in the code

* replace function: replace a function with a new static method in all direct call occurrences and all hook references.

* migrate function: replace a function with a static class method in all original function occurrences; files will receive "use" statements for the class hosting the static method, so that only the basic className is used along with the method in the code

* migrate global: replace a global var with a static class property in all original global var occurrences; files will receive "use" statements for the class hosting the static property to make it so that only the basic className is used inside the code

* drop include: after all the migration check the modified files to find if there are empty ones, and if there are, remove all include/include_once/require/require_once occurences for these files

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
