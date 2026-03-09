<?php
namespace AxlCore\Response\Objects\OpenAi;

use AxlCore\Response\Objects\ImageResponseObject;

class OpenAiResponsesImageResponseObject extends ImageResponseObject
{
    public function url() : string
    {
        throw new \Exception("not supported");
    }

    public function base64() : string
    {
        return $this->rawJsonArray()['output'][0]['result']
            ?? throw new \Exception("Base64 field not set in response when parsed");
    }
}