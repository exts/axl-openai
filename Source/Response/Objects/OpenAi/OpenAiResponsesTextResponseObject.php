<?php
namespace AxlCore\Response\Objects\OpenAi;

use AxlCore\Response\Objects\TextResponseObject;

class OpenAiResponsesTextResponseObject extends TextResponseObject
{
    public function text() : ?string
    {
        return $this->rawJsonArray()['output'][0]['content'][0]['text'] ?? null;
    }
}