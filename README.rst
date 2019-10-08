PHP tdp/di
======

PHP tdp/di is a small Dependency Injection Container for PHP.

Installation
------------

Before using PHP tdp/di in your project, add it to your ``composer.json`` file:

.. code-block:: bash

    $ ./composer.phar require toandp/di "^1.0"

Usage
-----

Creating a container is a matter of creating a ``Container`` instance:

.. code-block:: php

    use tdp\di\Container;

    $container = new Container();