<?php
namespace AxlCore\Response\Objects\OpenAi;

use AxlCore\Response\Objects\ImageResponseObject;

class OpenAiCompletionsImageResponseObject extends ImageResponseObject
{
    public function url() : string
    {
        throw new \Exception("to implement");
    }

    public function base64() : string
    {
        throw new \Exception("to implement");
    }
}