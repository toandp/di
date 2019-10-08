Apex/DI
======

Apex/DI is a small Dependency Injection Container for PHP.

Installation
------------

Before using Apex/DI in your project, add it to your ``composer.json`` file:

.. code-block:: bash

    $ ./composer.phar require toandp/di "^1.0"

Usage
-----

Creating a container is a matter of creating a ``Container`` instance:

.. code-block:: php

    use Apex\Di\Container;

    $container = new Container();