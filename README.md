# ShrinkPress: Break WordPress Apart

*A refactoring experiment by KT*

**Decompose WordPress to all its building blocks and put it up again as a modern and fast PHP7 project.**

My goal is to see how fast WordPress will become if it follows a better code
structure that resembles more a PHP7 project other than a PHP4 one converted
to PHP5 (as it is now).

## Roadmap
Here's what I want to do:

* no more includes, use autoloading (better with composer)
* no more loose functions, move them to static methods
* organise wp-includes into separate libs/packages
* extract packages from WP to allow to use them as separate libraries
* no more globals, use registry (or singleton)
* no more loose bundled 3rd-party libs, use composer and move them out
* no more default filters, load them only when they are needed
* take deprecated functions out into a "migration" plugin
* take non-essential core features and move them to plugins
* take out xmlrpc to a plugin
* take out wp-emojis to a plugin

The result must be compatible with existing plugins and themes, although with
some assistance via some sort of a migration tool.

Use [wp-dev tests|https://github.com/WordPress/wordpress-develop/tree/master/tests/phpunit] to validate the result as well.

## Bundled Packages in WordPress  
Let's try and list libraries bundled inside WordPress:

* **Requests** https://github.com/rmccue/Requests
