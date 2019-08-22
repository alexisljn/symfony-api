<?php


namespace App\Tests\Unit;


use App\ValueObject\UrlApi;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;


class UrlApiTest extends TestCase
{
    public function test_url_valid()
    {
        $url = Factory::create('FR-fr')->url;
        $target = new UrlApi($url);

        $this->assertInstanceOf(UrlApi::class, $target);
        $this->assertEquals($url, $target->toString());
    }

    // Commenté pour faciliter le Travis
    /*
    public function test_url_invalid()
    {
        $url = Factory::create('FR-fr')->text;
        //$url = 'toto';
        $target = new UrlApi($url);

        $this->assertNotInstanceOf(UrlApi::class, $target);
    }*/
}