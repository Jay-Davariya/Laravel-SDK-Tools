<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel AI SDK - Premium Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f3f4f6;
            color: #111827;
            overflow-x: hidden;
        }

        .glass {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 1515px;
        }

        .glass-hover:hover {
            background: #f9fafb;
            border: 1px solid #d1d5db;
        }

        .glow-blue {
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.2);
        }

        .gradient-text {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Sidebar Tab styling */
        .tab-active {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            color: #1d4ed8;
            font-weight: 700;
        }

        .feature-content {
            display: none;
            height: 800px;
            width: 1555px;
        }

        .feature-content.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .image-preview {
            aspect-ratio: 16/9;
            border-radius: 1rem;
            overflow: hidden;
            background: #f9fafb;
            border: 1px dashed #d1d5db;
        }

        .typing::after {
            content: '|';
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        .loader {
            width: 24px;
            height: 24px;
            border: 2px solid #3b82f6;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Base Form Elements */
        input,
        textarea,
        select {
            background-color: #ffffff !important;
            color: #111827 !important;
            border: 1px solid #d1d5db !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #9ca3af !important;
        }

        .chat-bubble {
            max-width: 85%;
            position: relative;
            transition: all 0.2s ease;
        }

        .chat-bubble:hover {
            transform: scale(1.005);
        }

        /* Markdown Styles */
        .markdown-body h1,
        .markdown-body h2,
        .markdown-body h3 {
            font-weight: bold;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            color: #111827;
        }

        .markdown-body h1 {
            font-size: 1.875rem;
        }

        .markdown-body h2 {
            font-size: 1.5rem;
        }

        .markdown-body h3 {
            font-size: 1.25rem;
        }

        .markdown-body p {
            margin-bottom: 1rem;
            line-height: 1.7;
            padding: 10px;
            color: #374151;
        }

        .markdown-body ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
            color: #374151;
        }

        .markdown-body ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
            color: #374151;
        }

        .markdown-body a {
            color: #2563eb;
            text-decoration: underline;
        }

        .markdown-body strong {
            font-weight: 700;
            color: #111827;
        }

        .markdown-body code:not([class*="language-"]) {
            background: #f3f4f6;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-family: monospace;
            font-size: 0.875em;
            color: #d946ef;
        }

        .markdown-body pre {
            background: #1f2937;
            padding: 1rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            margin-bottom: 1rem;
            color: #f9fafb;
            border: 1px solid #374151;
        }

        .markdown-body blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 1rem;
        }

        /* Techvoot Blog Styles */
        .page-section {
            margin-bottom: 3rem;
            padding: 1.5rem;
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
        }

        .page-section h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 1.25rem;
        }

        .page-section h3 {
            font-size: 1.25rem;
            color: #4f46e5;
            margin-top: 1.5rem;
        }

        .page-section p {
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 1rem;
        }

        .cta-card-box {
            background: linear-gradient(135deg, #eff6ff, #eef2ff);
            border: 1px solid #bfdbfe;
            padding: 2rem;
            border-radius: 1.5rem;
            margin: 2rem 0;
            text-align: center;
        }

        .cta-card-box h3.h5 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .cta-card-btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            font-weight: 700;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .cta-card-btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        /* Standardized Table Styling */
        .table-responsive {
            overflow-x: auto;
            margin: 1.5rem 0;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .table-responsive table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .table-responsive th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #1f2937;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }

        .table-responsive th:last-child {
            border-right: none;
        }

        .table-responsive td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 0.875rem;
        }

        .table-responsive td:last-child {
            border-right: none;
        }

        .table-responsive tr:last-child td {
            border-bottom: none;
        }

        .table-responsive tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .sidebar-blog-timeline {
            list-style: none;
            padding: 0;
            margin-top: 4rem;
            border-top: 1px solid #e5e7eb;
            padding-top: 2rem;
        }

        .blog-timeline-link {
            margin-bottom: 0.75rem;
        }

        .blog-timeline-link a {
            color: #6b7280;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
        }

        .blog-timeline-link a:hover {
            color: #2563eb;
        }

        .blog-timeline-link a::before {
            content: '→';
            margin-right: 0.5rem;
            opacity: 0.5;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Admin Layout Styles */
        .admin-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background: #f3f4f6;
        }

        .admin-sidebar {
            width: 260px;
            flex-shrink: 0;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .admin-main {
            flex: 1;
            overflow-y: auto;
            padding: 2rem;
            position: relative;
        }

        @media (max-width: 1024px) {
            .admin-layout {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                padding: 10px;
                overflow-x: auto;
            }

            .admin-sidebar nav {
                display: flex;
                flex-direction: row;
            }

            .tab-btn {
                display: inline-block;
                width: auto;
                margin-right: 0.5rem;
                white-space: nowrap;
                height: 45px;
                padding: 5px 10px 5px 10px !important;
            }

            .tab-active {
                border-left: none;
                border-bottom: 4px solid #3b82f6;
                border-radius: 0.5rem 0.5rem 0 0;
            }

        }
    </style>
</head>

<body class="antialiased text-gray-900 admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-2xl font-bold tracking-tight text-blue-600">Techvoot <span class="text-gray-900">AI</span>
            </h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mt-1">Admin Panel</p>
        </div>

        <nav class="flex-1 p-4 flex flex-col gap-2">
            @auth
                <button onclick="switchTab('assistant')" id="tab-assistant"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all tab-active text-gray-700 hover:bg-gray-100 flex items-center gap-3">
                    Assistant
                </button>
                <button onclick="switchTab('studio')" id="tab-studio"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Studio
                </button>
                <button onclick="switchTab('writer')" id="tab-writer"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Writer
                </button>
                <button onclick="switchTab('coder')" id="tab-coder"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Coder
                </button>
                <button onclick="switchTab('upload')" id="tab-upload"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Blog Upload
                </button>
                <button onclick="switchTab('media')" id="tab-media"
                    class="tab-btn w-full text-left px-4 py-3 rounded-xl text-sm font-semibold transition-all text-gray-500 hover:text-blue-600 hover:bg-blue-50 flex items-center gap-3">
                    Media Tools
                </button>
            @endauth
        </nav>

        <div class="p-4 border-t border-gray-200">
            @if (Route::has('login'))
                @auth
                    <div class="space-y-2">
                        <a href="{{ url('/') }}"
                            class="block w-full text-center px-4 py-2 rounded-lg text-sm font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full px-4 py-2 rounded-lg text-sm font-bold bg-red-50 text-red-600 hover:bg-red-100 transition-all">Logout</button>
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
    <main class="admin-main">

        <main class="w-full max-w-7xl">
            @guest
                <div class="max-w-md mx-auto mt-12 mb-24">
                    <div class="glass p-6 rounded-xl border border-gray-200 shadow-sm bg-white">
                        <div class="text-center mb-10">
                            <h2 class="text-xl font-bold mb-3">Fusion <span class="gradient-text">Portal</span></h2>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-[0.2em]">Authorized Access Only
                            </p>
                        </div>

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-5">
                            @csrf
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Email
                                    Address</label>
                                <input type="email" name="email" required autofocus
                                    class="w-full bg-white border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-sm"
                                    placeholder="admin@techvoot.com">
                                @error('email') <p class="text-[10px] text-red-400 mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label
                                        class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Password</label>
                                    <a href="{{ route('password.request') }}"
                                        class="text-[10px] font-black text-blue-400 hover:text-gray-900 transition-all uppercase tracking-widest">Forgot?</a>
                                </div>
                                <input type="password" name="password" required
                                    class="w-full bg-white border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-sm"
                                    placeholder="••••••••">
                            </div>
                            <div class="flex items-center space-x-3 ml-1">
                                <input type="checkbox" name="remember" id="remember_me"
                                    class="w-4 h-4 rounded bg-white border-gray-200 text-blue-600 focus:ring-blue-500/50">
                                <label for="remember_me"
                                    class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Remember
                                    Me</label>
                            </div>
                            <button type="submit"
                                class="w-full md:w-auto px-8 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-blue-500 transition-all shadow-xl shadow-blue-900/20">
                                Sign In
                            </button>
                        </form>

                        <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">New to Fusion AI?
                            </p>
                            <a href="{{ route('register') }}"
                                class="inline-block px-8 py-3 rounded-xl bg-white border border-gray-200 text-[10px] font-black text-blue-400 uppercase tracking-widest hover:bg-gray-100 transition-all">Create
                                Account</a>
                        </div>
                    </div>
                </div>
            @else
                @auth
                    <!-- Media Tools Feature -->
                    <div id="content-media" class="feature-content">
                        <div class="max-w-6xl mx-auto glass p-6 md:p-6 rounded-xl  border border-gray-200 shadow-sm">
                            <div class="mb-10">
                                <h2 class="text-xl font-bold mb-2">Professional <span class="gradient-text">Image
                                        Converter</span></h2>
                                <p class="text-sm text-gray-500 font-medium">Convert between PNG, JPG, and WebP instantly with
                                    zero loss in quality. Preserve transparency and optimize file sizes for the web.</p>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                                <!-- Left Column: Controls -->
                                <div class="lg:col-span-5 space-y-8">
                                    <!-- Drop Zone -->
                                    <div id="drop-zone"
                                        class="relative p-6 rounded-xl bg-white border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-center group hover:border-blue-500/50 hover:bg-blue-500/[0.02] transition-all cursor-pointer">
                                        <input type="file" id="media-input" class="absolute inset-0 opacity-0 cursor-pointer"
                                            accept="image/png,image/jpeg,image/webp" onchange="handleFileSelect(event)">

                                        <div id="drop-zone-prompt" class="space-y-4">
                                            <div
                                                class="w-6 h-6 rounded-2xl bg-blue-500/10 flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                                                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-700">Drop your image here</p>
                                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">or <span
                                                        class="text-blue-400">browse files</span> from your computer</p>
                                            </div>
                                            <div class="flex gap-2 justify-center">
                                                <span
                                                    class="px-2 py-1 rounded bg-white text-[8px] font-bold text-gray-500 uppercase">PNG</span>
                                                <span
                                                    class="px-2 py-1 rounded bg-white text-[8px] font-bold text-gray-500 uppercase">JPG</span>
                                                <span
                                                    class="px-2 py-1 rounded bg-white text-[8px] font-bold text-gray-500 uppercase">WEBP</span>
                                            </div>
                                        </div>

                                        <!-- Selected File Preview -->
                                        <div id="drop-zone-preview" class="hidden w-full relative">
                                            <button onclick="resetFileSelection(event)"
                                                class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500 text-gray-900 flex items-center justify-center shadow-lg hover:bg-red-400 transition-colors z-20">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                            <img id="source-preview" src="" alt="Source"
                                                class="w-full h-48 object-contain rounded-xl border border-gray-200 bg-gray-50">
                                            <p id="media-filename-hint"
                                                class="text-[10px] text-gray-600 mt-3 font-medium truncate"></p>
                                            <p id="original-size" class="text-[10px] text-blue-400 font-bold mt-1">0 KB</p>
                                        </div>
                                    </div>

                                    <!-- Settings -->
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Convert
                                                to Format</label>
                                            <select id="target-format"
                                                class="w-full bg-white border border-gray-200 rounded-xl py-3.5 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all text-xs text-gray-800 appearance-none">
                                                <option value="png" class="bg-gray-900">PNG (Preserves Transparency)</option>
                                                <option value="jpg" class="bg-gray-900">JPG (Optimized for Photos)</option>
                                                <option value="webp" selected class="bg-gray-900">WebP (Modern Web Standard)
                                                </option>
                                            </select>
                                        </div>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center px-1">
                                                <label
                                                    class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Quality
                                                    Optimization</label>
                                                <span id="quality-value" class="text-[10px] font-bold text-blue-400">85%</span>
                                            </div>
                                            <input type="range" id="quality-slider" min="1" max="100" value="85"
                                                oninput="updateQualityValue(this.value)"
                                                class="w-full h-1.5 bg-gray-100 rounded-lg appearance-none cursor-pointer accent-blue-500">
                                        </div>
                                    </div>

                                    <div class="flex justify-end pt-2">
                                        <button id="convert-submit" onclick="convertMedia()"
                                            class="px-8 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-black uppercase tracking-[0.2em] hover:brightness-110 transition-all shadow-xl flex items-center justify-center gap-3">
                                            START CONVERSION
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Column: Results -->
                                <div class="lg:col-span-7">
                                    <div
                                        class="glass h-full rounded-xl border border-gray-200 bg-white flex flex-col relative overflow-hidden min-h-[400px]">
                                        <div id="media-loader"
                                            class="hidden absolute inset-0 bg-gray-50 backdrop-blur-md z-30 flex flex-col items-center justify-center space-y-4">
                                            <div
                                                class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin">
                                            </div>
                                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-[0.2em]">
                                                Processing Image...</p>
                                        </div>

                                        <div id="media-placeholder"
                                            class="flex-1 flex flex-col items-center justify-center text-center p-6">
                                            <div class="w-20 h-20 rounded-3xl bg-white flex items-center justify-center mb-6">
                                                <svg class="w-10 h-10 text-gray-900/10" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <p class="text-xs text-gray-600 font-medium tracking-wide">Converted image will
                                                appear here</p>
                                        </div>

                                        <div id="media-result" class="hidden flex-1 flex flex-col p-6">
                                            <div
                                                class="flex-1 rounded-2xl overflow-hidden bg-gray-50 border border-gray-200 shadow-inner group relative">
                                                <img id="media-preview" src="" alt="Converted"
                                                    class="w-full h-full object-contain">
                                                <div class="absolute top-4 right-4 px-3 py-1 rounded-lg bg-blue-600/80 backdrop-blur text-[8px] font-black text-white uppercase tracking-widest"
                                                    id="result-badge">WEBP</div>
                                            </div>

                                            <div
                                                class="mt-6 p-5 glass rounded-2xl border border-gray-200 flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p id="result-filename"
                                                            class="text-[10px] font-bold text-gray-700 truncate max-w-[200px]">
                                                            image_optimized.webp</p>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <span id="optimized-size"
                                                                class="text-[10px] font-black text-green-400">0 KB</span>
                                                            <span id="resolution-hint"
                                                                class="text-[8px] font-bold text-gray-600">0 x 0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a id="media-download" href="" download
                                                    class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-[10px] font-black text-gray-900 uppercase tracking-widest transition-all shadow-xl flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Feature -->
                    <div id="content-upload" class="feature-content">
                        <div class="max-w-4xl mx-auto glass p-6 md:p-6 rounded-xl  border border-gray-200 shadow-sm">
                            <div class="mb-8 mt-12">
                                <h2 class="text-xl font-bold mb-2">Blog Content Restructurer</h2>
                                <p class="text-xl text-gray-500">Re structure blog content.</p>
                            </div>

                            <form id="upload-form" class="space-y-6">


                                <div class="space-y-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Raw
                                            HTML Content</label>
                                        <button type="button" onclick="standardizeContent()" id="standardize-btn"
                                            class="text-[10px] font-black text-purple-400 hover:text-gray-900 transition-all uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Standardize with AI
                                        </button>
                                    </div>
                                    <textarea id="upload-content" rows="12"
                                        class="w-full bg-white border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all font-mono text-xs"
                                        placeholder="Paste generated HTML here..."></textarea>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="submit" id="upload-submit"
                                        class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs font-black uppercase tracking-[0.2em] hover:brightness-110 transition-all shadow-xl shadow-blue-900/20">
                                        Confirm and Push to Live
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="content-assistant" class="feature-content active">
                        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 lg:gap-8">
                            <div class="xl:col-span-1 space-y-6">
                                <div class="glass p-6 rounded-xl  space-y-6 border border-gray-200">
                                    <div>
                                        <h3 class="text-xl font-bold mb-2">Omni Chat</h3>
                                        <p class="text-sm text-gray-600 leading-relaxed">Fast, context-aware responses powered
                                            by
                                            {{ config('groq.api_key') ? 'Groq' : (config('gemini.api_key') ? 'Gemini 1.5' : 'GPT-4o') }}.
                                        </p>
                                    </div>
                                    <div class="space-y-3" style="padding: 10px;">
                                        <div style="padding: 10px;"
                                            class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-200">
                                            <span class="text-[10px] font-bold text-gray-600 uppercase">Provider</span>
                                            <span
                                                class="text-[10px] font-bold text-blue-400 uppercase">{{ config('groq.api_key') ? 'GROQ (Llama 3.3)' : (config('gemini.api_key') ? 'GOOGLE (Gemini)' : 'OPENAI') }}</span>
                                        </div>
                                        <div style="padding: 10px; margin-top: 6px;"
                                            class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-200">
                                            <span class="text-[10px] font-bold text-gray-600 uppercase">Streaming</span>
                                            <span
                                                class="text-[10px] font-bold text-green-400 uppercase tracking-widest">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="xl:col-span-3 space-y-6">
                                <div
                                    class="glass min-h-[400px] md:min-h-[500px] rounded-xl  flex flex-col glow-blue overflow-hidden border border-gray-200 shadow-sm">
                                    <div id="chat-history"
                                        class="flex-1 p-6 space-y-6 overflow-y-auto max-h-[65vh] md:max-h-[600px]">
                                        <!-- AI Greeting -->
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-blue-600/20 flex items-center justify-center flex-shrink-0 border border-blue-500/20">
                                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div style="padding: 10px; margin-bottom: 25px;"
                                                class="chat-bubble bg-white p-5 rounded-3xl rounded-tl-none text-sm text-gray-800 border border-gray-200 shadow-xl">
                                                Welcome back. I'm synchronized and ready to assist with your Laravel workflow.
                                                What shall we build today?
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                                        <form id="chat-form" class="relative group">
                                            <div class="flex items-center">
                                                <input type="text" id="chat-prompt" name="prompt"
                                                    placeholder="Type your request here..."
                                                    class="w-full bg-white border border-gray-200 rounded-2xl py-3 pl-12 md:pl-14 pr-24 md:pr-32 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all placeholder:text-gray-600">
                                                <!-- <svg class="w-5 h-5 text-gray-500 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> -->
                                                <button type="submit"
                                                    class="absolute right-2 top-2 bottom-2 px-4 md:px-8 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest hover:bg-blue-500 transition-all disabled:opacity-50 shadow-lg shadow-blue-600/20"
                                                    id="chat-submit">
                                                    Send
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Studio Feature -->
                    <div id="content-studio" class="feature-content">
                        <div class="max-w-5xl mx-auto space-y-12">
                            <div class="text-center space-y-4">
                                <h2 class="text-2xl font-black tracking-tighter">VISUAL <span
                                        class="gradient-text uppercase">Studio</span></h2>
                                <p class="text-gray-500 font-medium">Synthesize photorealistic imagery from pure metadata
                                    descriptions.</p>
                            </div>

                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 lg:gap-12 items-start">
                                <div id="image-canvas"
                                    class="image-preview min-h-[300px] md:min-h-[500px] glass flex flex-col items-center justify-center relative border border-gray-200 shadow-sm">
                                    <div id="image-placeholder" class="text-center space-y-4 opacity-20">
                                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-xs font-black uppercase tracking-[0.3em]">Imaging System Idle</p>
                                    </div>
                                    <img id="image-result" class="hidden w-full h-full object-cover">
                                    <div id="image-loader"
                                        class="absolute inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center hidden">
                                        <div class="text-center space-y-6">
                                            <div class="loader !w-12 !h-12 !border-4 !border-purple-500 !border-b-transparent">
                                            </div>
                                            <p class="text-[10px] font-black tracking-[0.5em] text-purple-400 animate-pulse">
                                                GENERATING PIXELS</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="glass p-6 rounded-xl  space-y-8 border border-gray-200">
                                    <form id="image-form" class="space-y-4">
                                        <!-- Style Presets -->
                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Style
                                                Preset</label>
                                            <div class="flex space-x-2 overflow-x-auto scrollbar-hide pb-2" id="style-presets">
                                                <button type="button" data-style="None"
                                                    class="style-btn active px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-blue-500/50 text-blue-400 transition-all">None</button>
                                                <button type="button" data-style="Photorealistic"
                                                    class="style-btn px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all">📸
                                                    Realistic</button>
                                                <button type="button" data-style="Digital Art"
                                                    class="style-btn px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all">🎨
                                                    Digital</button>
                                                <button type="button" data-style="3D Render"
                                                    class="style-btn px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all">🕹️
                                                    3D</button>
                                                <button type="button" data-style="Cinematic"
                                                    class="style-btn px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all">🎬
                                                    Film</button>
                                                <button type="button" data-style="Anime"
                                                    class="style-btn px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all">🏮
                                                    Anime</button>
                                            </div>
                                        </div>

                                        <!-- Aspect Ratio -->
                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Aspect
                                                Ratio</label>
                                            <div class="flex space-x-4" id="size-selection">
                                                <button type="button" data-size="1024x1024"
                                                    class="size-btn active p-2 rounded-lg bg-white border border-blue-500/50 text-blue-400 transition-all"
                                                    title="Square (1:1)">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <rect x="4" y="4" width="16" height="16" rx="2" />
                                                    </svg>
                                                </button>
                                                <button type="button" data-size="1792x1024"
                                                    class="size-btn p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all"
                                                    title="Landscape (16:9)">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <rect x="2" y="6" width="20" height="12" rx="2" />
                                                    </svg>
                                                </button>
                                                <button type="button" data-size="1024x1792"
                                                    class="size-btn p-2 rounded-lg bg-white border border-gray-200 text-gray-600 hover:text-gray-900 transition-all"
                                                    title="Portrait (9:16)">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                        <rect x="6" y="2" width="12" height="20" rx="2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Composition
                                                Description</label>
                                            <textarea id="image-prompt" rows="5"
                                                placeholder="Hyper-realistic portrait of a tech-wizard in a neon-lit cyberpunk library, cinematic lighting, 8k..."
                                                class="w-full bg-white border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all resize-none text-sm"></textarea>
                                        </div>
                                        <div class="flex justify-end pt-2">
                                            <button type="submit" id="image-submit"
                                                class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-xs font-black uppercase tracking-[0.2em] hover:brightness-110 transition-all shadow-xl shadow-purple-900/20">
                                                Invoke Generation
                                            </button>
                                        </div>
                                    </form>
                                    <div class="flex items-center space-x-4 opacity-40 grayscale pt-4">
                                        <span class="text-[10px] font-bold">POWERED BY</span>
                                        <div class="h-4 w-px bg-white/20"></div>
                                        <span class="text-[10px] font-black tracking-widest">DALL-E 3 ENGINE</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Writer Feature -->
                    <div id="content-writer" class="feature-content">
                        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-6">
                            <div class="lg:col-span-1 glass p-6 rounded-xl  space-y-8 border border-gray-200">
                                <div class="space-y-2">
                                    <h3 class="text-2xl font-bold">Blog <span class="gradient-text">Engine</span></h3>
                                    <p class="text-xs text-gray-500 font-medium">Generate high-quality blog posts with custom
                                        HTML structure and SEO optimization.</p>
                                </div>
                                <form id="writer-form" class="space-y-4">
                                    <div class="space-y-3">
                                        <label
                                            class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Drafting
                                            Parameters</label>
                                        <textarea id="writer-prompt" rows="8"
                                            placeholder="Draft a tech blog about Laravel 13 and AI integration... (AI will use custom HTML blog classes)"
                                            class="w-full bg-white border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all resize-none text-sm"></textarea>
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <button type="submit" id="writer-submit"
                                            class="px-8 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-blue-500 transition-all shadow-xl shadow-blue-900/20">
                                            Generate Draft
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="lg:col-span-2">
                                <div
                                    class="glass p-6 rounded-xl  min-h-[500px] relative border border-gray-200 shadow-sm overflow-hidden bg-white">
                                    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
                                        <div class="flex items-center space-x-6">
                                            <button onclick="toggleWriterView('preview')" id="writer-btn-preview"
                                                class="text-[10px] font-black text-blue-400 uppercase tracking-widest pb-1 border-b-2 border-blue-500">Preview</button>
                                            <button onclick="toggleWriterView('code')" id="writer-btn-code"
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest pb-1 border-b-2 border-transparent">HTML
                                                Source</button>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="sendToUpload()"
                                                class="px-4 py-2 rounded-lg bg-green-500/10 hover:bg-green-500/20 text-[10px] text-green-400 font-black transition-all border border-green-500/10 uppercase tracking-widest">Send
                                                to Upload</button>
                                            <button onclick="copyContent('writer-result')"
                                                class="px-4 py-2 rounded-lg bg-white hover:bg-gray-100 text-[10px] text-blue-400 font-black transition-all border border-gray-200 uppercase tracking-widest">Copy
                                                Content</button>
                                        </div>
                                    </div>
                                    <div id="writer-result-preview"
                                        class="markdown-body max-w-none text-sm selection:bg-blue-500/30">
                                        <p class="italic text-gray-600 animate-pulse">Drafting system awaiting instructions...
                                        </p>
                                    </div>
                                    <div id="writer-result-code"
                                        class="hidden text-gray-700 leading-relaxed font-mono whitespace-pre-wrap max-w-none text-xs selection:bg-blue-500/30">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coder Feature -->
                    <div id="content-coder" class="feature-content">
                        <div class="glass rounded-xl  overflow-hidden border border-gray-200 shadow-sm">
                            <div class="bg-white px-8 py-2.5 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-3.5 h-3.5 rounded-full bg-red-500/20 border border-red-500/40"></div>
                                    <div class="w-3.5 h-3.5 rounded-full bg-yellow-500/20 border border-yellow-500/40"></div>
                                    <div class="w-3.5 h-3.5 rounded-full bg-green-500/20 border border-green-500/40"></div>
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-[10px] font-black text-gray-500 uppercase tracking-widest">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                    <span>architect_shell.php</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 xl:grid-cols-2">
                                <div class="p-6 border-r border-gray-200">
                                    <div class="space-y-2 mb-8">
                                        <h3 class="text-2xl font-bold">Code <span class="gradient-text">Architect</span></h3>
                                        <p class="text-xs text-gray-500 font-medium">Precision-engineered code generation and
                                            debugging.</p>
                                    </div>
                                    <form id="coder-form" class="space-y-4">
                                        <div class="space-y-3">
                                            <label
                                                class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Logic
                                                Constraints</label>
                                            <textarea id="coder-prompt" rows="10"
                                                placeholder="Implement a robust Job batching system for processing large image datasets..."
                                                class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-3 px-4 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all font-mono text-sm placeholder:text-gray-700"></textarea>
                                        </div>
                                        <div class="flex justify-end pt-2">
                                            <button type="submit" id="coder-submit"
                                                class="px-8 py-2.5 rounded-xl bg-green-600 text-black text-xs font-black uppercase tracking-[0.2em] hover:bg-green-800   transition-all shadow-xl shadow-green-900/20">
                                                Execute Synth
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="p-6 bg-black/60 relative flex flex-col">
                                    <div class="flex justify-end space-x-2 mb-6">
                                        <div
                                            class="px-3 py-1 rounded bg-green-500/10 border border-green-500/20 text-[8px] font-bold text-green-400 uppercase tracking-widest">
                                            PHP 8.3</div>
                                        <div
                                            class="px-3 py-1 rounded bg-blue-500/10 border border-blue-500/20 text-[8px] font-bold text-blue-400 uppercase tracking-widest">
                                            Laravel 13</div>
                                    </div>
                                    <div id="coder-result"
                                        class="font-mono text-sm text-green-400/90 whitespace-pre-wrap leading-relaxed selection:bg-green-500/20 flex-1">
                                        <span class="text-gray-700">// Synthetic logic pending input...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endauth
            @endguest
        </main>

        <footer
            class="mt-16 md:mt-24 w-full max-w-7xl border-t border-gray-200 pt-8 md:pt-12 pb-8 md:pb-12 flex flex-col lg:flex-row justify-between items-center gap-6 text-center lg:text-left text-gray-600 text-[10px] font-black uppercase tracking-widest">
            <div class="flex items-center space-x-3">
                <span class="text-gray-600">&copy; {{ date('Y') }}</span>
                <span>Laravel AI Engine</span>
                <span class="w-1 h-1 rounded-full bg-gray-800"></span>
                <span class="text-blue-500/50">Core Version 2.0</span>
            </div>
            <div class="flex space-x-8">
                <a href="#" class="hover:text-blue-400 transition-colors">Documentation</a>
                <a href="#" class="hover:text-blue-400 transition-colors">Repository</a>
                <a href="#" class="hover:text-blue-400 transition-colors">Endpoint Status</a>
            </div>
        </footer>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css">
        <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
        <script>
            let currentStyle = 'None';
            let currentSize = '1024x1024';
            let chatHistory = []; // New: Store conversation context

            // Handle Style Selection
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.style-btn').forEach(b => {
                        b.classList.remove('active', 'border-blue-500/50', 'text-blue-400');
                        b.classList.add('border-white/10', 'text-gray-400');
                    });
                    btn.classList.add('active', 'border-blue-500/50', 'text-blue-400');
                    btn.classList.remove('border-white/10', 'text-gray-400');
                    currentStyle = btn.dataset.style;
                });
            });

            // Handle Size Selection
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.size-btn').forEach(b => {
                        b.classList.remove('active', 'border-blue-500/50', 'text-blue-400');
                        b.classList.add('border-white/10', 'text-gray-400');
                    });
                    btn.classList.add('active', 'border-blue-500/50', 'text-blue-400');
                    btn.classList.remove('border-white/10', 'text-gray-400');
                    currentSize = btn.dataset.size;

                    const canvas = document.getElementById('image-canvas');
                    if (currentSize === '1792x1024') canvas.style.aspectRatio = '16/9';
                    else if (currentSize === '1024x1792') canvas.style.aspectRatio = '9/16';
                    else canvas.style.aspectRatio = '1/1';
                });
            });

            function switchTab(tabId) {
                // Hide all content
                document.querySelectorAll('.feature-content').forEach(c => c.classList.remove('active'));
                // Deactivate all buttons
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('tab-active');
                    b.classList.add('text-gray-500');
                    b.classList.add('hover:bg-blue-50');
                    b.classList.add('hover:text-blue-600');
                });

                // Show active content
                document.getElementById('content-' + tabId).classList.add('active');
                const btn = document.getElementById('tab-' + tabId);
                btn.classList.add('tab-active');
                btn.classList.remove('text-gray-500');
                btn.classList.remove('hover:bg-blue-50');
                btn.classList.remove('hover:text-blue-600');
            }

            async function handleStreamingResponse(url, body, targetId, submitBtnId, isChat = false) {
                const target = document.getElementById(targetId);
                const btn = document.getElementById(submitBtnId);
                const originalBtnText = btn.innerText;

                btn.disabled = true;
                btn.innerText = 'WAIT...';

                let fullText = '';
                if (!isChat) target.innerHTML = '';
                else {
                    const userMsg = document.createElement('div');
                    userMsg.className = 'flex items-start gap-4 justify-end';
                    userMsg.innerHTML = `
                        <div style="margin-bottom: 5px; padding: 20px;" class="chat-bubble bg-blue-600 p-5 rounded-3xl rounded-tr-none text-sm text-white shadow-xl shadow-blue-900/20">
                            ${body.prompt}
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center flex-shrink-0 border border-blue-500/20">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    `;
                    target.appendChild(userMsg);

                    const aiMsg = document.createElement('div');
                    aiMsg.className = 'flex items-start gap-4';
                    aiMsg.innerHTML = `
                        <div class="w-10 h-10 rounded-xl bg-blue-600/20 flex items-center justify-center flex-shrink-0 border border-blue-500/20">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div style="margin-bottom: 10px;" class="chat-bubble markdown-body bg-white/5 p-5 rounded-3xl rounded-tl-none text-sm text-gray-200 border border-white/10 typing shadow-xl"></div>
                    `;
                    target.appendChild(aiMsg);
                    var textContainer = aiMsg.querySelector('.typing');
                    chatHistory.push({ role: 'user', content: body.prompt });
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(body)
                    });

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let buffer = '';

                    while (true) {
                        const { value, done } = await reader.read();
                        if (done) break;

                        buffer += decoder.decode(value, { stream: true });
                        const lines = buffer.split('\n');
                        buffer = lines.pop();

                        for (const line of lines) {
                            const trimmed = line.trim();
                            if (!trimmed || !trimmed.startsWith('data: ')) continue;
                            const data = trimmed.slice(6);
                            if (data === '[DONE]') break;

                            try {
                                const parsed = JSON.parse(data);
                                if (parsed.text) {
                                    fullText += parsed.text;
                                    const renderTarget = isChat ? textContainer : target;

                                    if (typeof marked !== 'undefined') {
                                        const html = marked.parse(fullText);
                                        renderTarget.innerHTML = html;
                                        if (body.mode === 'writer') {
                                            document.getElementById('writer-result-preview').innerHTML = html;
                                            document.getElementById('writer-result-code').innerText = fullText;
                                        }
                                    } else {
                                        renderTarget.innerText = fullText;
                                    }
                                    target.scrollTop = target.scrollHeight;
                                }
                            } catch (e) {
                                console.warn('Stream parse error:', e);
                            }
                        }
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                } finally {
                    btn.disabled = false;
                    btn.innerText = originalBtnText;
                    if (isChat) {
                        textContainer.classList.remove('typing');
                        chatHistory.push({ role: 'assistant', content: fullText });
                        if (chatHistory.length > 10) chatHistory.shift();
                    }
                    target.scrollTop = target.scrollHeight;
                    if (typeof Prism !== 'undefined') Prism.highlightAll();
                }
            }

            // Chat Form
            document.getElementById('chat-form').addEventListener('submit', (e) => {
                e.preventDefault();
                const promptInput = document.getElementById('chat-prompt');
                const prompt = promptInput.value;
                if (!prompt) return;
                handleStreamingResponse('/ai/chat', { prompt, history: chatHistory }, 'chat-history', 'chat-submit', true);
                promptInput.value = '';
            });

            // Writer Form
            document.getElementById('writer-form').addEventListener('submit', (e) => {
                e.preventDefault();
                const prompt = document.getElementById('writer-prompt').value;
                handleStreamingResponse('/ai/content', { prompt, mode: 'writer' }, 'writer-result', 'writer-submit');
            });

            // Coder Form
            document.getElementById('coder-form').addEventListener('submit', (e) => {
                e.preventDefault();
                const prompt = document.getElementById('coder-prompt').value;
                handleStreamingResponse('/ai/content', { prompt, mode: 'coder' }, 'coder-result', 'coder-submit');
            });

            // Image Form
            document.getElementById('image-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const prompt = document.getElementById('image-prompt').value;
                const placeholder = document.getElementById('image-placeholder');
                const resultImg = document.getElementById('image-result');
                const loader = document.getElementById('image-loader');
                const btn = document.getElementById('image-submit');

                btn.disabled = true;
                btn.innerText = 'SYNTHESIZING...';
                loader.classList.remove('hidden');
                placeholder.classList.add('hidden');
                resultImg.classList.add('hidden');

                try {
                    const response = await fetch('/ai/image', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            prompt,
                            style: typeof currentStyle !== 'undefined' ? currentStyle : 'None',
                            size: typeof currentSize !== 'undefined' ? currentSize : '1024x1024'
                        })
                    });
                    const data = await response.json();
                    if (data.url) {
                        resultImg.src = data.url;
                        resultImg.onload = () => {
                            resultImg.classList.remove('hidden');
                            loader.classList.add('hidden');
                        };
                    } else {
                        throw new Error(data.error || 'Failed');
                    }
                } catch (error) {
                    alert('Imaging Error: ' + error.message);
                    placeholder.classList.remove('hidden');
                    loader.classList.add('hidden');
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'INVOKE GENERATION';
                }
            });

            function toggleWriterView(view) {
                const preview = document.getElementById('writer-result-preview');
                const code = document.getElementById('writer-result-code');
                const btnPreview = document.getElementById('writer-btn-preview');
                const btnCode = document.getElementById('writer-btn-code');

                if (view === 'preview') {
                    preview.classList.remove('hidden');
                    code.classList.add('hidden');
                    btnPreview.className = 'text-[10px] font-black text-blue-400 uppercase tracking-widest pb-1 border-b-2 border-blue-500';
                    btnCode.className = 'text-[10px] font-black text-gray-500 uppercase tracking-widest pb-1 border-b-2 border-transparent';
                } else {
                    preview.classList.add('hidden');
                    code.classList.remove('hidden');
                    btnPreview.className = 'text-[10px] font-black text-gray-500 uppercase tracking-widest pb-1 border-b-2 border-transparent';
                    btnCode.className = 'text-[10px] font-black text-blue-400 uppercase tracking-widest pb-1 border-b-2 border-blue-500';
                }
            }

            function copyContent(id) {
                const targetId = (id === 'writer-result') ? 'writer-result-code' : id;
                const text = document.getElementById(targetId).innerText;
                navigator.clipboard.writeText(text).then(() => {
                    const btn = event.target;
                    const original = btn.innerText;
                    btn.innerText = 'COPIED!';
                    setTimeout(() => btn.innerText = original, 2000);
                });
            }

            function sendToUpload() {
                const htmlContent = document.getElementById('writer-result-code').innerText;
                if (!htmlContent || htmlContent.includes('Drafting system')) {
                    alert('Please generate content first!');
                    return;
                }

                // Set content in upload tab
                document.getElementById('upload-content').value = htmlContent;

                // Try to extract a title from the HTML (look for first <h2> or <h1>)
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlContent;
                const heading = tempDiv.querySelector('h1, h2, h3');
                if (heading) {
                    document.getElementById('upload-title').value = heading.innerText;
                }

                // Switch tab
                switchTab('upload');
            }

            async function standardizeContent() {
                const contentArea = document.getElementById('upload-content');
                const btn = document.getElementById('standardize-btn');
                const rawHtml = contentArea.value;

                if (!rawHtml) {
                    alert('Please paste some HTML first!');
                    return;
                }

                btn.disabled = true;
                btn.innerHTML = '✨ Standardizing...';
                contentArea.classList.add('animate-pulse');

                try {
                    const response = await fetch('/ai/standardize', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ html: rawHtml })
                    });

                    const data = await response.json();
                    if (data.standardized) {
                        contentArea.value = data.standardized;
                    } else {
                        alert('Cleanup failed: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Standardize error:', error);
                    alert('Server error. Check console.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Standardize with AI';
                    contentArea.classList.remove('animate-pulse');
                }
            }

            function updateQualityValue(val) {
                document.getElementById('quality-value').innerText = val + '%';
            }

            function handleFileSelect(e) {
                const file = e.target.files[0];
                if (!file) return;

                const prompt = document.getElementById('drop-zone-prompt');
                const preview = document.getElementById('drop-zone-preview');
                const sourceImg = document.getElementById('source-preview');
                const filename = document.getElementById('media-filename-hint');
                const originalSize = document.getElementById('original-size');

                prompt.classList.add('hidden');
                preview.classList.remove('hidden');

                sourceImg.src = URL.createObjectURL(file);
                filename.innerText = file.name;
                originalSize.innerText = (file.size / 1024).toFixed(1) + ' KB';
            }

            function resetFileSelection(e) {
                e.preventDefault();
                e.stopPropagation();

                const input = document.getElementById('media-input');
                const prompt = document.getElementById('drop-zone-prompt');
                const preview = document.getElementById('drop-zone-preview');

                input.value = '';
                prompt.classList.remove('hidden');
                preview.classList.add('hidden');

                // Clear results
                document.getElementById('media-result').classList.add('hidden');
                document.getElementById('media-placeholder').classList.remove('hidden');
            }

            async function convertMedia() {
                const input = document.getElementById('media-input');
                const format = document.getElementById('target-format').value;
                const quality = document.getElementById('quality-slider').value;
                const btn = document.getElementById('convert-submit');

                const loader = document.getElementById('media-loader');
                const placeholder = document.getElementById('media-placeholder');
                const result = document.getElementById('media-result');
                const preview = document.getElementById('media-preview');
                const downloadLink = document.getElementById('media-download');
                const optimizedSize = document.getElementById('optimized-size');
                const resultFilename = document.getElementById('result-filename');
                const resolutionHint = document.getElementById('resolution-hint');
                const resultBadge = document.getElementById('result-badge');

                if (!input.files || !input.files[0]) {
                    alert('Please select an image first!');
                    return;
                }

                const formData = new FormData();
                formData.append('image', input.files[0]);
                formData.append('format', format);
                formData.append('quality', quality);

                btn.disabled = true;
                btn.innerHTML = 'PROCESSING...';
                loader.classList.remove('hidden');

                try {
                    const response = await fetch('/ai/convert-image', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    });

                    const data = await response.json();
                    if (data.url) {
                        preview.src = data.url;
                        downloadLink.href = data.url;
                        resultFilename.innerText = data.filename;
                        resultBadge.innerText = format.toUpperCase();

                        placeholder.classList.add('hidden');
                        result.classList.remove('hidden');

                        // Fetch the new file to get its actual size
                        const res = await fetch(data.url);
                        const blob = await res.blob();
                        optimizedSize.innerText = (blob.size / 1024).toFixed(1) + ' KB';

                        // Get dimensions
                        const img = new Image();
                        img.onload = () => {
                            resolutionHint.innerText = `${img.width} x ${img.height}`;
                        };
                        img.src = data.url;

                    } else {
                        alert('Conversion failed: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Conversion error:', error);
                    alert('Server error during conversion.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = 'START CONVERSION <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>';
                    loader.classList.add('hidden');
                }
            }

            // Upload Form Submission
            document.getElementById('upload-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const btn = document.getElementById('upload-submit');
                const title = document.getElementById('upload-title').value;
                const category = document.getElementById('upload-category').value;
                const content = document.getElementById('upload-content').value;

                if (!title || !content) {
                    alert('Title and Content are required!');
                    return;
                }

                btn.disabled = true;
                btn.innerText = 'UPLOADING...';

                try {
                    // Note: We'll need to define this route or handle it in AIController
                    const response = await fetch('/ai/blog-upload', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title, category, content })
                    });

                    const data = await response.json();
                    if (data.success) {
                        alert('Blog post uploaded successfully!');
                    } else {
                        alert('Upload failed: ' + (data.error || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Server error during upload. Please check console.');
                } finally {
                    btn.disabled = false;
                    btn.innerText = 'Confirm and Push to Live';
                }
            });
        </script>
    </main>
</body>

</html>