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
                if (isset($msg['role'], $msg['content'])) {
                    $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
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
                            if (isset($msg['role'], $msg['content'])) {
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
                2. Use <p> tags for content.
                3. Use <h3> for sub-sections.
                4. Include internal links to relevant techvoot.com services (e.g., /odoo, /healthcare-software-development, /services/ai-services-solutions) using <a href="...">...</a>.
                5. Use <ul> and <ol> for lists.
                6. For Call-to-Actions, use: <div class="cta-card-box"><h3 class="h5">Ready to scale?</h3><p>Connect with our expert team today.</p><div><a class="cta-card-btn" href="https://www.techvoot.com/contact-us">Contact Us</a></div></div>.
                7. If including tables, always wrap them in <div class="table-responsive">. The table element itself must be <table class="table table-bordered" style="width: 100%;">. Table headers must be inside <thead> with <th> tags (optionally containing <strong> for bold headings), and the table body must be inside <tbody> with <td> tags. Keep table cells clean without nesting <p> elements inside <td> or <th> tags.
                8. Apply Blog Highlights rules dynamically by detecting patterns in the content:
                   - Highlight Box (List Sections): If the content contains a heading followed by a numbered or bulleted list (such as "What This Guide Covers", "Key Takeaways", "Checklist", etc.), wrap the heading and the list in:
                     <div class="blog-highlight-bg-box">
                       <h3>Heading</h3>
                       <ol/ul>
                         <li>Item content</li>
                       </ol/ul>
                     </div>
                     (Use <h3> for the heading, convert numbered lists to <ol>, and bulleted lists to <ul>).
                   - Highlight Paragraph: If a standalone paragraph should be highlighted, wrap it in:
                     <div class="blog-highlight-p">
                       <h3>Optional Heading</h3>
                       <p>Highlighted paragraph...</p>
                     </div>
                     (If there is no heading, omit the <h3>).
                   - Left Border Highlight: If a paragraph starts with labels such as "Best:", "Best For:", "Tip:", "Important:", "Warning:", "Recommendation:", or "Pro Tip:" (including variations ending with " :-" or ":-"), wrap it in:
                     <div class="blog-highlight-with-left-border">
                       <p><strong>Normalized Label:</strong> Rest of content.</p>
                     </div>
                     (Normalize the label to its standard title-cased form followed by a colon, e.g., <strong>Best For:</strong>, <strong>Pro Tip:</strong>, <strong>Tip:</strong>, <strong>Important:</strong>, <strong>Warning:</strong>).
                   - Note Handling: If a paragraph starts with "Note:", "Note :-", "Note:-", or "Note :- ", remove the "Note" label entirely (including any colon, hyphen, or trailing space) and wrap the remaining content in:
                     <div class="blog-highlight-with-left-border">
                       <p>Content...</p>
                     </div>
                   - Note: Use ONLY these class names for highlight features: blog-highlight-bg-box, blog-highlight-p, blog-highlight-with-left-border. No inline styles are allowed.
                9. ALWAYS append a timeline index at the end: <ul class="sidebar-blog-timeline"><li class="blog-timeline-link"><a href="#slug">Section Title</a></li></ul>.
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
                    $stream = Gemini::generativeModel(config('services.gemini.model', 'gemini-2.5-flash'))->streamGenerateContent($systemPrompt . "\n\nUser request: " . $request->prompt);
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
        1. Wrap each logical section in <div id=\"...\" class=\"page-section\">. The ID must be a short, clean, slugified version of the section's <h2> title (e.g., if the title is 'Introduction', the id must be 'introduction'; if the title is 'Remote Development Team Cost & Timeline Comparison', the id must be 'cost-timeline-comparison').
        2. Use <h2> for section titles and <h3> for sub-points (unless styled inside highlight boxes).
        3. REMOVE ALL inline styles (except style=\"width: 100%;\" on table as specified below), <span> tags, and font-weight attributes.
        4. Use only semantic tags: <p>, <ul>, <li>, <a>, <strong>, and standard table/heading/list tags.
           - CRITICAL: Absolutely DO NOT remove or strip any anchor links (<a> tags) present in the source HTML. Keep them exactly in their original positions with their href attributes preserved.
           - Manage the target attribute: For external links (URLs starting with http:// or https:// that do not point to techvoot.com), ensure they have target=\"_blank\" and rel=\"noopener noreferrer\". For internal links (or any anchor links that originally had target=\"_blank\"), preserve target=\"_blank\" and do not remove it.
        5. If the input contains a table, ensure the table is wrapped in a `<div class=\"table-responsive\">` container.
        6. The `<table>` element itself must have the classes `table table-bordered` and the inline style `style=\"width: 100%;\"` (i.e., <table class=\"table table-bordered\" style=\"width: 100%;\">).
        7. Table header rows must be placed inside `<thead>` with header cells using `<th>` tags (optionally containing `<strong>` for bold headings). All data rows must be placed inside `<tbody>` with data cells using `<td>` tags.
        8. Clean up all inner paragraphs and styling inside table cells (<td>/<th>) to keep them clean. E.g., instead of <td><p><strong>Content</strong></p></td>, simplify to <td>Content</td> (or <th><strong>Content</strong></th>).
        9. Apply Blog Highlights rules dynamically by detecting patterns in the content:
           - Highlight Box (List Sections): If the content contains a heading followed by a numbered or bulleted list (such as 'What This Guide Covers', 'Key Takeaways', 'Checklist', etc.), wrap the heading and the list in:
             <div class=\"blog-highlight-bg-box\">
               <h3>Heading</h3>
               <ol/ul>
                 <li>Item content</li>
               </ol/ul>
             </div>
             (Use <h3> for the heading, convert numbered lists to <ol>, and bulleted lists to <ul>).
           - Highlight Paragraph: If a standalone paragraph should be highlighted, wrap it in:
             <div class=\"blog-highlight-p\">
               <h3>Optional Heading</h3>
               <p>Highlighted paragraph...</p>
             </div>
             (If there is no heading, omit the <h3>).
           - Left Border Highlight: If a paragraph starts with labels such as 'Best:', 'Best For:', 'Tip:', 'Important:', 'Warning:', 'Recommendation:', or 'Pro Tip:' (including variations ending with ' :-' or ':-'), wrap it in:
             <div class=\"blog-highlight-with-left-border\">
               <p><strong>Normalized Label:</strong> Rest of content.</p>
             </div>
             (Normalize the label to its standard title-cased form followed by a colon, e.g., <strong>Best For:</strong>, <strong>Pro Tip:</strong>, <strong>Tip:</strong>, <strong>Important:</strong>, <strong>Warning:</strong>).
           - Note Handling: If a paragraph starts with 'Note:', 'Note :-', 'Note:-', or 'Note :- ', remove the 'Note' label entirely (including any colon, hyphen, or trailing space) and wrap the remaining content in:
             <div class=\"blog-highlight-with-left-border\">
               <p>Content...</p>
             </div>
           - Note: Use ONLY these class names for highlight features: blog-highlight-bg-box, blog-highlight-p, blog-highlight-with-left-border. No inline styles are allowed.
        10. Always append a timeline index at the end of the content using this custom structure:
           <ul class=\"sidebar-blog-timeline\">
             <li class=\"blog-timeline-link\"><a href=\"#section-id\">Section Title</a></li>
             ...
           </ul>
           Create one <li> entry for each <h2> section title in the document, matching the generated ID and title text exactly.
        11. CRITICAL: Do not summarize, shorten, or omit any section of the input HTML. Every paragraph, list item, table row, and section (such as FAQs) from the original content must be fully preserved and included in the output. Truncation or laziness is strictly forbidden.
        12. Return ONLY the final HTML body content without any markdown backticks.";

        $htmlContent = $request->html;
        
        // Chunk HTML to avoid max output token limits in large documents
        $chunks = $this->chunkHtml($htmlContent, 15000);
        $standardizedChunks = [];
        $errors = [];

        foreach ($chunks as $index => $chunk) {
            $chunkHtml = null;
            $chunkErrors = [];

            // 1. Try Gemini
            if (config('gemini.api_key') && config('gemini.api_key') !== 'your_google_studio_key_here') {
                try {
                    $response = Gemini::generativeModel(config('services.gemini.model', 'gemini-2.5-flash'))
                        ->generateContent($systemPrompt . "\n\nUser request: " . $chunk);
                    $chunkHtml = $response->text();
                    \Illuminate\Support\Facades\Log::info("Standardize chunk " . ($index + 1) . ": Gemini provider succeeded.");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Standardize chunk " . ($index + 1) . ": Gemini provider failed. Error: " . $e->getMessage());
                    $chunkErrors[] = "Gemini Error: " . $e->getMessage();
                }
            }

            // 2. Try OpenAI if Gemini failed or skipped
            if (empty($chunkHtml) && config('openai.api_key')) {
                try {
                    $response = OpenAI::chat()->create([
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $chunk],
                        ],
                        'temperature' => 0.2,
                    ]);
                    $chunkHtml = $response->choices[0]->message->content;
                    \Illuminate\Support\Facades\Log::info("Standardize chunk " . ($index + 1) . ": OpenAI provider succeeded.");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Standardize chunk " . ($index + 1) . ": OpenAI provider failed. Error: " . $e->getMessage());
                    $chunkErrors[] = "OpenAI Error: " . $e->getMessage();
                }
            }

            // 3. Try Groq if others failed or skipped
            if (empty($chunkHtml) && config('groq.api_key')) {
                try {
                    $response = Http::withToken(config('groq.api_key'))
                        ->post(config('groq.base_url') . '/chat/completions', [
                            'model' => config('groq.default_model', 'llama-3.3-70b-versatile'),
                            'messages' => [
                                ['role' => 'system', 'content' => $systemPrompt],
                                ['role' => 'user', 'content' => $chunk]
                            ],
                            'temperature' => 0.2,
                        ]);

                    if ($response->failed()) {
                        throw new \Exception("Groq API Error: " . $response->body());
                    }

                    $data = $response->json();
                    $chunkHtml = $data['choices'][0]['message']['content'] ?? '';
                    \Illuminate\Support\Facades\Log::info("Standardize chunk " . ($index + 1) . ": Groq provider succeeded.");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Standardize chunk " . ($index + 1) . ": Groq provider failed. Error: " . $e->getMessage());
                    $chunkErrors[] = "Groq Error: " . $e->getMessage();
                }
            }

            if (empty($chunkHtml)) {
                $errors[] = "Chunk " . ($index + 1) . " failed: " . implode(" | ", $chunkErrors);
                break;
            }

            // Strip potential markdown backticks if AI included them
            $chunkHtml = preg_replace('/^```html\n|```$/', '', trim($chunkHtml));
            
            // Strip any timeline index from intermediate chunk results
            $chunkHtml = preg_replace('/<ul class="sidebar-blog-timeline">.*?<\/ul>/is', '', $chunkHtml);

            $standardizedChunks[] = $chunkHtml;
        }

        try {
            if (count($standardizedChunks) < count($chunks)) {
                throw new \Exception("Failed to process all document chunks. Errors: " . implode(" | ", $errors));
            }

            // Combine all chunks
            $combinedHtml = implode("\n", $standardizedChunks);

            // Strip any leftover/duplicate timeline indexes
            $combinedHtml = preg_replace('/<ul class="sidebar-blog-timeline">.*?<\/ul>/is', '', $combinedHtml);

            // Re-generate the single final timeline index from all h2 headings in the combined HTML
            $timeline = $this->generateTimeline($combinedHtml);
            if (!empty($timeline)) {
                $combinedHtml = rtrim($combinedHtml) . "\n\n" . $timeline;
            }

            return response()->json([
                'success' => true,
                'standardized' => $combinedHtml
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chunk HTML content into smaller blocks.
     */
    private function chunkHtml(string $html, int $maxLength = 15000): array
    {
        if (strlen($html) <= $maxLength) {
            return [$html];
        }

        // Try to split by <h2> tags first (main sections)
        $parts = preg_split('/(<h2[^>]*>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        if (count($parts) > 1) {
            $chunks = [];
            $currentChunk = $parts[0];
            
            for ($i = 1; $i < count($parts); $i += 2) {
                $h2Tag = $parts[$i];
                $sectionContent = $parts[$i + 1] ?? '';
                $sectionHtml = $h2Tag . $sectionContent;
                
                if (strlen($currentChunk) + strlen($sectionHtml) > $maxLength && strlen($currentChunk) > 0) {
                    $chunks[] = $currentChunk;
                    $currentChunk = $sectionHtml;
                } else {
                    $currentChunk .= $sectionHtml;
                }
            }
            if (strlen($currentChunk) > 0) {
                $chunks[] = $currentChunk;
            }
            return $chunks;
        }

        // Fallback: split by paragraph/div closures if no h2 tags exist
        $parts = preg_split('/(<\/p>|<\/div>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $chunks = [];
        $currentChunk = '';
        for ($i = 0; $i < count($parts); $i += 2) {
            $content = $parts[$i];
            $tag = $parts[$i + 1] ?? '';
            $segment = $content . $tag;
            
            if (strlen($currentChunk) + strlen($segment) > $maxLength && strlen($currentChunk) > 0) {
                $chunks[] = $currentChunk;
                $currentChunk = $segment;
            } else {
                $currentChunk .= $segment;
            }
        }
        if (strlen($currentChunk) > 0) {
            $chunks[] = $currentChunk;
        }
        return $chunks;
    }

    /**
     * Generate the sidebar blog timeline index programmatically from the HTML.
     */
    private function generateTimeline(string $html): string
    {
        preg_match_all('/<div[^>]*id="([^"]+)"[^>]*>\s*<h2[^>]*>(.*?)<\/h2>/is', $html, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            return '';
        }
        
        $timeline = '<ul class="sidebar-blog-timeline">' . "\n";
        foreach ($matches as $match) {
            $id = $match[1];
            $title = strip_tags($match[2]);
            $timeline .= '  <li class="blog-timeline-link"><a href="#' . $id . '">' . $title . '</a></li>' . "\n";
        }
        $timeline .= '</ul>';
        
        return $timeline;
    }
}
