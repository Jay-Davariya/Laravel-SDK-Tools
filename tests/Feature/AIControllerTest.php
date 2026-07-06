<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIControllerTest extends TestCase
{
    public function test_standardize_uses_correct_system_prompt_and_returns_html(): void
    {
        $this->withoutMiddleware();
        config(['groq.api_key' => 'gsk_test']);

        // Fake the Groq API call
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => '<div class="blog-highlight-bg-box"><h3>What This Guide Covers</h3><ol><li>Item one</li></ol></div>'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/ai/standardize', [
            'html' => 'What This Guide Covers\nItem one'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'standardized' => '<div class="blog-highlight-bg-box"><h3>What This Guide Covers</h3><ol><li>Item one</li></ol></div>'
        ]);

        // Assert the request was sent with our system prompt containing the new rules
        Http::assertSent(function ($request) {
            $systemPrompt = $request['messages'][0]['content'] ?? '';
            return str_contains($systemPrompt, 'blog-highlight-bg-box') &&
                   str_contains($systemPrompt, 'blog-highlight-p') &&
                   str_contains($systemPrompt, 'blog-highlight-with-left-border');
        });
    }

    public function test_writer_content_uses_correct_system_prompt(): void
    {
        $this->withoutMiddleware();
        config(['groq.api_key' => 'gsk_test']);

        // Fake the Groq API call
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    [
                        'delta' => [
                            'content' => 'Some content'
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Call generateContent with writer mode (starts streaming)
        $response = $this->postJson('/ai/content', [
            'prompt' => 'Write about PHP',
            'mode' => 'writer'
        ]);

        $response->assertStatus(200);

        // Capture/evaluate the stream
        ob_start();
        $response->sendContent();
        ob_end_clean();

        // Assert that the request contains the formatting rules in the system prompt
        Http::assertSent(function ($request) {
            $systemPrompt = $request['messages'][0]['content'] ?? '';
            return str_contains($systemPrompt, 'blog-highlight-bg-box') &&
                   str_contains($systemPrompt, 'blog-highlight-p') &&
                   str_contains($systemPrompt, 'blog-highlight-with-left-border');
        });
    }
}
