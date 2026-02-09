<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Changelog;
use Carbon\Carbon;

class ChangelogSeeder extends Seeder
{
    public function run()
    {
        Changelog::create([
            'title' => 'Community & Messaging Update',
            'version' => 'v1.2.0',
            'published_at' => Carbon::now(),
            'body' => "
### 🚀 New Features
- **Private Messaging**: You can now send private messages to other users! Check the 'Inbox' in your profile or use the 'Send Message' button on user profiles.
- **Unread Notifications**: A blinking envelope icon now alerts you to new messages.
- **Chat Interface**: Improved chat bubble design for better readability.

### 🐛 Bug Fixes
- **Profile Page**: Fixed a crash that occurred when editing profiles.
- **Image Uploads**: Fixed an issue where large image uploads in the Community Feed would cause a redirect to the login page. Added a 1.8MB file size limit check.
            "
        ]);
    }
}
