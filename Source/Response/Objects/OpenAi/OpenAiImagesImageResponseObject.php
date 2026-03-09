<?php
namespace AxlCore\Response\Objects\OpenAi;

use AxlCore\Response\Objects\ImageResponseObject;

class OpenAiImagesImageResponseObject extends ImageResponseObject
{
    public function url() : string
    {
        throw new \Exception("to implement");
    }

    public function base64() : string
    {
        return $this->rawJsonArray()['data'][0]['b64_json']
            ?? throw new \Exception("Base64 field not set in response when parsed");
    }
}