import re

with open('/var/www/html/Laravel-ai-sdk/resources/views/welcome.blade.php', 'r') as f:
    content = f.read()

# 1. Update the <style> section
style_pattern = re.compile(r'<style>.*?</style>', re.DOTALL)

new_style = """<style>
    body { font-family: 'Outfit', sans-serif; background: #f3f4f6; color: #111827; overflow-x: hidden; }
    .glass { background: #ffffff; border: 1px solid #e5e7eb; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .glass-hover:hover { background: #f9fafb; border: 1px solid #d1d5db; }
    .glow-blue { box-shadow: 0 0 15px rgba(56, 189, 248, 0.2); }
    .gradient-text { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    
    /* Sidebar Tab styling */
    .tab-active { background: #eff6ff; border-left: 4px solid #3b82f6; color: #1d4ed8; font-weight: 700; }
    .feature-content { display: none; }
    .feature-content.active { display: block; animation: fadeIn 0.4s ease-out; }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .image-preview { aspect-ratio: 16/9; border-radius: 1rem; overflow: hidden; background: #f9fafb; border: 1px dashed #d1d5db; }
    .typing::after { content: '|'; animation: blink 1s infinite; }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

    .loader { width: 24px; height: 24px; border: 2px solid #3b82f6; border-bottom-color: transparent; border-radius: 50%; display: inline-block; animation: rotation 1s linear infinite; }
    @keyframes rotation { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

    /* Base Form Elements */
    input, textarea, select {
        background-color: #ffffff !important;
        color: #111827 !important;
        border: 1px solid #d1d5db !important;
    }
    input::placeholder, textarea::placeholder {
        color: #9ca3af !important;
    }

    .chat-bubble { max-width: 85%; position: relative; transition: all 0.2s ease; }
    .chat-bubble:hover { transform: scale(1.005); }

    /* Markdown Styles */
    .markdown-body h1, .markdown-body h2, .markdown-body h3 { font-weight: bold; margin-top: 1.5em; margin-bottom: 0.5em; color: #111827; }
    .markdown-body h1 { font-size: 1.875rem; }
    .markdown-body h2 { font-size: 1.5rem; }
    .markdown-body h3 { font-size: 1.25rem; }
    .markdown-body p { margin-bottom: 1rem; line-height: 1.7; padding: 10px; color: #374151;}
    .markdown-body ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; color: #374151;}
    .markdown-body ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1rem; color: #374151;}
    .markdown-body a { color: #2563eb; text-decoration: underline; }
    .markdown-body strong { font-weight: 700; color: #111827; }
    .markdown-body code:not([class*="language-"]) { background: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 0.25rem; font-family: monospace; font-size: 0.875em; color: #d946ef; }
    .markdown-body pre { background: #1f2937; padding: 1rem; border-radius: 0.75rem; overflow-x: auto; margin-bottom: 1rem; color: #f9fafb; border: 1px solid #374151; }
    .markdown-body blockquote { border-left: 4px solid #3b82f6; padding-left: 1rem; color: #6b7280; font-style: italic; margin-bottom: 1rem; }

    /* Techvoot Blog Styles */
    .page-section { margin-bottom: 3rem; padding: 1.5rem; background: #ffffff; border-radius: 1rem; border: 1px solid #e5e7eb; }
    .page-section h2 { font-size: 1.75rem; font-weight: 700; color: #2563eb; margin-bottom: 1.25rem; }
    .page-section h3 { font-size: 1.25rem; color: #4f46e5; margin-top: 1.5rem; }
    .page-section p { line-height: 1.8; color: #4b5563; margin-bottom: 1rem; }
    
    .cta-card-box { background: linear-gradient(135deg, #eff6ff, #eef2ff); border: 1px solid #bfdbfe; padding: 2rem; border-radius: 1.5rem; margin: 2rem 0; text-align: center; }
    .cta-card-box h3.h5 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: #111827;}
    .cta-card-btn { display: inline-block; background: #2563eb; color: #ffffff; font-weight: 700; padding: 0.75rem 1.5rem; border-radius: 0.75rem; text-decoration: none; margin-top: 1rem; transition: all 0.3s ease; }
    .cta-card-btn:hover { background: #1d4ed8; transform: translateY(-2px); }

    .sidebar-blog-timeline { list-style: none; padding: 0; margin-top: 4rem; border-top: 1px solid #e5e7eb; padding-top: 2rem; }
    .blog-timeline-link { margin-bottom: 0.75rem; }
    .blog-timeline-link a { color: #6b7280; font-size: 0.875rem; text-decoration: none; transition: color 0.2s; }
    .blog-timeline-link a:hover { color: #2563eb; }
    .blog-timeline-link a::before { content: '→'; margin-right: 0.5rem; opacity: 0.5; }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Admin Layout Styles */
    .admin-layout { display: flex; height: 100vh; overflow: hidden; background: #f3f4f6; }
    .admin-sidebar { width: 260px; flex-shrink: 0; background: #ffffff; border-right: 1px solid #e5e7eb; display: flex; flex-direction: column; overflow-y: auto;}
    .admin-main { flex: 1; overflow-y: auto; padding: 2rem; position: relative;}
    
    @media (max-width: 1024px) {
        .admin-layout { flex-direction: column; }
        .admin-sidebar { width: 100%; height: auto; border-right: none; border-bottom: 1px solid #e5e7eb; flex-direction: row; align-items: center; justify-content: space-between; padding: 10px; overflow-x: auto;}
        .admin-sidebar nav { display: flex; flex-direction: row; }
        .tab-btn { display: inline-block; width: auto; margin-right: 0.5rem; white-space: nowrap; }
        .tab-active { border-left: none; border-bottom: 4px solid #3b82f6; border-radius: 0.5rem 0.5rem 0 0; }
    }
</style>"""

content = style_pattern.sub(new_style, content)

# 2. Update Layout: Body and Header -> Admin Layout
# Find everything from <body to </header>
header_pattern = re.compile(r'<body class="antialiased[^>]*>.*?<\/header>', re.DOTALL)

sidebar_html = """<body class="antialiased text-gray-900 admin-layout">
    
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold tracking-tight text-blue-600">Techvoot <span class="text-gray-900">AI</span></h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mt-1">Admin Panel</p>
        </div>
        
        <nav class="flex-1 p-4 space-y-2">
            @auth
                <button onclick="switchTab('assistant')" id="tab-assistant" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all tab-active text-gray-700 hover:bg-gray-100 flex items-center gap-3">
                    Assistant
                </button>
                <button onclick="switchTab('studio')" id="tab-studio" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Studio
                </button>
                <button onclick="switchTab('writer')" id="tab-writer" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Writer
                </button>
                <button onclick="switchTab('coder')" id="tab-coder" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Coder
                </button>
                <button onclick="switchTab('upload')" id="tab-upload" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Blog Upload
                </button>
                <button onclick="switchTab('media')" id="tab-media" class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Media Tools
                </button>
            @endauth
        </nav>

        <div class="p-4 border-t border-gray-200">
            @if (Route::has('login'))
                @auth
                    <div class="space-y-2">
                        <a href="{{ url('/') }}" class="block w-full text-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 rounded-lg text-sm font-bold bg-red-50 text-red-600 hover:bg-red-100 transition-all">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center space-x-2 px-2 py-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Auth Required</span>
                    </div>
                @endauth
            @endif
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="admin-main">"""

content = header_pattern.sub(sidebar_html, content)

# 3. Replace text colors and background colors for light theme
replacements = {
    'text-white': 'text-gray-900',
    'text-gray-400': 'text-gray-600',
    'bg-white/5': 'bg-white',
    'bg-white/10': 'bg-gray-100',
    'border-white/10': 'border-gray-200',
    'border-white/5': 'border-gray-200',
    'shadow-2xl': 'shadow-lg',
    'bg-black/20': 'bg-gray-50',
    'bg-black/40': 'bg-gray-50',
    'bg-white/[0.02]': 'bg-white',
    'bg-white/[0.01]': 'bg-white',
    'text-gray-200': 'text-gray-800',
    'text-gray-300': 'text-gray-700',
}

# Apply replacements to the main content area (excluding the script part)
parts = content.split('<script>')
main_content = parts[0]
script_content = '<script>' + parts[1]

for old, new in replacements.items():
    main_content = main_content.replace(old, new)

# 4. Update JavaScript switchTab function
script_content = script_content.replace(
    "b.classList.add('text-gray-400');\n                    b.classList.add('hover:bg-white/5');",
    "b.classList.add('text-gray-500');\n                    b.classList.add('hover:bg-blue-50');\n                    b.classList.add('hover:text-blue-600');"
)

script_content = script_content.replace(
    "btn.classList.remove('text-gray-400');\n                btn.classList.remove('hover:bg-white/5');",
    "btn.classList.remove('text-gray-500');\n                btn.classList.remove('hover:bg-blue-50');\n                btn.classList.remove('hover:text-blue-600');"
)

content = main_content + script_content

with open('/var/www/html/Laravel-ai-sdk/resources/views/welcome.blade.php', 'w') as f:
    f.write(content)

print("Migration complete!")
