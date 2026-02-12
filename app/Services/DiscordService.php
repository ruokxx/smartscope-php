<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordService
{
    /**
     * Send a notification for a new user registration.
     * 
     * @param \App\Models\User $user
     * @return void
     */
    public function sendRegistration($user)
    {
        $settings = Setting::all()->pluck('value', 'key');

        if (empty($settings['discord_notify_register'])) {
            return;
        }

        $msg = "🎉 **New Member Joined!**\n\nPassionate Astrophotographer **{$user->name}** has just joined SmartScope! 🔭";
        $this->send($msg);
    }

    /**
     * Send a notification for a new image upload.
     * 
     * @param \App\Models\Image $image
     * @return void
     */
    public function sendUpload($image)
    {
        $settings = Setting::all()->pluck('value', 'key');

        if (empty($settings['discord_notify_upload'])) {
            return;
        }

        $user = $image->user;

        // Use object_id for the URL if available, as that's where the gallery is.
        $targetId = $image->object_id ?: $image->id;
        $url = route('objects.show', $targetId);

        $msg = "📸 **New Image Uploaded!**\n\n**{$image->title}** by **{$user->name}**\n\n" . $url;

        // Add embed with thumbnail if possible
        $embeds = [
            [
                'title' => $image->title,
                'url' => $url,
                'color' => 5814783, // #5865F2 (Discord Blurple)
                'image' => [
                    'url' => $image->url // using the accessor we made earlier
                ],
                'author' => [
                    'name' => $user->name,
                    'icon_url' => $user->avatar_url
                ],
                'footer' => [
                    'text' => 'SmartScope Gallery'
                ]
            ]
        ];

        $this->send($msg, $embeds);
    }

    /**
     * Send a message to the configured Discord Webhook.
     *
     * @param string $content The text content of the message.
     * @param array|null $embeds Optional array of embeds.
     * @return void
     */
    public function send(string $content, array $embeds = null)
    {
        // 1. Fetch settings
        $settings = Setting::all()->pluck('value', 'key');

        // 2. Check if enabled and URL exists
        if (empty($settings['discord_enabled']) || empty($settings['discord_webhook_url'])) {
            return;
        }

        $webhookUrl = $settings['discord_webhook_url'];

        // 3. Build payload
        $payload = [
            'content' => $content,
        ];

        if ($embeds) {
            $payload['embeds'] = $embeds;
        }

        // 4. Send request
        try {
            // Discord expects JSON
            $response = Http::post($webhookUrl, $payload);

            if ($response->failed()) {
                Log::error('Discord Webhook failed: ' . $response->body());
            }
        }
        catch (\Exception $e) {
            Log::error('Discord Webhook exception: ' . $e->getMessage());
        }
    }
}
