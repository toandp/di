<?php

namespace tdp\di;

use Exception;

class NotInstantiableException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function __construct($class, $message = null, $code = 0, Exception $previous = null)
    {
        if ($message === null) {
            $message = 'Can not instantiate ' . $class;
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Not instantiable';
    }
}
