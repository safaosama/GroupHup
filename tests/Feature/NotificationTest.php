<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_notifications()
    {
        $user = User::factory()->create(['role' => 'student']);
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test message',
        ]);

        $response = $this->actingAs($user)->get('/student');

        $response->assertStatus(200);
        $response->assertSee('Test Notification');
        $response->assertSee('This is a test message');
    }

    public function test_user_can_mark_notification_as_read()
    {
        $user = User::factory()->create(['role' => 'student']);
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'This is a test message',
        ]);

        $this->assertNull($notification->fresh()->read_at);

        $response = $this->actingAs($user)->post("/notifications/{$notification->id}/mark-read");

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_cannot_mark_others_notifications_as_read()
    {
        $user1 = User::factory()->create(['role' => 'student']);
        $user2 = User::factory()->create(['role' => 'student']);
        $notification = Notification::create([
            'user_id' => $user2->id,
            'title' => 'Test Notification',
            'message' => 'This is a test message',
        ]);

        $response = $this->actingAs($user1)->post("/notifications/{$notification->id}/mark-read");

        $response->assertStatus(404);
    }
}
