<?php
/**
 * Created By: Henry Ejemuta
 * PC: Enrico Systems
 * Project: laravel-gecharl
 * Company: Stimolive Technologies Limited
 * Class Name: GecharlResponse.php
 * Date Created: 9/27/20
 * Time Created: 6:00 PM
 */

namespace HenryEjemuta\LaravelGecharl\Classes;


use HenryEjemuta\LaravelGecharl\Exceptions\GecharlErrorException;

class GecharlResponse
{
    private $message;

    /**
     * @var bool
     */
    private $hasError;

    /**
     * @var int
     */
    private $code;

    /**
     * Response Body from
     * @var object|null $body
     */
    private $body;

    const STATUS_MESSAGE = [
        "200" => '200 OK',
        "201" => '201 Created',
        "203" => '203 Non-Authoritative Information',
        "204" => '204 No Content',
        "301" => '301 Moved Permanently',
        "307" => '307 Temporary Redirect',
        "308" => '308 Permanent Redirect',
        "400" => '400 Bad Request',
        "401" => '401 Unauthorized',
        "402" => '402 Payment Required',
        "403" => '403 Forbidden',
        "404" => '404 Not Found',
        "408" => '408 Request Timeout',
        "413" => '413 Payload Too Large',
        "414" => '414 URI Too Long',
        "422" => '422 Unprocessable Entity',
        "500" => '500 Internal Server Error',
        "502" => '502 Bad Gateway',
        "503" => '503 Service Unavailable',
        "504" => '504 Gateway Timeout',
        "505" => '505 HTTP Version Not Supported',
    ];


    /**
     * GecharlResponse constructor.
     * @param int $code
     * @param object|array|null $responseBody
     * @throws GecharlErrorException
     */
    public function __construct(int $code, $responseBody = null)
    {
        $this->body = $responseBody;
        $this->hasError = ($code !== 200);
        $this->code = $code;
        $this->message = isset(self::STATUS_MESSAGE["{$this->code}"]) ? isset(self::STATUS_MESSAGE["{$this->code}"]) : 'Unable to determine response status.';

        if ($this->hasError)
            throw new GecharlErrorException($this->message, "$code");

    }

    /**
     * Determine if this ise a success response object
     * @return bool
     */
    public function successful(): bool
    {
        return !($this->hasError);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return object|array|null
     */
    public function getBody()
    {
        return $this->body;
    }

    public function __toString()
    {
        return json_encode($this->body);
    }

}
