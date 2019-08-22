<?php


namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();
        $client->request('GET', 'api/users', [], [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'HTTP_X-AUTH-TOKEN' => '183'
            ]
        );

        $response = $client->getResponse();
        //dd($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());

        $content = $response->getContent();
        $this->assertJson($content);

       // $arrayContent = \json_decode($content, true);
       // $this->assertCount(5, $arrayContent);
    }

    public function testPostUsers()
    {
        $client = static::createClient();
        $client->request('POST', '/api/users', [], [],
            [
                'HTTP_ACCEPT' => 'application/json' ,
                'CONTENT_TYPE' => 'application/json' ,
                'HTTP_X-AUTH-TOKEN' => '183'
            ],
            '{"apiKey": "adddqaaa","email": "testo@behat.com", "firstname": "Jean",
                      "lastname": "Baptiste", "password":"toto"}'
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);
    }
}