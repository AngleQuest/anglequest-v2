<?php

namespace App\Services;

use LiveKit\AccessToken;
use Agence104\LiveKit\VideoGrant;

class LiveKitService
{
    protected $apiKey;
    protected $apiSecret;
    protected $livekitUrl;

    public function __construct()
    {
        $this->apiKey = env('LIVEKIT_API_KEY');
        $this->apiSecret = env('LIVEKIT_API_SECRET');
        $this->livekitUrl = env('LIVEKIT_URL');
    }

    public function generateToken($roomName, $userIdentity, $isModerator = false)
    {
        $grant = new VideoGrant();
        $grant->setRoomJoin(true);
        $grant->setRoom($roomName);
        $grant->setCanPublish(true); // Allow video/audio publishing
        $grant->setCanSubscribe(true);
        $grant->setCanPublishData(true);

        if ($isModerator) {
            $grant->setCanPublishSources(["screen_share"]); // Enable screen sharing
        }

        $token = new AccessToken($this->apiKey, $this->apiSecret);
        $token->setIdentity($userIdentity);
        $token->setGrant($grant);

        return $token->toJwt();
    }

    public function getRoomUrl($roomName, $userIdentity)
    {
        $token = $this->generateToken($roomName, $userIdentity, true);
        return "{$this->livekitUrl}/?token={$token}";
    }
}
