<?php
namespace AxlCore\Response\Objects\OpenAi;

use AxlCore\Response\Objects\TextResponseObject;

class OpenAiCompletionsTextResponseObject extends TextResponseObject
{
    public function text() : ?string
    {
        return $this->rawJsonArray()['choices'][0]['message']['content'] ?? null;
    }
}