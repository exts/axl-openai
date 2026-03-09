<?php
namespace AxlCore\Models\OpenAi;

use AxlCore\Conversation\Content\ContentType;
use AxlCore\Models\AbstractModel;
use AxlCore\Models\ModelType;
use AxlCore\Providers\OpenAi\OpenAiEndpoints;
use AxlCore\Response\Objects\OpenAi\OpenAiCompletionsImageResponseObject;
use AxlCore\Response\Objects\OpenAi\OpenAiCompletionsTextResponseObject;
use AxlCore\Response\ResponseType;

class OpenAiGpt4MiniChatModel extends AbstractModel
{
    const string MODEL_NAME = 'gpt-4.1-mini';

    #[\Override]
    public function initialize() : void
    {
        $this->modelType(ModelType::CHAT);
        $this->setEndpoint(OpenAiEndpoints::Chat);
        $this->registerResponseTypesOrThrow([
            ResponseType::Text,
        ]);
        $this->addAcceptedOptions([
            'n',
            'max_completion_tokens',
        ]);
        $this->addMappedOptions([
            // token cap -> chat key
            'maxTokens' => 'max_completion_tokens',
            'max_tokens' => 'max_completion_tokens',
            'maxOutputTokens' => 'max_completion_tokens',
            'max_output_tokens' => 'max_completion_tokens',

            // multi-output -> chat only
            'num' => 'n',
            'choices' => 'n',
        ]);

        // Register content block templates inline
        $this->registerContentTypeFormatter(ContentType::text, fn($content) => [
            'type' => 'text',
            'text' => $content->data(),
        ]);

        $this->registerContentTypeFormatter(ContentType::imageUrl, fn($content) => [
            'type' => 'image_url',
            'image_url' => [
                'url' => $content->data(),
            ],
        ]);

        $this->registerContentTypeFormatter(ContentType::imageToBase64, fn($content) => [
            'type' => 'image_url',
            'image_url' => [
                'url' => sprintf('data:%s;base64,%s', $content->data(),
                    $content->metadata('mimetype', default: 'image/jpeg')),
            ],
        ]);

        $this->registerContentTypeFormatter(ContentType::documentToBase64, fn($content) => [
            'type' => 'file',
            'file' => [
                'filename' => $content->metadata('filename'),
                'file_data' => sprintf('data:%s;base64,%s', 'test' ?? $content->data(),
                    $content->metadata('mimetype', default: 'application/pdf')),
            ],
        ]);

        $this->registerContentTypeFormatter(ContentType::audio, fn($content) => [
            'type' => 'input_audio',
            'input_audio' => [
                'data' => $content->data(),
                'format' => 'wav',
            ],
        ]);

        // Register role mappings
        $this->registerRoleMappings([
            'user' => 'user',
            'system' => 'system',
        ]);

        $this->registerResponseTypeObjectFormatter(
            ResponseType::Text, fn($raw) => new OpenAiCompletionsTextResponseObject($raw));
        $this->registerResponseTypeObjectFormatter(
            ResponseType::Image, fn($raw) => new OpenAiCompletionsImageResponseObject($raw));

    }
}