<?php

namespace Cyclops1101\PageObjectManager\Exceptions;

use Exception;

class TemplateNotFoundException extends Exception
{

    /**
     * Define the Exception
     *
     * @param string $template
     * @param string $name
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($template = null, $name = null, $code = 0, Exception $previous = null) {
        $message = 'PageObjectManager Template';

        if($template) {
            $message .= ' "' . $template . '"';
        }

        if($name) {
            $message .= ' for "' . $name . '"';
        }

        $message .= ' not found.';

        parent::__construct($message, $code, $previous);
    }

}
