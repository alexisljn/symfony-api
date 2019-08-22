<?php


namespace App\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;

class UrlApi
{

    /**
     * @Assert\Url()
     */
    private $url;


    public function __construct($url)
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('Invalid url : '.$url);
        }
        $this->url = $url;
    }

    public function toString()
    {
        return $this->url;
    }

}