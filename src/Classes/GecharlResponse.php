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

    /**
     * @var bool
     */
    private $hasError;

    /**
     * @var string $title
     */
    private $title;

    /**
     * Response Message as determined by status code
     * @var string $message
     */
    private $message;

    /**
     * Response Body from
     * @var object|null $body
     */
    private $body;

    /**
     * @var array $additionalStatusDetails
     */
    private $additionalStatus;

    /**
     * GecharlResponse constructor.
     * @param string $code
     * @param object|array|null $responseBody
     * @throws GecharlErrorException
     */
    public function __construct(string $code, $responseBody = null)
    {
        $this->body = $responseBody;
        $this->additionalStatus = [];
        $this->title = "Empty Response";
        $this->message = "Empty Response from VTPass server";
        $this->hasError = false;

        if (isset(GecharlResponse::RESPONSE["$code"])) {
            $msg = GecharlResponse::RESPONSE["$code"];
            $this->title = $msg['title'];
            $this->message = $msg['message'];
            $this->hasError = $msg['error'];
            if ("$code" === "000" && isset($responseBody->status)) {
                if (isset(GecharlResponse::TRANSACTION_PROCESSED_STATUS["{$responseBody->status}"])) {
                    $this->additionalStatus = GecharlResponse::TRANSACTION_PROCESSED_STATUS["{$responseBody->status}"];
                }
            }
        }

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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
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
