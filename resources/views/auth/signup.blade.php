<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>সাইন আপ | JLPT N3 Vocabulary Tracker</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Fonts: Hind Siliguri, Noto Sans Bengali & Noto Sans JP -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet" />

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Hind Siliguri"', '"Noto Sans Bengali"', 'sans-serif'],
            jp: ['"Noto Sans JP"', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    body { -webkit-tap-highlight-color: transparent; }
    .bg-grid {
      background-image:
        linear-gradient(to right, rgba(148, 163, 184, 0.15) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(148, 163, 184, 0.15) 1px, transparent 1px);
      background-size: 44px 44px;
    }
    .jp-watermark {
      writing-mode: vertical-rl;
      letter-spacing: 0.35em;
    }
    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {
      -webkit-text-fill-color: #0f172a;
      -webkit-box-shadow: 0 0 0 1000px #ffffff inset;
      transition: background-color 9999s ease-in-out 0s;
    }
  </style>
</head>

<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">

  <!-- Background decoration -->
  <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute inset-0 bg-grid opacity-60"></div>
    <div class="absolute -top-32 -left-24 h-96 w-96 rounded-full bg-sky-200/35 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-20 h-[28rem] w-[28rem] rounded-full bg-amber-200/50 blur-3xl"></div>
  </div>

  <main class="min-h-screen flex items-center justify-center px-4 py-8 sm:px-6 sm:py-12">

    <div class="w-full max-w-5xl bg-white rounded-[2rem] shadow-2xl shadow-slate-900/10 ring-1 ring-slate-900/5 overflow-hidden grid lg:grid-cols-2">

      <!-- ===== Brand Panel ===== -->
      <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-sky-300/90 via-sky-400/75 to-sky-600/70 text-slate-900 p-10">
        <!-- Decorative circles -->
        <div class="absolute -top-24 -right-16 h-72 w-72 rounded-full border border-white/10"></div>
        <div class="absolute -top-10 -right-2 h-44 w-44 rounded-full border border-white/10"></div>
        <div class="absolute bottom-10 -left-16 h-64 w-64 rounded-full bg-white/5 blur-2xl"></div>
        <span class="jp-watermark absolute right-6 top-1/2 -translate-y-1/2 text-6xl font-jp font-bold text-white/10 select-none">はじめ</span>

        <div class="relative">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-white/15 border border-white/25 flex items-center justify-center text-xl font-bold font-jp backdrop-blur">
              語彙
            </div>
            <span class="px-3 py-1 text-xs font-semibold bg-amber-300 text-amber-950 rounded-full">
              ৬০ দিন • ৬০০ শব্দ
            </span>
          </div>

          <h1 class="mt-8 text-3xl font-bold leading-snug tracking-tight">
            JLPT N3<br />
            Vocabulary Tracker <br/> <br>
            100% Free Membership 
            <br> Daily Mock Test
          </h1>
          <p class="mt-3 text-sm leading-relaxed text-sky-950/80">
            ফ্রি অ্যাকাউন্ট খুলে প্রতিদিন ১০টি শব্দ, ডেইলি মক টেস্ট ও
            ফাইনাল এক্সামের মাধ্যমে N3 প্রস্তুতি শুরু করুন।
          </p>
        </div>

        <ul class="relative mt-6 space-y-4 text-sm">
          <li class="flex items-start gap-3">
            <span class="mt-0.5 h-6 w-6 shrink-0 rounded-lg bg-emerald-400/20 border border-emerald-300/40 flex items-center justify-center text-emerald-300 text-xs">✓</span>
            <span class="text-slate-800">৬০ দিন × ১০ শব্দ = ৬০০টি N3 ভোকাবুলারি</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="mt-0.5 h-6 w-6 shrink-0 rounded-lg bg-emerald-400/20 border border-emerald-300/40 flex items-center justify-center text-emerald-300 text-xs">✓</span>
            <span class="text-slate-800">প্রতিদিনের মক টেস্ট ও নিজের প্রোগ্রেস ট্র্যাকিং</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="mt-0.5 h-6 w-6 shrink-0 rounded-lg bg-emerald-400/20 border border-emerald-300/40 flex items-center justify-center text-emerald-300 text-xs">✓</span>
            <span class="text-slate-800">বাংলা বা 日本語 — নিজের পছন্দের ভাষায় অর্থ</span>
          </li>
        </ul>

        <p class="relative mt-10 text-xs text-sky-950/70 border-t border-white/25 pt-5">
          নিয়মিত চর্চা ও মক টেস্টের মাধ্যমে N3 প্রস্তুতি সম্পন্ন করুন।
        </p>
      </aside>

      <!-- ===== Form Panel ===== -->
      <section class="p-6 sm:p-10 flex flex-col justify-center">

        <!-- Mobile brand -->
        <div class="lg:hidden flex items-center gap-3 mb-8">
          <div class="h-11 w-11 rounded-2xl bg-sky-300/70 text-sky-950 flex items-center justify-center text-lg font-bold font-jp shadow-lg shadow-sky-300/30">
            語彙
          </div>
          <div>
            <p class="text-sm font-bold leading-tight">JLPT N3 Vocabulary Tracker</p>
            <p class="text-xs text-slate-500">৬০ দিন • ৬০০ শব্দ</p>
          </div>
        </div>

        <div class="mb-8">
          <span class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-sky-700">
            <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
            Create Account
          </span>
          <h2 class="mt-3 text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">অ্যাকাউন্ট খুলুন</h2>
          <p class="mt-2 text-sm text-slate-500">নিচের তথ্যগুলো দিয়ে আপনার ফ্রি অ্যাকাউন্ট তৈরি করুন।</p>
        </div>

        @if ($errors->any())
          <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 flex items-start gap-2">
            <span class="mt-0.5">⚠️</span>
            <span>অনুগ্রহ করে নিচের তথ্যগুলো সঠিকভাবে দিন।</span>
          </div>
        @endif

        <form method="POST" action="{{ route('signup') }}" class="space-y-5">
          @csrf

          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">নাম</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="8" r="4"></circle>
                  <path d="M4 21a8 8 0 0 1 16 0"></path>
                </svg>
              </span>
              <input
                id="name"
                type="text"
                name="name"
                autocomplete="name"
                placeholder="আপনার নাম"
                value="{{ old('name') }}"
                required
                autofocus
                class="w-full rounded-xl border bg-white py-3 pl-12 pr-4 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                       focus:ring-4 focus:ring-sky-400/20 focus:border-sky-400
                       {{ $errors->has('name') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }}"
              >
            </div>
            @error('name')
              <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Phone -->
          <div>
            <label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">মোবাইল নম্বর</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="7" y="2" width="10" height="20" rx="2.5"></rect>
                  <path d="M11 18h2"></path>
                </svg>
              </span>
              <input
                id="phone"
                type="tel"
                name="phone"
                inputmode="numeric"
                autocomplete="tel"
                placeholder="01XXXXXXXXX"
                value="{{ old('phone') }}"
                required
                class="w-full rounded-xl border bg-white py-3 pl-12 pr-4 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                       focus:ring-4 focus:ring-sky-400/20 focus:border-sky-400
                       {{ $errors->has('phone') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }}"
              >
            </div>
            @error('phone')
              <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">পাসওয়ার্ড</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="4" y="10" width="16" height="11" rx="2.5"></rect>
                  <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                </svg>
              </span>
              <input
                id="password"
                type="password"
                name="password"
                autocomplete="new-password"
                placeholder="কমপক্ষে ৬ অক্ষর"
                required
                class="w-full rounded-xl border bg-white py-3 pl-12 pr-12 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                       focus:ring-4 focus:ring-sky-400/20 focus:border-sky-400
                       {{ $errors->has('password') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }}"
              >
              <button type="button" data-toggle-password="password" aria-label="পাসওয়ার্ড দেখান"
                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-sky-600 transition">
                <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                  <circle cx="12" cy="12" r="2.5"></circle>
                </svg>
                <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 3l18 18"></path>
                  <path d="M10.6 6.2A9.8 9.8 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3 3.7"></path>
                  <path d="M6.2 6.7A17 17 0 0 0 2 12s3.5 6 10 6a9.9 9.9 0 0 0 4.2-.9"></path>
                  <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>
                </svg>
              </button>
            </div>
            @error('password')
              <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password confirmation -->
          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">পাসওয়ার্ড নিশ্চিত করুন</label>
            <div class="relative">
              <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="4" y="10" width="16" height="11" rx="2.5"></rect>
                  <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                </svg>
              </span>
              <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                placeholder="আবার পাসওয়ার্ড লিখুন"
                required
                class="w-full rounded-xl border bg-white py-3 pl-12 pr-12 text-slate-900 placeholder-slate-400 shadow-sm outline-none transition
                       focus:ring-4 focus:ring-sky-400/20 focus:border-sky-400
                       {{ $errors->has('password') ? 'border-rose-400 ring-4 ring-rose-500/10' : 'border-slate-200' }}"
              >
              <button type="button" data-toggle-password="password_confirmation" aria-label="পাসওয়ার্ড দেখান"
                class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-sky-600 transition">
                <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6Z"></path>
                  <circle cx="12" cy="12" r="2.5"></circle>
                </svg>
                <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 3l18 18"></path>
                  <path d="M10.6 6.2A9.8 9.8 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-3 3.7"></path>
                  <path d="M6.2 6.7A17 17 0 0 0 2 12s3.5 6 10 6a9.9 9.9 0 0 0 4.2-.9"></path>
                  <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Preferred language -->
          <div>
            <span class="block text-sm font-semibold text-slate-700 mb-2">শব্দের অর্থের ভাষা</span>
            <div class="grid grid-cols-2 gap-3">
              <label class="cursor-pointer">
                <input type="radio" name="preferred_lang" value="bn" class="peer sr-only" {{ old('preferred_lang', 'bn') === 'bn' ? 'checked' : '' }}>
                <span class="block rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-600 shadow-sm transition
                             peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:text-sky-700
                             peer-focus:ring-4 peer-focus:ring-sky-400/20 hover:border-sky-300">
                  বাংলা
                </span>
              </label>
              <label class="cursor-pointer">
                <input type="radio" name="preferred_lang" value="jp" class="peer sr-only" {{ old('preferred_lang') === 'jp' ? 'checked' : '' }}>
                <span class="block rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-600 shadow-sm transition
                             peer-checked:border-sky-400 peer-checked:bg-sky-50 peer-checked:text-sky-700
                             peer-focus:ring-4 peer-focus:ring-sky-400/20 hover:border-sky-300 font-jp">
                  日本語
                </span>
              </label>
            </div>
            @error('preferred_lang')
              <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Submit -->
          <button type="submit"
            class="group w-full rounded-xl bg-sky-400/90 px-6 py-3.5 text-sm sm:text-base font-bold text-sky-950 shadow-lg shadow-sky-400/25 outline-none transition
                   hover:bg-sky-300/90 hover:shadow-sky-400/35 hover:-translate-y-0.5
                   focus:ring-4 focus:ring-sky-400/30 active:translate-y-0">
            <span class="inline-flex items-center justify-center gap-2">
              অ্যাকাউন্ট তৈরি করুন
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="m13 6 6 6-6 6"></path>
              </svg>
            </span>
          </button>
        </form>

        <!-- Divider -->
        <div class="my-7 flex items-center gap-4">
          <span class="h-px flex-1 bg-slate-200"></span>
          <span class="text-xs font-medium text-slate-400">অথবা</span>
          <span class="h-px flex-1 bg-slate-200"></span>
        </div>

        <!-- Login CTA -->
        <p class="text-center text-sm text-slate-500">
          ইতিমধ্যে অ্যাকাউন্ট আছে?
          <a href="{{ route('login') }}" class="ml-1 font-bold text-sky-700 hover:text-sky-900 underline decoration-sky-200 underline-offset-4 transition">
            লগইন করুন
          </a>
        </p>

        <p class="mt-8 text-center text-xs text-slate-400">
          © {{ date('Y') }} JLPT N3 Vocabulary Tracker · Developed by Shovon
        </p>
      </section>
    </div>
  </main>

  <script>
    (function () {
      document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
          var input = document.getElementById(button.getAttribute('data-toggle-password'));
          if (!input) return;
          var show = input.type === 'password';
          input.type = show ? 'text' : 'password';
          var open = button.querySelector('[data-eye-open]');
          var closed = button.querySelector('[data-eye-closed]');
          if (open) open.classList.toggle('hidden', show);
          if (closed) closed.classList.toggle('hidden', !show);
          button.setAttribute('aria-label', show ? 'পাসওয়ার্ড লুকান' : 'পাসওয়ার্ড দেখান');
        });
      });
    })();
  </script>
</body>
</html>