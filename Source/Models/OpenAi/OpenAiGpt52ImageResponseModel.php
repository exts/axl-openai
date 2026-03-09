<?php
namespace AxlCore\Models\OpenAi;

use AxlCore\Contracts\ContentInterface;
use AxlCore\Conversation\Content\ContentType;
use AxlCore\Models\AbstractModel;
use AxlCore\Models\ModelType;
use AxlCore\Providers\OpenAi\OpenAiEndpoints;
use AxlCore\Response\Objects\OpenAi\OpenAiResponsesImageResponseObject;
use AxlCore\Response\ResponseType;

class OpenAiGpt52ImageResponseModel extends AbstractModel
{
    const string MODEL_NAME = 'gpt-5.2';

    #[\Override]
    public function initialize() : void
    {
        $this->modelType(ModelType::CHAT);
        $this->setEndpoint(OpenAiEndpoints::Responses);
        $this->messagesKey('input');
        $this->registerResponseTypesOrThrow([
            ResponseType::Image,
        ]);

        $this->addAcceptedOptions([
            'reasoning',
        ]);

        $this->unsetAcceptedOptions([
            'temperature',
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
            'image_url' => sprintf('data:%s;base64,%s', $content->data(),
                $content->metadata('mimetype', default: 'image/jpeg')),
        ]);

        $this->registerContentTypeFormatter(ContentType::audio, fn(ContentInterface $content) => [
            'type' => 'input_audio',
            'input_audio' => [
                'data' => $content->data(),
                'format' => $content->metadata('format', default: 'wav'),
            ],
        ]);

        // Register role mappings
        $this->registerRoleMappings([
            'user' => 'user',
            'system' => 'system',
        ]);

        $this->registerResponseTypeObjectFormatter(
            ResponseType::Image, fn($raw) => new OpenAiResponsesImageResponseObject($raw));
    }
}