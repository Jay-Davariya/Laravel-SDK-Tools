<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Enums\ModelType;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    /**
     * Handle a streaming chat request.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'history' => 'nullable|array',
        ]);

        // Format history for OpenAI/Groq style
        $messages = [];
        if ($request->history) {
            foreach ($request->history as $msg) {
                $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }
        $messages[] = ['role' => 'user', 'content' => $request->prompt];

        // 1. Priority to Groq
        // if (config('groq.api_key')) {
        //     return response()->stream(function () use ($request, $messages) {
        //         try {
        //             $response = Http::withToken(config('groq.api_key'))
        //                 ->withOptions(['stream' => true])
        //                 ->withHeaders(['Accept' => 'text/event-stream'])
        //                 ->post(config('groq.base_url') . '/chat/completions', [
        //                     'model' => config('groq.default_model', 'llama-3.3-70b-versatile'),
        //                     'messages' => $messages,
        //                     'stream' => true,
        //                 ]);

        //             $stream = $response->toPsrResponse()->getBody();
        //             $buffer = '';

        //             while (!$stream->eof()) {
        //                 $chunk = $stream->read(1024);
        //                 $buffer .= $chunk;

        //                 while (($pos = strpos($buffer, "\n")) !== false) {
        //                     $line = substr($buffer, 0, $pos);
        //                     $buffer = substr($buffer, $pos + 1);
        //                     $line = trim($line);

        //                     if (str_starts_with($line, 'data: ')) {
        //                         $data = substr($line, 6);
        //                         if ($data === '[DONE]') break 2;
                                
        //                         $decoded = json_decode($data, true);
        //                         $text = $decoded['choices'][0]['delta']['content'] ?? '';
                                
        //                         if ($text !== '') {
        //                             echo "data: " . json_encode(['text' => $text]) . "\n\n";
        //                             ob_flush(); flush();
        //                         }
        //                     }
        //                 }
        //             }
        //         } catch (\Exception $e) {
        //             echo "data: " . json_encode(['text' => "Groq Error: " . $e->getMessage()]) . "\n\n";
        //         }
        //         echo "data: [DONE]\n\n";
        //         ob_flush(); flush();
        //     }, 200, [
        //         'Cache-Control' => 'no-cache',
        //         'Content-Type' => 'text/event-stream',
        //         'X-Accel-Buffering' => 'no',
        //     ]);
        // }

        // 2. Fallback to Gemini SDK (Using gemini-2.5-flash)
        if (config('gemini.api_key') && config('gemini.api_key') !== 'your_google_studio_key_here') {
            return response()->stream(function () use ($request) {
                try {
                    $model = Gemini::generativeModel('gemini-2.5-flash');
                    
                    $geminiHistory = [];
                    if (!empty($request->history)) {
                        foreach ($request->history as $msg) {
                            if (!empty($msg['content'])) {
                                $geminiHistory[] = \Gemini\Data\Content::parse(
                                    $msg['content'],
                                    $msg['role'] === 'assistant' ? \Gemini\Enums\Role::MODEL : \Gemini\Enums\Role::USER
                                );
                            }
                        }
                    }

                    $chat = $model->startChat(history: $geminiHistory);
                    $stream = $chat->streamSendMessage($request->prompt);

                    foreach ($stream as $response) {
                        try {
                            // Using the safe extraction method to prevent SDK crashes
                            if (isset($response->candidates[0]->content->parts[0]->text)) {
                                $text = $response->candidates[0]->content->parts[0]->text;
                                if ($text) {
                                    echo "data: " . json_encode(['text' => $text]) . "\n\n";
                                    ob_flush(); flush();
                                    \Log::info('Gemini SDK chunk: ' . $text);
                                }
                            }
                        } catch (\Exception $inner) {
                            \Log::warning('Gemini SDK chunk skipped: ' . $inner->getMessage());
                            continue;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Gemini SDK Critical Error: " . $e->getMessage());
                    echo "data: " . json_encode(['text' => "Gemini API Error: " . $e->getMessage()]) . "\n\n";
                }
                echo "data: [DONE]\n\n";
                ob_flush(); flush();
            }, 200, [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'text/event-stream',
                'X-Accel-Buffering' => 'no',
            ]);
        }

        // 3. Fallback to OpenAI
        return response()->stream(function () use ($request, $messages) {
            $stream = OpenAI::chat()->createStreamed([
                'model' => 'gpt-4o-mini',
                'messages' => $messages,
            ]);

            foreach ($stream as $response) {
                $text = $response->choices[0]->delta->content;
                if ($text) {
                    echo "data: " . json_encode(['text' => $text]) . "\n\n";
                    ob_flush();
                    flush();
                }
            }
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    /**
     * Handle image generation request.
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'style' => 'nullable|string',
            'size' => 'nullable|string|in:1024x1024,1024x1792,1792x1024',
        ]);

        try {
            // Enhance prompt based on style
            $prompt = $request->prompt;
            if ($request->style && $request->style !== 'None') {
                $prompt .= ", " . $request->style . " style";
            }
            
            // Add quality keywords
            $prompt .= ", 4k resolution, high quality, detailed";

            // Encode prompt for URL
            $encodedPrompt = urlencode($prompt);
            $width = 1024;
            $height = 1024;
            
            if ($request->size === '1024x1792') { $width = 1024; $height = 1792; }
            if ($request->size === '1792x1024') { $width = 1792; $height = 1024; }

            // Use Pollinations.ai for high-quality free generation
            $imageUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width={$width}&height={$height}&nologo=true&seed=" . rand(1, 100000);

            return response()->json([
                'url' => $imageUrl,
                'revised_prompt' => $prompt
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle image conversion (PNG to WebP).
     */
    public function convertImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:png,jpg,jpeg,webp|max:5120',
            'format' => 'required|string|in:png,jpg,webp',
            'quality' => 'required|integer|min:1|max:100',
        ]);

        try {
            $file = $request->file('image');
            $extension = strtolower($file->getClientOriginalExtension());
            $targetFormat = $request->input('format');
            $quality = $request->input('quality');
            $image = null;
            
            // Load source image
            if ($extension === 'png') {
                $image = imagecreatefrompng($file->getRealPath());
            } else if (in_array($extension, ['jpg', 'jpeg'])) {
                $image = imagecreatefromjpeg($file->getRealPath());
            } else if ($extension === 'webp') {
                $image = imagecreatefromwebp($file->getRealPath());
            }

            if (!$image) throw new \Exception("Unsupported image format or corrupted file.");

            // Prepare for transparency
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // Keep original filename, only change extension
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $tempName = $originalName . '.' . $targetFormat;
            $fullPath = storage_path('app/public/' . $tempName);
            
            if (!file_exists(storage_path('app/public'))) {
                mkdir(storage_path('app/public'), 0755, true);
            }

            // Save in target format
            switch ($targetFormat) {
                case 'webp':
                    imagewebp($image, $fullPath, $quality);
                    break;
                case 'jpg':
                    // Remove transparency for JPG (fill with white)
                    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
                    imagejpeg($bg, $fullPath, $quality);
                    imagedestroy($bg);
                    break;
                case 'png':
                    // PNG quality is 0-9 (compression level)
                    $pngQuality = (int)round((100 - $quality) / 10);
                    imagepng($image, $fullPath, $pngQuality);
                    break;
            }

            imagedestroy($image);

            return response()->json([
                'url' => asset('storage/' . $tempName),
                'filename' => $tempName
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle specialized content generation.
     */
    public function generateContent(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
            'mode' => 'required|string|in:writer,coder,vision',
        ]);

        $systemPrompt = match ($request->mode) {
            'writer' => 'You are a professional blog writer for techvoot.com. Create high-quality, SEO-optimized articles. IMPORTANT: Use the following HTML structure:
                1. Wrap major sections in <div id="slug" class="page-section"> with an <h2> header.
                2. Use <p> tags for content. You may use <span style="font-weight: 400;"> inside <p> and <li> tags for consistent styling.
                3. Use <h3> for sub-sections.
                4. Include internal links to relevant techvoot.com services (e.g., /odoo, /healthcare-software-development, /services/ai-services-solutions) using <a href="...">...</a>.
                5. Use <ul> and <ol> for lists.
                6. For Call-to-Actions, use: <div class="cta-card-box"><h3 class="h5">Ready to scale?</h3><p>Connect with our expert team today.</p><div><a class="cta-card-btn" href="https://www.techvoot.com/contact-us">Contact Us</a></div></div>.
                7. ALWAYS append a timeline index at the end: <ul class="sidebar-blog-timeline"><li class="blog-timeline-link"><a href="#slug">Section Title</a></li></ul>.
                Output ONLY clean HTML.',
            'coder' => 'You are an expert software developer. Provide clean, efficient code snippets and clear explanations.',
            'vision' => 'You are an image analysis expert. Describe images or provide creative vision insights.',
            default => 'You are a helpful assistant.',
        };

        return response()->stream(function () use ($request, $systemPrompt) {
            if (config('groq.api_key')) {
                $response = Http::withToken(config('groq.api_key'))
                    ->withOptions(['stream' => true])
                    ->withHeaders(['Accept' => 'text/event-stream'])
                    ->post(config('groq.base_url') . '/chat/completions', [
                        'model' => config('groq.default_model', 'llama-3.3-70b-versatile'),
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $request->prompt]
                        ],
                        'stream' => true,
                    ]);

                $stream = $response->toPsrResponse()->getBody();
                $buffer = '';

                while (!$stream->eof()) {
                    $chunk = $stream->read(1024);
                    $buffer .= $chunk;
                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);
                        $line = trim($line);
                        if (str_starts_with($line, 'data: ')) {
                            $data = substr($line, 6);
                            if ($data === '[DONE]') break 2;
                            $decoded = json_decode($data, true);
                            $text = $decoded['choices'][0]['delta']['content'] ?? '';
                            if ($text !== '') {
                                echo "data: " . json_encode(['text' => $text]) . "\n\n";
                                ob_flush(); flush();
                            }
                        }
                    }
                }
            } elseif (config('gemini.api_key') && config('gemini.api_key') !== 'your_google_studio_key_here') {
                try {
                    $stream = Gemini::generativeModel(ModelType::GEMINI_FLASH)->streamGenerateContent($systemPrompt . "\n\nUser request: " . $request->prompt);
                    foreach ($stream as $response) {
                        $text = $response->text();
                        if ($text) {
                            echo "data: " . json_encode(['text' => $text]) . "\n\n";
                            ob_flush(); flush();
                        }
                    }
                } catch (\Exception $e) {
                    echo "data: " . json_encode(['text' => "Error: " . $e->getMessage()]) . "\n\n";
                }
            } else {
                $stream = OpenAI::chat()->createStreamed([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $request->prompt],
                    ],
                ]);
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;
                    if ($text) {
                        echo "data: " . json_encode(['text' => $text]) . "\n\n";
                        ob_flush(); flush();
                    }
                }
            }
            echo "data: [DONE]\n\n";
            ob_flush(); flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Handle final blog content upload.
     */
    public function uploadBlog(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'content' => 'required|string',
        ]);

        try {
            // TODO: Here you would save to your 'techvoot-v2' database.
            // Example: 
            // Blog::create([
            //    'title' => $request->title,
            //    'category' => $request->category,
            //    'html_content' => $request->content,
            //    'slug' => str($request->title)->slug(),
            //    'status' => 'draft'
            // ]);

            // For now, we simulate a successful save
            return response()->json([
                'success' => true,
                'message' => 'Blog content received and ready to publish!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Standardize messy HTML into Techvoot structured HTML.
     */
    public function standardize(Request $request)
    {
        $request->validate(['html' => 'required|string']);

        $systemPrompt = "You are a Senior Technical Content Architect. Rewrite the provided HTML into a CLEAN and STRUCTURED format:
        1. Wrap each logical section in <div id=\"...\" class=\"page-section\">.
        2. Use <h2> for section titles and <h3> for sub-points.
        3. REMOVE ALL inline styles, <span> tags, and font-weight attributes.
        4. Use only semantic tags: <p>, <ul>, <li>, <a>, <strong>.
        5. Return ONLY the final HTML body content without any markdown backticks.";

        try {
            // Use Groq (Llama 3.3) - Much faster and reliable for HTML formatting
            $response = Http::withToken(config('groq.api_key'))
                ->post(config('groq.base_url') . '/chat/completions', [
                    'model' => config('groq.default_model', 'llama-3.3-70b-versatile'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $request->html]
                    ],
                    'temperature' => 0.2,
                ]);

            if ($response->failed()) {
                throw new \Exception("Groq API Error: " . $response->body());
            }

            $data = $response->json();
            $cleanHtml = $data['choices'][0]['message']['content'] ?? '';

            // Strip potential markdown backticks if AI included them
            $cleanHtml = preg_replace('/^```html\n|```$/', '', trim($cleanHtml));

            return response()->json([
                'success' => true,
                'standardized' => $cleanHtml
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
