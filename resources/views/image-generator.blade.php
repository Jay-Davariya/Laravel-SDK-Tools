<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AI Image Studio - Premium Experience</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Outfit', sans-serif; background: #0a0a0a; color: #fff; overflow-x: hidden; }
            .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.05); }
            .glow-purple { box-shadow: 0 0 50px -10px rgba(168, 85, 247, 0.2); }
            .gradient-text { background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            .bg-gradient { background: radial-gradient(circle at 50% 50%, rgba(168, 85, 247, 0.05) 0%, rgba(10, 10, 10, 1) 100%); }
            
            .image-canvas {
                aspect-ratio: 1/1;
                width: 100%;
                max-width: 600px;
                border-radius: 24px;
                overflow: hidden;
                position: relative;
                background: rgba(255, 255, 255, 0.02);
                border: 1px dashed rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .image-canvas img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }

            .loader {
                width: 48px;
                height: 48px;
                border: 3px solid #fff;
                border-bottom-color: #a855f7;
                border-radius: 50%;
                display: inline-block;
                box-sizing: border-box;
                animation: rotation 1s linear infinite;
            }

            @keyframes rotation {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .floating-label {
                position: absolute;
                bottom: 20px;
                left: 20px;
                right: 20px;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(10px);
                padding: 12px 20px;
                border-radius: 16px;
                font-size: 0.875rem;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
        </style>
    </head>
    <body class="antialiased bg-gradient min-h-screen flex flex-col items-center justify-center p-6">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-96 bg-purple-500/10 blur-[120px] rounded-full -z-10"></div>
        
        <div class="max-w-4xl w-full text-center space-y-8">
            <a href="/" class="inline-flex items-center space-x-2 text-gray-400 hover:text-white transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Dashboard</span>
            </a>

            <h1 class="text-5xl md:text-6xl font-bold tracking-tight">
                AI <span class="gradient-text">Image Generator</span>
            </h1>
            
            <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                Turn your imagination into stunning visuals using state-of-the-art diffusion models.
            </p>
            
            <div class="flex flex-col items-center space-y-8 pt-8">
                <div id="canvas" class="image-canvas glass glow-purple">
                    <div id="placeholder" class="text-gray-500 flex flex-col items-center space-y-4">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p>Your creation will appear here</p>
                    </div>
                    <img id="result-image" src="" class="hidden">
                    <div id="loading-overlay" class="absolute inset-0 bg-black/40 flex items-center justify-center hidden">
                        <span class="loader"></span>
                    </div>
                    <div id="image-label" class="floating-label hidden">
                        <p class="text-xs text-purple-400 font-bold uppercase mb-1">Generated Prompt</p>
                        <p id="revised-prompt-text" class="text-white line-clamp-2"></p>
                    </div>
                </div>

                <div class="w-full max-w-2xl space-y-6">
                    <!-- Style Presets -->
                    <div class="glass rounded-2xl p-4 overflow-x-auto scrollbar-hide">
                        <p class="text-xs text-gray-500 font-bold uppercase mb-3 text-left px-2">Style Presets</p>
                        <div class="flex space-x-3 pb-2" id="style-presets">
                            <button type="button" data-style="None" class="style-btn active px-4 py-2 rounded-lg text-sm whitespace-nowrap glass border-purple-500/50 text-white transition-all">None</button>
                            <button type="button" data-style="Photorealistic" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">📸 Realistic</button>
                            <button type="button" data-style="Digital Art" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">🎨 Digital Art</button>
                            <button type="button" data-style="3D Render" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">🕹️ 3D Render</button>
                            <button type="button" data-style="Cinematic" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">🎬 Cinematic</button>
                            <button type="button" data-style="Anime" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">🏮 Anime</button>
                            <button type="button" data-style="Cyberpunk" class="style-btn px-4 py-2 rounded-lg text-sm whitespace-nowrap glass hover:border-purple-500/30 text-gray-400 transition-all">🏙️ Cyberpunk</button>
                        </div>
                    </div>

                    <!-- Size Selection -->
                    <div class="glass rounded-2xl p-4 flex items-center justify-between">
                        <p class="text-xs text-gray-500 font-bold uppercase text-left px-2">Aspect Ratio</p>
                        <div class="flex space-x-2" id="size-selection">
                            <button type="button" data-size="1024x1024" class="size-btn active p-2 rounded-lg glass border-purple-500/50 text-white" title="Square (1:1)">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><rect x="4" y="4" width="16" height="16" rx="2"/></svg>
                            </button>
                            <button type="button" data-size="1792x1024" class="size-btn p-2 rounded-lg glass text-gray-400" title="Landscape (16:9)">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="6" width="20" height="12" rx="2"/></svg>
                            </button>
                            <button type="button" data-size="1024x1792" class="size-btn p-2 rounded-lg glass text-gray-400" title="Portrait (9:16)">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="2" width="12" height="20" rx="2"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="glass rounded-3xl p-6 glow-purple">
                        <form id="gen-form" class="relative">
                            <textarea id="prompt" name="prompt" rows="2" placeholder="Describe the image you want to create..." class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 px-6 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all resize-none mb-4" required></textarea>
                            <button type="submit" class="w-full py-4 rounded-xl bg-purple-600 text-white font-bold hover:bg-purple-500 transition-all shadow-lg shadow-purple-600/20" id="submit-btn">
                                Generate Magic
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .style-btn.active, .size-btn.active { border-color: rgba(168, 85, 247, 0.5); background: rgba(168, 85, 247, 0.1); color: white; }
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        </style>

        <script>
            let currentStyle = 'None';
            let currentSize = '1024x1024';

            // Handle Style Selection
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.style-btn').forEach(b => b.classList.remove('active', 'border-purple-500/50', 'text-white'));
                    btn.classList.add('active', 'border-purple-500/50', 'text-white');
                    currentStyle = btn.dataset.style;
                });
            });

            // Handle Size Selection
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active', 'border-purple-500/50', 'text-white'));
                    btn.classList.add('active', 'border-purple-500/50', 'text-white');
                    currentSize = btn.dataset.size;
                    
                    // Adjust canvas aspect ratio for preview
                    const canvas = document.getElementById('canvas');
                    if (currentSize === '1792x1024') canvas.style.aspectRatio = '16/9';
                    else if (currentSize === '1024x1792') canvas.style.aspectRatio = '9/16';
                    else canvas.style.aspectRatio = '1/1';
                });
            });

            document.getElementById('gen-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const promptInput = document.getElementById('prompt');
                const prompt = promptInput.value;
                const canvas = document.getElementById('canvas');
                const placeholder = document.getElementById('placeholder');
                const resultImg = document.getElementById('result-image');
                const loader = document.getElementById('loading-overlay');
                const label = document.getElementById('image-label');
                const revisedPrompt = document.getElementById('revised-prompt-text');
                const btn = document.getElementById('submit-btn');
                
                btn.disabled = true;
                btn.innerText = 'Creating...';
                loader.classList.remove('hidden');
                placeholder.classList.add('hidden');
                label.classList.add('hidden');

                try {
                    const response = await fetch('{{ route('ai.image') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            prompt,
                            style: currentStyle,
                            size: currentSize
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.url) {
                        resultImg.src = data.url;
                        resultImg.classList.remove('hidden');
                        revisedPrompt.innerText = data.revised_prompt;
                        label.classList.remove('hidden');
                    } else if (data.error) {
                        alert('Error: ' + data.error);
                        placeholder.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to generate image. Check console for details.');
                    placeholder.classList.remove('hidden');
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'Generate Magic';
                    loader.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
