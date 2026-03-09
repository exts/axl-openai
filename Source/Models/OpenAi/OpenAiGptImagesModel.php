<?php
namespace AxlCore\Models\OpenAi;

use AxlCore\Contracts\ContentInterface;
use AxlCore\Conversation\Content\ContentType;
use AxlCore\Models\AbstractChatModel;
use AxlCore\Models\ModelType;
use AxlCore\Providers\OpenAi\OpenAiEndpoints;
use AxlCore\Response\Objects\OpenAi\OpenAiImagesImageResponseObject;
use AxlCore\Response\ResponseType;

class OpenAiGptImagesModel extends AbstractChatModel
{
    const string MODEL_NAME = 'gpt-image-1.5';

    #[\Override]
    public function initialize() : void
    {
        $this->modelType(ModelType::CHAT);
        $this->setEndpoint(OpenAiEndpoints::Images);
        $this->messagesKey('prompt');
        $this->enableFlattenPrompt();
        $this->registerResponseTypesOrThrow([
            ResponseType::Image,
        ]);
        $this->addAcceptedOptions([
            'size', 'n', 'background'
        ]);

        // Register content block templates inline
//        $this->registerContentTypeFormatter(ContentType::text, fn($content) => [
//            'type' => 'input_text',
//            'text' => $content->data(),
//        ]);
        $this->registerContentFlattenedItem(ContentType::text, fn($content) => $content->data());

        $this->registerContentFlattenedItem(ContentType::imageToBase64,
            function(ContentInterface $content) {
                return sprintf("Filename: %s\nFile Data: %s",
                    $content->metadata('filename', default: 'unknown filename'),
                    sprintf('data:%s;base64,%s',
                        $content->metadata('mimetype', default: 'image/jpeg'),
                        $content->data(),
                    ),
                );
            }
        );

        $this->registerContentFlattenedItem(ContentType::documentToBase64,
            function(ContentInterface $content) {
                return sprintf("Filename: %s\nFile Data: %s",
                    $content->metadata('filename', default: 'unknown filename'),
                    sprintf('data:%s;base64,%s',
                        $content->metadata('mimetype', default: 'application/pdf'),
                        $content->data(),
                    ),
                );
            }
        );

        // Register role mappings
        $this->registerRoleMappings([
            'user' => 'user',
            'system' => 'system',
        ]);

        $this->registerResponseTypeObjectFormatter(
            ResponseType::Image, fn($raw) => new OpenAiImagesImageResponseObject($raw));
    }
}