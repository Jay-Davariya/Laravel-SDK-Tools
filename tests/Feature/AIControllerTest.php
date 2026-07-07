<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\ModelType;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class AIControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function test_standardize_uses_gemini_first(): void
    {
        $this->withoutMiddleware();
        config([
            'gemini.api_key' => 'gemini_test_key'
        ]);

        $fakeResponse = GenerateContentResponse::from([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => '<p>Gemini Standardized</p>',
                            ],
                        ],
                        'role' => 'model',
                    ],
                ],
            ],
            'usageMetadata' => [
                'promptTokenCount' => 8,
                'candidatesTokenCount' => 444,
                'totalTokenCount' => 452,
            ],
        ]);

        Gemini::fake([$fakeResponse]);

        $response = $this->postJson('/ai/standardize', [
            'html' => '<p>Some Content</p>'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'standardized' => '<p>Gemini Standardized</p>'
        ]);
    }

    public function test_standardize_uses_openai_second(): void
    {
        $this->withoutMiddleware();
        config([
            'gemini.api_key' => null,
            'openai.api_key' => 'openai_test_key'
        ]);

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '<p>OpenAI Standardized</p>',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson('/ai/standardize', [
            'html' => '<p>Some Content</p>'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'standardized' => '<p>OpenAI Standardized</p>'
        ]);
    }

    public function test_standardize_uses_groq_third(): void
    {
        $this->withoutMiddleware();
        config([
            'gemini.api_key' => null,
            'openai.api_key' => null,
            'groq.api_key' => 'gsk_test'
        ]);

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

    public function test_standardize_falls_back_to_openai_if_gemini_fails_at_runtime(): void
    {
        $this->withoutMiddleware();
        config([
            'gemini.api_key' => 'gemini_test_key',
            'openai.api_key' => 'openai_test_key'
        ]);

        // Mock Gemini to throw an exception at runtime
        Gemini::fake([new \Exception("Quota exceeded for metric")]);

        // Fake OpenAI's successful response
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '<p>OpenAI Standardized Fallback</p>',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson('/ai/standardize', [
            'html' => '<p>Some Content</p>'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'standardized' => '<p>OpenAI Standardized Fallback</p>'
        ]);
    }

    public function test_standardize_chunks_large_document_and_joins_them(): void
    {
        $this->withoutMiddleware();
        config([
            'gemini.api_key' => null,
            'openai.api_key' => 'openai_test_key'
        ]);

        // Generate a large HTML document of > 15,000 characters
        $largeHtml = "<h2>Section One</h2>\n" . str_repeat("<p>Paragraph content</p>\n", 300) .
                     "<h2>Section Two</h2>\n" . str_repeat("<p>Paragraph content</p>\n", 300);

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '<div id="section-one" class="page-section"><h2>Section One</h2><p>Paragraph content</p></div>',
                        ],
                    ],
                ],
            ]),
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => '<div id="section-two" class="page-section"><h2>Section Two</h2><p>Paragraph content</p></div>',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson('/ai/standardize', [
            'html' => $largeHtml
        ]);

        $response->assertStatus(200);
        $standardized = $response->json('standardized');

        // It should contain both sections
        $this->assertStringContainsString('id="section-one"', $standardized);
        $this->assertStringContainsString('id="section-two"', $standardized);

        // It should append a unified timeline at the end programmatically
        $this->assertStringContainsString('<ul class="sidebar-blog-timeline">', $standardized);
        $this->assertStringContainsString('<li class="blog-timeline-link"><a href="#section-one">Section One</a></li>', $standardized);
        $this->assertStringContainsString('<li class="blog-timeline-link"><a href="#section-two">Section Two</a></li>', $standardized);
    }
}

