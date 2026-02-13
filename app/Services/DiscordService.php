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

        // Use Register Hook or Fallback
        $webhookUrl = $settings['discord_webhook_register'] ?? ($settings['discord_webhook_url'] ?? null);
        if (!$webhookUrl)
            return;

        $lang = $settings['discord_active_language'] ?? 'en';
        $templateKey = 'discord_template_register_' . $lang;

        $msg = $settings[$templateKey] ?? '';

        if (empty($msg)) {
            // Fallback to default
            $msg = "🎉 **New Member Joined!**\n\nPassionate Astrophotographer **{USER_NAME}** has just joined SmartScope! 🔭";
        }

        $msg = str_replace('{USER_NAME}', $user->name, $msg);

        $this->send($msg, null, $webhookUrl);
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

        // Use Upload Hook or Fallback
        $webhookUrl = $settings['discord_webhook_upload'] ?? ($settings['discord_webhook_url'] ?? null);
        if (!$webhookUrl)
            return;

        $user = $image->user;
        $targetId = $image->object_id ?: $image->id;
        $url = route('objects.show', $targetId);

        $lang = $settings['discord_active_language'] ?? 'en';
        $templateKey = 'discord_template_upload_' . $lang;

        $msg = $settings[$templateKey] ?? '';

        if (empty($msg)) {
            // Fallback
            $msg = "📸 **New Image Uploaded!**\n\n**{IMAGE_TITLE}** by **{USER_NAME}**\n\n{IMAGE_URL}";
        }

        $msg = str_replace(
        ['{USER_NAME}', '{IMAGE_TITLE}', '{IMAGE_URL}'],
        [$user->name, $image->title, $url],
            $msg
        );

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

        $this->send($msg, $embeds, $webhookUrl);
    }

    /**
     * Send a message to the configured Discord Webhook.
     *
     * @param string $content The text content of the message.
     * @param array|null $embeds Optional array of embeds.
     * @param string|null $webhookUrl Specific webhook URL.
     * @return void
     */
    public function send(string $content, array $embeds = null, string $webhookUrl = null)
    {
        $settings = Setting::all()->pluck('value', 'key');

        if (empty($settings['discord_enabled'])) {
            return;
        }

        // If no specific URL provided, try legacy/default
        if (!$webhookUrl) {
            $webhookUrl = $settings['discord_webhook_url'] ?? null;
        }

        if (!$webhookUrl) {
            return;
        }

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
