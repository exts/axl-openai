<?php
namespace AxlCore\Models\OpenAi;

use AxlCore\Contracts\ContentInterface;
use AxlCore\Conversation\Content\ContentType;
use AxlCore\Models\AbstractModel;
use AxlCore\Models\ModelType;
use AxlCore\Providers\OpenAi\OpenAiEndpoints;
use AxlCore\Response\Objects\OpenAi\OpenAiCompletionsTextResponseObject;
use AxlCore\Response\Objects\OpenAi\OpenAiResponsesTextResponseObject;
use AxlCore\Response\Objects\OpenAi\OpenAiResponsesImageResponseObject;
use AxlCore\Response\ResponseType;

class OpenAiGpt4MiniResponseModel extends AbstractModel
{
    const string MODEL_NAME = 'gpt-4.1-mini';

    #[\Override]
    public function initialize() : void
    {
        $this->modelType(ModelType::CHAT);
        $this->setEndpoint(OpenAiEndpoints::Responses);
        $this->messagesKey('input');
        $this->registerResponseTypesOrThrow([
            ResponseType::Text,
            ResponseType::Image,
        ]);
        $this->addAcceptedOptions([
            'reasoning',
        ]);
        $this->addMappedOptions([
            // token cap -> chat key
            'maxTokens' => 'completion_tokens',
            'max_tokens' => 'completion_tokens',
            'maxOutputTokens' => 'completion_tokens',
            'max_output_tokens' => 'completion_tokens',
        ]);

        // Register content block templates inline
        $this->registerContentTypeFormatter(ContentType::text, fn($content) => [
            'type' => 'input_text',
            'text' => $content->data(),
        ]);

        $this->registerContentTypeFormatter(ContentType::imageUrl, fn($content) => [
            'type' => 'input_image',
            'image_url' => $content->data(),
        ]);

        $this->registerContentTypeFormatter(ContentType::imageToBase64, fn($content) => [
            'type' => 'input_image',
            'image_url' => sprintf('data:%s;base64,%s',
                $content->metadata('mimetype', default: 'image/jpeg'),
                $content->data(),
            ),
        ]);

        $this->registerContentTypeFormatter(ContentType::documentToBase64, fn($content) => [
            'type' => 'input_file',
            'filename' => $content->metadata('filename'),
            'file_data' => sprintf('data:%s;base64,%s',
                $content->metadata('mimetype', default: 'application/pdf'),
                $content->data(),
            ),
        ]);

//        $this->registerContentTypeFormatter(ContentType::audio, fn(ContentInterface $content) => [
//            'type' => 'input_audio',
//            'input_audio' => [
//                'data' => $content->data(),
//                'format' => $content->metadata('format', default: 'wav'),
//            ],
//        ]);

        $this->registerStreamParser(function($raw) {
            return match($raw['type']) {
                'response.output_text.delta' => $raw['delta'] ?? null,
                'response.created' => "Created Response\n",
                'response.completed' => "\nCompleted Response\n",
                default => '',
            };
        });


        // Register role mappings
        $this->registerRoleMappings([
            'user' => 'user',
            'system' => 'system',
        ]);

        $this->registerResponseTypeObjectFormatter(
            ResponseType::Text, fn($raw) => new OpenAiResponsesTextResponseObject($raw));
        $this->registerResponseTypeObjectFormatter(
            ResponseType::Image, fn($raw) => new OpenAiResponsesImageResponseObject($raw));
    }
}