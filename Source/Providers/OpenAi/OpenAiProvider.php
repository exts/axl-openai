<?php
namespace AxlCore\Providers\OpenAi;

use AxlCore\Models\OpenAi\OpenAiGpt4MiniChatModel;
use AxlCore\Models\OpenAi\OpenAiGpt4MiniResponseModel;
use AxlCore\Models\OpenAi\OpenAiGpt51CodexMiniResponseModel;
use AxlCore\Models\OpenAi\OpenAiGpt52ImageResponseModel;
use AxlCore\Models\OpenAi\OpenAiGptImagesModel;
use AxlCore\Providers\AbstractProvider;
use AxlCore\Providers\ProviderEndpoint;

class OpenAiProvider extends AbstractProvider
{
    public ?string $api_url = 'https://api.openai.com';

    public function initialize(): void
    {
        // register models
        $this->register('gpt4_chat', fn($m, $a) => new OpenAiGpt4MiniChatModel($m, $a));
        $this->register('gpt4_responses', fn($m, $a) => new OpenAiGpt4MiniResponseModel($m, $a));
        $this->register('gpt_images', fn($m, $a) => new OpenAiGptImagesModel($m, $a));
        $this->register('gpt52_images', fn($m, $a) => new OpenAiGpt52ImageResponseModel($m, $a));
        $this->register('gpt51_responses', fn($m, $a) => new OpenAiGpt51CodexMiniResponseModel($m, $a));

        // register accepted enpoints
        $this->registerEndpointsOrThrow([
            new ProviderEndpoint(
                OpenAiEndpoints::Chat,
                '/v1/chat/completions',
            ),
            new ProviderEndpoint(
                OpenAiEndpoints::Responses,
                '/v1/responses',
            ),
            new ProviderEndpoint(
                OpenAiEndpoints::Images,
                '/v1/images/generations',
            ),
        ]);

        // register accepted setOptions
        $this->acceptedOptions([
            'stream',
            'tools',
            'temperature',
            'top_p',
            'stop',
            'presence_penalty',
            'frequency_penalty',
        ]);

        // register mapped (alias) setOptions
        $this->mappedOptions([
            // temperature
            'temp' => 'temperature',
            'Temperature' => 'temperature',

            // top_p
            'topP' => 'top_p',
            'topp' => 'top_p',

            // stop
            'stopSequences' => 'stop',
            'stop_sequences' => 'stop',

            // penalties
            'presencePenalty' => 'presence_penalty',
            'frequencyPenalty' => 'frequency_penalty',
        ]);
    }

    public function authHeaders(?string $key = null): array
    {
        $key = $key ?? $this->getApiKey();
        return [
            'Authorization' => "Bearer $key",
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    public function registerErrorParser(array $response_data): ?array
    {
        $data = [];
        if(isset($response_data['error'])) {
            $data['type'] = $response_data['error']['type'] ?? 'unknown error type';
            $data['message'] = $response_data['error']['message'] ?? 'unknown error';
        }
        return empty($data) ? null : $data;
    }
}