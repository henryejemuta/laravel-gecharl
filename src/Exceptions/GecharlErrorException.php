<?php
/**
 * Created By: Henry Ejemuta
 * PC: Enrico Systems
 * Project: laravel-gecharl
 * Company: Stimolive Technologies Limited
 * Class Name: GecharlErrorException.php
 * Date Created: 9/27/20
 * Time Created: 7:24 PM
 */

namespace Henryejemuta\LaravelGecharl\Exceptions;


class GecharlErrorException extends \Exception
{
    /**
     * GecharlErrorException constructor.
     * @param string $message
     * @param $code
     */
    public function __construct(string $message, $code)
    {
        parent::__construct($message, $code);
    }
}
