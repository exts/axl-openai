<?php
namespace AxlCore\Providers\OpenAi;

use AxlCore\Contracts\ProviderEndpointsInterface;

class OpenAiEndpoints implements ProviderEndpointsInterface
{
    public const string Chat = 'chat';
    public const string Images = 'images';
    public const string Responses = 'responses';
}