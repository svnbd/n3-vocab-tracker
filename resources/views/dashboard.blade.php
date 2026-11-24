<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="mock-test-url" content="{{ route('progress.mock') }}">
  <meta name="progress-complete-url" content="{{ route('progress.complete') }}">
  <meta name="progress-reset-url" content="{{ route('progress.reset') }}">
  <title>JLPT N3 Vocabulary Tracker | N3 ভোকাবুলারি স্টাডি অ্যাপ</title>
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
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

  <!-- Toast Notification Box (No alert/confirm used) -->
  <div id="toast-box" class="fixed top-4 right-4 z-50 hidden max-w-sm bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 transition-all duration-300">
    <span id="toast-icon" class="text-xl shrink-0">🔔</span>
    <p id="toast-message" class="text-sm font-medium leading-snug"></p>
  </div>

  <!-- Confirmation Modal Box -->
  <div id="confirm-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
      <h3 id="confirm-title" class="text-lg font-bold text-slate-900 mb-2">নিশ্চিত করুন</h3>
      <p id="confirm-desc" class="text-sm text-slate-600 mb-6 leading-relaxed"></p>
      <div class="flex justify-end gap-3">
        <button id="confirm-cancel-btn" type="button" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition">বাতিল</button>
        <button id="confirm-ok-btn" type="button" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm transition">হ্যাঁ, রিসেট করুন</button>
      </div>
    </div>
  </div>

  <!-- Header Section -->
  <header class="bg-sky-100/80 backdrop-blur-md border-b border-sky-200/80 text-slate-800 shadow-lg sticky top-0 z-30">
    <div class="max-w-6xl mx-auto px-4 py-4">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Brand & Bilingual Toggle -->
        <div class="flex items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-indigo-500/30 border border-indigo-400/40 flex items-center justify-center text-xl font-bold font-jp shrink-0">
              語彙
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <h1 id="app-title" class="text-lg sm:text-2xl font-bold tracking-tight">JLPT N3 Vocabulary Tracker</h1>
                <span id="header-badge" class="px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200 rounded-full">৬০ দিন • ৬০০ শব্দ</span>
              </div>
              <p id="app-subtitle" class="text-xs sm:text-sm text-gray-600">N3 ভোকাবুলারি স্টাডি অ্যাপ — প্রতিদিন ১০টি শব্দ, মক টেস্ট ও ফাইনাল এক্সাম</p>
            </div>
          </div>

          <!-- Two-Mode Language Toggle (বাংলা / 日本語) -->
          <div class="flex items-center gap-2 shrink-0">
            <div role="group" aria-label="Language Switcher" class="flex items-center bg-slate-950/80 p-1 rounded-2xl border border-white/20 shadow-inner">
              <button id="lang-bn-btn" type="button" class="px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition bg-white text-slate-950 shadow">
                বাংলা (BN)
              </button>
              <button id="lang-ja-btn" type="button" class="px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition text-indigo-200 hover:text-white font-jp">
                日本語 (JP)
              </button>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex items-center gap-2">
              @csrf
              <span class="max-w-[120px] truncate text-xs sm:text-sm font-medium text-red-600" title="{{ auth()->user()->name }}">
                {{ auth()->user()->name }}
              </span>
              <button type="submit" class="px-3 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold transition shadow-sm">
                লগআউট
              </button>
            </form>
          </div>
        </div>

        <!-- Dynamic Progress Bar (% Complete) -->
        <div class="bg-gray-100 border border-gray-200 rounded-2xl p-3 sm:px-4 sm:py-2.5 min-w-[270px] sm:min-w-[330px]">
          <div class="flex items-center justify-between text-xs sm:text-sm mb-1.5 gap-2">
            <span id="progress-label" class="font-medium text-gray-600">মোট অগ্রগতি:</span>
            <span id="progress-text" class="font-bold text-emerald-700">০% সম্পন্ন (০/৬০ দিন • ০/৬০০ শব্দ)</span>
          </div>
          <div class="w-full h-2.5 bg-slate-800/80 rounded-full overflow-hidden">
            <div id="progress-bar" class="h-full bg-gradient-to-r from-emerald-400 to-teal-300 rounded-full transition-all duration-500" style="width: 0%"></div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Workspace -->
  <main class="max-w-6xl mx-auto w-full px-4 py-6 flex-1 space-y-6">

    <!-- Top Control Panel: Day Toggle System & Study Timer Selector -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-5">
      
      <!-- Day Toggle Card (7 cols) -->
      <div class="lg:col-span-7 bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between gap-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>
            <span id="day-kicker" class="text-xs font-bold uppercase tracking-wider text-indigo-600">Day Toggle System</span>
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2 flex-wrap">
              <span id="active-day-title">Day 1 (দিন ১)</span>
              <span id="day-status-badge" class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">চলমান</span>
            </h2>
          </div>

          <!-- Prev / Dropdown / Next Controls -->
          <div class="flex items-center gap-2 flex-wrap">
            <button id="prev-day-btn" type="button" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition disabled:opacity-40">
              ◀ আগের দিন
            </button>
            <select id="day-select" aria-label="Select Day" class="px-3 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 font-bold text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
              <!-- Populated dynamically -->
            </select>
            <button id="next-day-btn" type="button" class="px-3 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition disabled:opacity-40">
              পরের দিন ▶
            </button>
          </div>
        </div>

        <!-- Quick Day Toggle Grid (Collapsible / Scrollable 1 to 60 Pills) -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <span id="quick-day-label" class="text-xs font-semibold text-slate-500">দ্রুত দিন পরিবর্তন করুন (Day 1 – 60):</span>
            <button id="toggle-grid-btn" type="button" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 underline">সব ৬০ দিন দেখুন / লুকান</button>
          </div>
          <div id="day-pills-container" class="flex gap-1.5 overflow-x-auto pb-2">
            <!-- Day 1-60 pills generated dynamically -->
          </div>
        </div>
      </div>

      <!-- Study Timer Selector Card (5 cols) -->
      <div class="lg:col-span-5 bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between gap-4">
        <div class="flex items-center justify-between gap-2">
          <div>
            <span id="timer-kicker" class="text-xs font-bold uppercase tracking-wider text-amber-600">Study Timer</span>
            <h2 id="timer-heading" class="text-lg font-bold text-slate-900">দৈনিক অধ্যয়নের সময়</h2>
          </div>
          <!-- 5 / 10 / 15 Min Selector Buttons -->
          <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
            <button type="button" data-mins="5" class="timer-preset-btn px-3 py-1.5 rounded-lg text-xs font-bold transition bg-indigo-600 text-white shadow-sm">৫ মি.</button>
            <button type="button" data-mins="10" class="timer-preset-btn px-3 py-1.5 rounded-lg text-xs font-bold transition text-slate-600 hover:text-slate-900">১০ মি.</button>
            <button type="button" data-mins="15" class="timer-preset-btn px-3 py-1.5 rounded-lg text-xs font-bold transition text-slate-600 hover:text-slate-900">১৫ মি.</button>
          </div>
        </div>

        <!-- Timer Display & Action Buttons -->
        <div class="flex items-center justify-between bg-slate-900 text-white rounded-2xl px-5 py-3.5">
          <div>
            <p id="timer-remaining-label" class="text-[11px] text-slate-400 uppercase tracking-wider">অবশিষ্ট সময়</p>
            <div id="timer-display" class="text-3xl font-extrabold tracking-wider font-mono text-amber-300">05:00</div>
          </div>
          <div class="flex items-center gap-2">
            <button id="timer-start-btn" type="button" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-sm transition shadow">
              ▶ শুরু
            </button>
            <button id="timer-pause-btn" type="button" class="hidden px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold text-sm transition shadow">
              ⏸ বিরতি
            </button>
            <button id="timer-reset-btn" type="button" class="px-3 py-2.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 font-semibold text-sm transition" title="রিসেট">
              ↺
            </button>
          </div>
        </div>
      </div>

    </section>

    <!-- Mode Navigation Tabs -->
    <nav class="flex flex-wrap items-center justify-between gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-200">
      <div class="flex flex-wrap items-center gap-2">
        <button id="tab-study" type="button" class="mode-tab px-4 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 text-white shadow-sm transition">
          📖 স্টাডি মোড (১০টি শব্দ)
        </button>
        <button id="tab-mock" type="button" class="mode-tab px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition">
          📝 ডেইলি মক টেস্ট (Mock Test)
        </button>
        <button id="tab-exam" type="button" class="mode-tab px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5">
          <span id="tab-exam-label">🏆 ফাইনাল মেগা এক্সাম (Day 1–60)</span>
          <span id="exam-lock-icon" class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">0/60</span>
        </button>
      </div>

      <!-- Reset Progress Button -->
      <button id="reset-all-progress-btn" type="button" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
        প্রোগ্রেস রিসেট
      </button>
    </nav>

    <!-- VIEW 1: STUDY MODE (10 Words per Day) -->
    <section id="view-study" class="space-y-5">
      <!-- Study Action Toolbar -->
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2 flex-wrap">
          <button id="toggle-meanings-btn" type="button" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition flex items-center gap-2">
            👁️ অর্থ লুকান / দেখুন (Self-Test)
          </button>
          <button id="toggle-kanji-btn" type="button" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition">
            🈳 কঞ্জি লুকান / দেখুন
          </button>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <button id="mark-complete-btn" type="button" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition flex items-center gap-2">
            ✅ Day Complete মার্ক করুন
          </button>
          <button id="start-day-mock-cta" type="button" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-sm transition flex items-center gap-2">
            🎯 মক টেস্ট দিন →
          </button>
        </div>
      </div>

      <!-- 10 Vocabulary Cards Grid -->
      <div id="vocab-cards-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Populated dynamically with 10 words for the active Day -->
      </div>
    </section>

    <!-- VIEW 2: DAILY MOCK TEST -->
    <section id="view-mock" class="hidden space-y-5">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
          <div>
            <span id="mock-kicker" class="text-xs font-bold uppercase tracking-wider text-indigo-600">Daily Mock Test</span>
            <h2 id="mock-header-title" class="text-xl font-bold text-slate-900">Day 1 — ভোকাবুলারি মক টেস্ট (১০টি প্রশ্ন)</h2>
            <p id="mock-subtitle" class="text-sm text-slate-500">সঠিক উত্তরটি নির্বাচন করুন। সবগুলো উত্তর দিলে ফলাফল দেখতে পাবেন।</p>
          </div>
          <button id="restart-mock-btn" type="button" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition self-start">
            🔄 নতুন করে প্রশ্ন সাজান
          </button>
        </div>

        <!-- Quiz Questions Container -->
        <div id="mock-questions-list" class="mt-6 space-y-6">
          <!-- 10 MCQ questions generated dynamically -->
        </div>

        <!-- Submit & Score Box -->
        <div class="mt-8 pt-5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div id="mock-result-banner" class="hidden w-full sm:w-auto flex-1 bg-indigo-50 border border-indigo-200 rounded-2xl p-4">
            <p id="mock-score-text" class="text-lg font-bold text-indigo-950"></p>
            <p id="mock-feedback-text" class="text-sm text-indigo-700 mt-0.5"></p>
          </div>
          <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <button id="submit-mock-btn" type="button" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow transition">
              ফলাফল যাচাই করুন (Submit)
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- VIEW 3: GRAND FINAL EXAM (DAY 1 - 60) -->
    <section id="view-exam" class="hidden space-y-5">
      <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <!-- Exam Status Banner -->
        <div id="exam-status-header" class="rounded-2xl p-5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
          <div>
            <div class="flex items-center gap-2">
              <span class="text-2xl">🎓</span>
              <h2 id="exam-main-title" class="text-xl font-bold text-slate-900">JLPT N3 গ্র্যান্ড ফাইনাল এক্সাম (Day 1 – Day 60)</h2>
            </div>
            <p id="exam-unlock-desc" class="text-sm text-slate-600 mt-1">
              ৬০ দিনের ৬০০টি শব্দ থেকে বাছাইকৃত ২০টি প্রশ্নের চূড়ান্ত পরীক্ষা। (পাস মার্ক: ১৬/২০ বা ৮০%)
            </p>
          </div>
          <div class="flex items-center gap-3">
            <button id="start-final-exam-btn" type="button" class="px-5 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow transition">
              🚀 ফাইনাল এক্সাম শুরু করুন
            </button>
          </div>
        </div>

        <!-- Final Exam Questions Area -->
        <div id="final-exam-body" class="hidden space-y-6">
          <div class="flex items-center justify-between bg-slate-900 text-white px-5 py-3 rounded-2xl">
            <span id="exam-q-count-label" class="text-sm font-semibold">মোট প্রশ্ন: ২০টি (Day 1 – 60)</span>
            <span id="exam-timer-display" class="font-mono font-bold text-amber-300 text-lg">সময়: 10:00</span>
          </div>

          <div id="final-exam-questions" class="space-y-6"></div>

          <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div id="final-exam-result" class="hidden flex-1 rounded-2xl p-5 border">
              <h3 id="final-exam-grade" class="text-xl font-bold"></h3>
              <p id="final-exam-summary" class="text-sm mt-1"></p>
            </div>
            <button id="submit-final-exam-btn" type="button" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow transition">
              চূড়ান্ত পরীক্ষা জমা দিন (Finish Exam)
            </button>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <footer id="app-footer" class="bg-white border-t border-slate-200 mt-10 py-4 px-4 text-center text-xs text-slate-500">
    JLPT N3 Vocabulary Tracker • ৬০ দিন × ১০ শব্দ = ৬০০টি N3 ভোকাবুলারি • নিয়মিত চর্চা ও মক টেস্টের মাধ্যমে N3 প্রস্তুতি সম্পন্ন করুন।
    Developed by Md.Shovon
  </footer>

  <script>
    (function () {
      "use strict";

      // =========================================================================
      // 1. COMPLETE 600 AUTHENTIC JLPT N3 VOCABULARY DATASET (60 DAYS x 10 WORDS)
      // Format: [reading (hiragana/katakana), kanji (or "" if katakana-only), bangla_meaning]
      // =========================================================================
      const RAW_VOCAB_LIST = [
        // DAY 1 (1-10)
        ["たかい", "高い", "দামী"],
        ["やすい", "安い", "সস্তা"],
        ["ふるい", "古い", "পুরনো"],
        ["おおきい", "大きい", "বড়"],
        ["ちいさい", "小さい", "ছোট"],
        ["あたらしい", "新しい", "নতুন"],
        ["いそがしい", "忙しい", "ব্যস্ত"],
        ["むずかしい", "難しい", "কঠিন"],
        ["やさしい", "易しい", "সহজ / দয়ালু"],
        ["おもしろい", "面白い", "মজাদার / আকর্ষণীয়"],

        // DAY 2 (11-20)
        ["つまらない", "詰まらない", "বিরক্তিকর / একঘেয়ে"],
        ["あぶない", "危ない", "বিপজ্জনক"],
        ["あんぜん", "安全", "নিরাপদ"],
        ["べんり", "便利", "সুবিধাজনক"],
        ["ふべん", "不便", "অসুবিধাজনক"],
        ["たいせつ", "大切", "গুরুত্বপূর্ণ / মূল্যবান"],
        ["ひつよう", "必要", "প্রয়োজনীয়"],
        ["ふよう", "不要", "অপ্রয়োজনীয়"],
        ["じゅうぶん", "十分", "যথেষ্ট"],
        ["ふそく", "不足", "অভাব / ঘাটতি"],

        // DAY 3 (21-30)
        ["けいけん", "経験", "অভিজ্ঞতা"],
        ["きかい", "機会", "সুযোগ"],
        ["けいかく", "計画", "পরিকল্পনা"],
        ["じゅんび", "準備", "প্রস্তুতি"],
        ["よてい", "予定", "নির্ধারিত সূচি / পরিকল্পনা"],
        ["やくそく", "約束", "প্রতিশ্রুতি / অ্যাপয়েন্টমেন্ট"],
        ["れんらく", "連絡", "যোগাযোগ"],
        ["そうだん", "相談", "পরামর্শ / আলোচনা"],
        ["せつめい", "説明", "ব্যাখ্যা"],
        ["しつもん", "質問", "প্রশ্ন"],

        // DAY 4 (31-40)
        ["へんじ", "返事", "উত্তর / জবাব"],
        ["あんない", "案内", "পথনির্দেশ / গাইড"],
        ["しょうかい", "紹介", "পরিচয় করিয়ে দেওয়া"],
        ["しょうたい", "招待", "আমন্ত্রণ / নিমন্ত্রণ"],
        ["さんか", "参加", "অংশগ্রহণ"],
        ["しゅっせき", "出席", "উপস্থিতি"],
        ["けっせき", "欠席", "অনুপস্থিতি"],
        ["ちこく", "遅刻", "দেরি করে আসা"],
        ["そうたい", "早退", "সময়ের আগে ছুটি নেওয়া"],
        ["きゅうけい", "休憩", "বিশ্রাম / বিরতি"],

        // DAY 5 (41-50)
        ["かいぎ", "会議", "সভা / মিটিং"],
        ["しりょう", "資料", "নথিপত্র / ডকুমেন্ট"],
        ["しょるい", "書類", "কাগজপত্র / ফাইল"],
        ["ほうこく", "報告", "প্রতিবেদন / রিপোর্ট"],
        ["かくにん", "確認", "নিশ্চিতকরণ / যাচাই"],
        ["へんこう", "変更", "পরিবর্তন"],
        ["ちゅうし", "中止", "বাতিল / স্থগিত"],
        ["えんき", "延期", "মুলতবি / তারিখ পেছানো"],
        ["せいこう", "成功", "সফলতা"],
        ["しっぱい", "失敗", "ব্যর্থতা"],

        // DAY 6 (51-60)
        ["どりょく", "努力", "কঠোর পরিশ্রম"],
        ["おうえん", "応援", "উৎসাহ / সমর্থন"],
        ["きょうりょく", "協力", "সহযোগিতা"],
        ["きょうそう", "競争", "প্রতিযোগিতা"],
        ["しあい", "試合", "খেলা / ম্যাচ"],
        ["ゆうしょう", "優勝", "চ্যাম্পিয়ন / বিজয়"],
        ["れんしゅう", "練習", "অনুশীলন"],
        ["ふくしゅう", "復習", "পুনরাবৃত্তি / রিভিশন"],
        ["よしゅう", "予習", "পূর্বপ্রস্তুতি (পড়া)"],
        ["しけん", "試験", "পরীক্ষা"],

        // DAY 7 (61-70)
        ["ごうかく", "合格", "পরীক্ষায় উত্তীর্ণ হওয়া"],
        ["ふごうかく", "不合格", "অনুত্তীর্ণ / ফেল"],
        ["せいせき", "成績", "ফলাফল / রেজাল্ট"],
        ["てんすう", "点数", "নম্বর / স্কোর"],
        ["へいきん", "平均", "গড়"],
        ["ちしき", "知識", "জ্ঞান"],
        ["ぎじゅつ", "技術", "প্রযুক্তি / দক্ষতা"],
        ["のうりょく", "能力", "সামর্থ্য / যোগ্যতা"],
        ["しゅみ", "趣味", "শখ"],
        ["とくぎ", "特技", "বিশেষ দক্ষতা"],

        // DAY 8 (71-80)
        ["せいかく", "性格", "স্বভাব / চরিত্র"],
        ["きもち", "気持ち", "অনুভূতি / মন"],
        ["きぶん", "気分", "মেজাজ / শারীরিক ভাব"],
        ["しんぱい", "心配", "দুশ্চিন্তা"],
        ["あんしん", "安心", "নিশ্চিন্ত / স্বস্তি"],
        ["きんちょう", "緊張", "উত্তেজনা / নার্ভাস হওয়া"],
        ["まんぞく", "満足", "সন্তুষ্টি"],
        ["ふまん", "不満", "অসন্তুষ্টি"],
        ["こうかい", "後悔", "অনুতাপ / আফসোস"],
        ["かんしゃ", "感謝", "কৃতজ্ঞতা"],

        // DAY 9 (81-90)
        ["あいさつ", "挨拶", "অভিবাদন / সালাম"],
        ["れいぎ", "礼儀", "শিষ্টাচার / ভদ্রতা"],
        ["しつれい", "失礼", "অভদ্রতা / মাফ করবেন"],
        ["えんりょ", "遠慮", "সংকোচ / দ্বিধা"],
        ["がまん", "我慢", "ধৈর্য ধরা / সহ্য করা"],
        ["しんらい", "信頼", "আস্থা / বিশ্বাস"],
        ["しんよう", "信用", "বিশ্বাসযোগ্যতা / সুনাম"],
        ["うそ", "嘘", "মিথ্যা"],
        ["ひみつ", "秘密", "গোপন কথা"],
        ["うわさ", "噂", "গুজব"],

        // DAY 10 (91-100)
        ["じじつ", "事実", "সত্য ঘটনা"],
        ["しんじつ", "真実", "প্রকৃত সত্য"],
        ["りゆう", "理由", "কারণ"],
        ["げんいん", "原因", "মূল কারণ (সমস্যার)"],
        ["けっか", "結果", "ফলাফল"],
        ["えいきょう", "影響", "প্রভাব"],
        ["かんけい", "関係", "সম্পর্ক"],
        ["もんだい", "問題", "সমস্যা / প্রশ্ন"],
        ["かいけつ", "解決", "সমাধান"],
        ["ほうほう", "方法", "পদ্ধতি / উপায়"],

        // DAY 11 (101-110)
        ["しゅだん", "手段", "মাধ্যম / উপায়"],
        ["もくてき", "目的", "উদ্দেশ্য"],
        ["もくひょう", "目標", "লক্ষ্য"],
        ["きぼう", "希望", "আশা / আকাঙ্ক্ষা"],
        ["ゆめ", "夢", "স্বপ্ন"],
        ["しょうらい", "将来", "ভবিষ্যৎ"],
        ["かこ", "過去", "অতীত"],
        ["げんざい", "現在", "বর্তমান"],
        ["じだい", "時代", "যুগ / সময়কাল"],
        ["れきし", "歴史", "ইতিহাস"],

        // DAY 12 (111-120)
        ["ぶんか", "文化", "সংস্কৃতি"],
        ["しゅうかん", "習慣", "অভ্যাস / রীতিনীতি"],
        ["でんとう", "伝統", "ঐতিহ্য"],
        ["しゃかい", "社会", "সমাজ"],
        ["けいざい", "経済", "অর্থনীতি"],
        ["せいじ", "政治", "রাজনীতি"],
        ["ほうりつ", "法律", "আইন"],
        ["きそく", "規則", "নিয়মকানুন"],
        ["マナー", "", "আচরণবিধি / ম্যানার"],
        ["せきにん", "責任", "দায়িত্ব"],

        // DAY 13 (121-130)
        ["ぎむ", "義務", "কর্তব্য / বাধ্যবাধকতা"],
        ["けんり", "権利", "অধিকার"],
        ["じゆう", "自由", "স্বাধীনতা"],
        ["へいわ", "平和", "শান্তি"],
        ["せんそう", "戦争", "যুদ্ধ"],
        ["じけん", "事件", "ঘটনা / অপরাধ"],
        ["じこ", "事故", "দুর্ঘটনা"],
        ["きけん", "危険", "বিপদ"],
        ["ひがい", "被害", "ক্ষয়ক্ষতি"],
        ["さいがい", "災害", "প্রাকৃতিক দুর্যোগ"],

        // DAY 14 (131-140)
        ["じしん", "地震", "ভূমিকম্প"],
        ["たいふう", "台風", "টাইফুন / ঘূর্ণিঝড়"],
        ["こうずい", "洪水", "বন্যা"],
        ["かじ", "火事", "অগ্নিকাণ্ড"],
        ["きゅうきゅうしゃ", "救急車", "অ্যাম্বুলেন্স"],
        ["けいさつ", "警察", "পুলিশ"],
        ["しょうぼうしょ", "消防署", "ফায়ার সার্ভিস"],
        ["ひなん", "避難", "নিরাপদ আশ্রয়ে যাওয়া"],
        ["あんぴ", "安否", "নিরাপত্তা ও কুশল"],
        ["きゅうじょ", "救助", "উদ্ধার"],

        // DAY 15 (141-150)
        ["けんこう", "健康", "স্বাস্থ্য"],
        ["びょうき", "病気", "অসুস্থতা / রোগ"],
        ["けが", "怪我", "আঘাত / জখম"],
        ["いたみ", "痛み", "ব্যথা"],
        ["ねつ", "熱", "জ্বর / তাপ"],
        ["かぜ", "風邪", "সর্দি-কাশি"],
        ["せき", "咳", "কাশি"],
        ["ずつう", "頭痛", "মাথাব্যথা"],
        ["ふくつう", "腹痛", "পেটব্যথা"],
        ["ちりょう", "治療", "চিকিৎসা"],

        // DAY 16 (151-160)
        ["しんさつ", "診察", "ডাক্তারি পরীক্ষা"],
        ["けんさ", "検査", "টেস্ট / পরীক্ষা-নিরীক্ষা"],
        ["しゅじゅつ", "手術", "অপারেশন / অস্ত্রোপচার"],
        ["にゅういん", "入院", "হাসপাতালে ভর্তি হওয়া"],
        ["たいいん", "退院", "হাসপাতাল থেকে ছাড়পত্র পাওয়া"],
        ["くすり", "薬", "ওষুধ"],
        ["ちゅうしゃ", "注射", "ইনজেকশন"],
        ["かいふく", "回復", "সুস্থ হয়ে ওঠা / পুনরুদ্ধার"],
        ["よぼう", "予防", "প্রতিরোধ"],
        ["えいよう", "栄養", "পুষ্টি"],

        // DAY 17 (161-170)
        ["しょくじ", "食事", "খাবার গ্রহণ"],
        ["ちょうしょく", "朝食", "সকালের নাশতা"],
        ["ちゅうしょく", "昼食", "দুপুরের খাবার"],
        ["ゆうしょく", "夕食", "রাতের খাবার"],
        ["ざいりょう", "材料", "উপকরণ"],
        ["ちょうみりょう", "調味料", "মসলা / স্বাদবর্ধক"],
        ["あじ", "味", "স্বাদ"],
        ["におい", "匂い", "গন্ধ / সুবাস"],
        ["かおり", "香り", "সুগন্ধ"],
        ["しょくよく", "食欲", "ক্ষুধা / খাবারের রুচি"],

        // DAY 18 (171-180)
        ["あまい", "甘い", "মিষ্টি"],
        ["からい", "辛い", "ঝাল"],
        ["しおからい", "塩辛い", "নোনতা"],
        ["すっぱい", "酸っぱい", "টক"],
        ["にがい", "苦い", "তিতা"],
        ["こい", "濃い", "গাঢ় / কড়া"],
        ["うすい", "薄い", "পাতলা / হালকা"],
        ["やわらかい", "柔らかい", "নরম"],
        ["かたい", "硬い", "শক্ত"],
        ["なま", "生", "কাঁচা / তাজা"],

        // DAY 19 (181-190)
        ["りょうり", "料理", "রান্না / খাবার"],
        ["やく", "焼く", "ভাজা / সেঁকা"],
        ["にる", "煮る", "সিদ্ধ করা / রান্না করা"],
        ["いためる", "炒める", "তেলে ভাজা (Stir-fry)"],
        ["あげる", "揚げる", "ডুবো তেলে ভাজা"],
        ["むす", "蒸す", "ভাপে সিদ্ধ করা"],
        ["わかす", "沸かす", "পানি ফোটানো"],
        ["ひやす", "冷やす", "ঠান্ডা করা"],
        ["あたためる", "温める", "গরম করা"],
        ["まぜる", "混ぜる", "মেশানো"],

        // DAY 20 (191-200)
        ["かいもの", "買い物", "কেনাকাটা"],
        ["ねだん", "値段", "দাম / মূল্য"],
        ["かかく", "価格", "বাজারমূল্য"],
        ["わりびき", "割引", "ডিসকাউন্ট / ছাড়"],
        ["はんがく", "半額", "অর্ধেক দাম"],
        ["むりょう", "無料", "বিনামূল্যে / ফ্রি"],
        ["ゆうりょう", "有料", "মূল্য প্রযোজ্য"],
        ["ぜいきん", "税金", "কর / ট্যাক্স"],
        ["しょうひぜい", "消費税", "ভ্যাট / বিক্রয় কর"],
        ["りょうしゅうしょ", "領収書", "রসিদ / ভাউচার"],

        // DAY 21 (201-210)
        ["おつり", "お釣り", "ভাংতি / ফেরত টাকা"],
        ["げんきん", "現金", "নগদ টাকা"],
        ["クレジットカード", "", "ক্রেডিট কার্ড"],
        ["ぎんこう", "銀行", "ব্যাংক"],
        ["こうざ", "口座", "ব্যাংক অ্যাকাউন্ট"],
        ["ちょきん", "貯金", "সঞ্চয়"],
        ["しゅうにゅう", "収入", "আয়"],
        ["ししゅつ", "支出", "ব্যয়"],
        ["きゅうりょう", "給料", "বেতন"],
        ["ボーナス", "", "বোনাস"],

        // DAY 22 (211-220)
        ["しごと", "仕事", "কাজ / চাকরি"],
        ["しょくぎょう", "職業", "পেশা"],
        ["かいしゃいん", "会社員", "কোম্পানির কর্মচারী"],
        ["こうむいん", "公務員", "সরকারি চাকরিজীবী"],
        ["しゃちょう", "社長", "কোম্পানির প্রেসিডেন্ট"],
        ["ぶちょう", "部長", "বিভাগীয় প্রধান"],
        ["かちょう", "課長", "সেকশন ম্যানেজার"],
        ["どうりょう", "同僚", "সহকর্মী"],
        ["せんぱい", "先輩", "সিনিয়র"],
        ["こうはい", "後輩", "জুনিয়র"],

        // DAY 23 (221-230)
        ["しゅうしょく", "就職", "চাকরিতে যোগদান"],
        ["てんしょく", "転職", "চাকরি পরিবর্তন"],
        ["たいしょく", "退職", "চাকরি থেকে অবসর বা পদত্যাগ"],
        ["しつぎょう", "失業", "বেকারত্ব"],
        ["めんせつ", "面接", "ইন্টারভিউ / সাক্ষাৎকার"],
        ["りれきしょ", "履歴書", "জীবনবৃত্তান্ত / সিভি"],
        ["しゅっきん", "出勤", "অফিসে উপস্থিত হওয়া"],
        ["たいきん", "退勤", "অফিস শেষ করে বের হওয়া"],
        ["ざんぎょう", "残業", "ওভারটাইম"],
        ["しゅっちょう", "出張", "অফিসিয়াল ভ্রমণ"],

        // DAY 24 (231-240)
        ["こうつう", "交通", "যানবাহন / যাতায়াত"],
        ["でんしゃ", "電車", "ট্রেন"],
        ["ちかてつ", "地下鉄", "পাতাল রেল / মেট্রো"],
        ["しんかんせん", "新幹線", "বুলেট ট্রেন"],
        ["バス", "", "বাস"],
        ["タクシー", "", "ট্যাক্সি"],
        ["ひこうき", "飛行機", "উড়োজাহাজ"],
        ["くうこう", "空港", "বিমানবন্দর"],
        ["えき", "駅", "স্টেশন"],
        ["のりかえ", "乗り換え", "ট্রেন বা বাস পরিবর্তন"],

        // DAY 25 (241-250)
        ["きっぷ", "切符", "টিকিট"],
        ["ていきけん", "定期券", "মাসিক পাস টিকিট"],
        ["おうふく", "往復", "যাওয়া-আসা (Round trip)"],
        ["かたみち", "片道", "একমুখী (One way)"],
        ["ざせき", "座席", "আসন / সিট"],
        ["していせき", "指定席", "সংরক্ষিত আসন"],
        ["じゆうせき", "自由席", "অসংরক্ষিত আসন"],
        ["じゅうたい", "渋滞", "যানজট"],
        ["うんてん", "運転", "গাড়ি চালানো"],
        ["ちゅうしゃじょう", "駐車場", "পার্কিং লট"],

        // DAY 26 (251-260)
        ["りょこう", "旅行", "ভ্রমণ"],
        ["かんこう", "観光", "দর্শনীয় স্থান ভ্রমণ"],
        ["おみやげ", "お土産", "ভ্রমণের উপহার / স্যুভেনির"],
        ["よやく", "予約", "বুকিং / রিজার্ভেশন"],
        ["しゅくはく", "宿泊", "হোটেলে রাত্রিযাপন"],
        ["りょかん", "旅館", "জাপানি ঐতিহ্যবাহী হোটেল"],
        ["にもつ", "荷物", "লাগেজ / মালপত্র"],
        ["パスポート", "", "পাসপোর্ট"],
        ["ビザ", "", "ভিসা"],
        ["がいこく", "外国", "বিদেশ"],

        // DAY 27 (261-270)
        ["すまい", "住まい", "বাসস্থান"],
        ["アパート", "", "অ্যাপার্টমেন্ট"],
        ["マンション", "", "বহুতল ফ্ল্যাট বাড়ি"],
        ["やちん", "家賃", "বাড়িভাড়া"],
        ["しききん", "敷金", "সিকিউরিটি ডিপোজিট"],
        ["れいきん", "礼金", "বাড়িওয়ালাকে প্রদেয় উপহার ফি"],
        ["ひっこし", "引っ越し", "বাসা পরিবর্তন"],
        ["げんかん", "玄関", "প্রবেশদ্বার"],
        ["ろうか", "廊下", "করিডোর / বারান্দা"],
        ["ベランダ", "", "ব্যালকনি"],

        // DAY 28 (271-280)
        ["かぐ", "家具", "আসবাবপত্র"],
        ["かでん", "家電", "বৈদ্যুতিক গৃহস্থালি সামগ্রী"],
        ["れいぞうこ", "冷蔵庫", "রেফ্রিজারেটর / ফ্রিজ"],
        ["せんたくき", "洗濯機", "ওয়াশিং মেশিন"],
        ["そうじき", "掃除機", "ভ্যাকুয়াম ক্লিনার"],
        ["エアコン", "", "এয়ার কন্ডিশনার"],
        ["でんき", "電気", "বিদ্যুৎ / বাতি"],
        ["すいどう", "水道", "পানির লাইন"],
        ["ガス", "", "গ্যাস"],
        ["ごみ", "ゴミ", "ময়লা-আবর্জনা"],

        // DAY 29 (281-290)
        ["しぜん", "自然", "প্রকৃতি"],
        ["かんきょう", "環境", "পরিবেশ"],
        ["ちきゅう", "地球", "পৃথিবী"],
        ["うちゅう", "宇宙", "মহাকাশ"],
        ["たいよう", "太陽", "সূর্য"],
        ["つき", "月", "চাঁদ"],
        ["ほし", "星", "তারা"],
        ["くも", "雲", "মেঘ"],
        ["かぜ", "風", "বাতাস"],
        ["かみなり", "雷", "বজ্রপাত"],

        // DAY 30 (291-300)
        ["てんき", "天気", "আবহাওয়া"],
        ["きおん", "気温", "তাপমাত্রা"],
        ["しつど", "湿度", "আর্দ্রতা"],
        ["はれ", "晴れ", "রৌদ্রোজ্জ্বল / পরিষ্কার আকাশ"],
        ["くもり", "曇り", "মেঘলা"],
        ["あめ", "雨", "বৃষ্টি"],
        ["ゆき", "雪", "তুষার / বরফ"],
        ["きり", "霧", "কুয়াশা"],
        ["きせつ", "季節", "ঋতু"],
        ["はる", "春", "বসন্তকাল"],

        // DAY 31 (301-310)
        ["なつ", "夏", "গ্রীষ্মকাল"],
        ["あき", "秋", "শরৎকাল"],
        ["ふゆ", "冬", "শীতকাল"],
        ["さくら", "桜", "চেরি ফুল"],
        ["もみじ", "紅葉", "শরতের লাল পাতা"],
        ["やま", "山", "পাহাড়"],
        ["かわ", "川", "নদী"],
        ["うみ", "海", "সমুদ্র"],
        ["みずうみ", "湖", "হ্রদ"],
        ["しま", "島", "দ্বীপ"],

        // DAY 32 (311-320)
        ["もり", "森", "গভীর বন"],
        ["はやし", "林", "ছোট জঙ্গল"],
        ["はたけ", "畑", "ফসলের ক্ষেত"],
        ["たんぼ", "田んぼ", "ধান ক্ষেত"],
        ["どうぶつ", "動物", "প্রাণী"],
        ["しょくぶつ", "植物", "উদ্ভিদ"],
        ["むし", "虫", "পোকামাকড়"],
        ["とり", "鳥", "পাখি"],
        ["さかな", "魚", "মাছ"],
        ["ペット", "", "পোষা প্রাণী"],

        // DAY 33 (321-330)
        ["かぞく", "家族", "পরিবার"],
        ["りょうしん", "両親", "মা-বাবা"],
        ["きょうだい", "兄弟", "ভাইবোন"],
        ["ふうふ", "夫婦", "স্বামী-স্ত্রী"],
        ["おっと", "夫", "স্বামী"],
        ["つま", "妻", "স্ত্রী"],
        ["むすこ", "息子", "পুত্র"],
        ["むすめ", "娘", "কন্যা"],
        ["まご", "孫", "নাতি-নাতনি"],
        ["しんせき", "親戚", "আত্মীয়স্বজন"],

        // DAY 34 (331-340)
        ["しりあい", "知り合い", "পরিচিত ব্যক্তি"],
        ["ゆうじん", "友人", "বন্ধু"],
        ["しんゆう", "親友", "ঘনিষ্ঠ বন্ধু"],
        ["こいびと", "恋人", "প্রেমিক / প্রেমিকা"],
        ["けっこん", "結婚", "বিবাহ"],
        ["りこん", "離婚", "বিবাহবিচ্ছেদ"],
        ["しゅっさん", "出産", "সন্তান প্রসব"],
        ["いくじ", "育児", "শিশু লালন-পালন"],
        ["きょういく", "教育", "শিক্ষা"],
        ["せいちょう", "成長", "বেড়ে ওঠা / উন্নতি"],

        // DAY 35 (341-350)
        ["あかちゃん", "赤ちゃん", "শিশু / বাচ্চা"],
        ["ようちえん", "幼稚園", "কিন্ডারগার্টেন"],
        ["しょうがっこう", "小学校", "প্রাথমিক বিদ্যালয়"],
        ["ちゅうがっこう", "中学校", "মাধ্যমিক বিদ্যালয়"],
        ["こうこう", "高校", "উচ্চ মাধ্যমিক বিদ্যালয়"],
        ["だいがく", "大学", "বিশ্ববিদ্যালয়"],
        ["だいがくいん", "大学院", "স্নাতকোত্তর বিশ্ববিদ্যালয়"],
        ["りゅうがくせい", "留学生", "বিদেশি শিক্ষার্থী"],
        ["そつぎょう", "卒業", "স্নাতক / পড়াশোনা শেষ করা"],
        ["にゅうがく", "入学", "ভর্তি হওয়া"],

        // DAY 36 (351-360)
        ["じゅぎょう", "授業", "ক্লাস / পাঠদান"],
        ["こうぎ", "講義", "লেকচার"],
        ["けんきゅう", "研究", "গবেষণা"],
        ["ろんぶん", "論文", "গবেষণাপত্র / থিসিস"],
        ["としょかん", "図書館", "গ্রন্থাগার / লাইব্রেরি"],
        ["じしょ", "辞書", "অভিধান / ডিকশনারি"],
        ["ぶんぽう", "文法", "ব্যাকরণ"],
        ["たんご", "単語", "শব্দ / ভোকাবুলারি"],
        ["はつおん", "発音", "উচ্চারণ"],
        ["かいわ", "会話", "কথোপকথন"],

        // DAY 37 (361-370)
        ["ほんやく", "翻訳", "অনুবাদ (লিখিত)"],
        ["つうやく", "通訳", "দোভাষী / মৌখিক অনুবাদ"],
        ["いみ", "意味", "অর্থ"],
        ["ないよう", "内容", "বিষয়বস্তু"],
        ["りかい", "理解", "বোঝাপড়া / অনুধাবন"],
        ["ごかい", "誤解", "ভুল বোঝাবুঝি"],
        ["いけん", "意見", "মতামত"],
        ["さんせい", "賛成", "সম্মতি / সমর্থন"],
        ["はんたい", "反対", "বিরোধিতা"],
        ["ぎろん", "議論", "বিতর্ক / আলোচনা"],

        // DAY 38 (371-380)
        ["はっぴょう", "発表", "উপস্থাপনা / ঘোষণা"],
        ["スピーチ", "", "বক্তৃতা"],
        ["ニュース", "", "সংবাদ / খবর"],
        ["しんぶん", "新聞", "সংবাদপত্র"],
        ["ざっし", "雑誌", "ম্যাগাজিন"],
        ["きじ", "記事", "প্রবন্ধ / আর্টিকেল"],
        ["こうこく", "広告", "বিজ্ঞাপন"],
        ["じょうほう", "情報", "তথ্য"],
        ["インターネット", "", "ইন্টারনেট"],
        ["メール", "", "ইমেইল"],

        // DAY 39 (381-390)
        ["けんさく", "検索", "সার্চ / অনুসন্ধান"],
        ["とうろく", "登録", "নিবন্ধন / রেজিস্ট্রেশন"],
        ["にゅうりょく", "入力", "ইনপুট দেওয়া / টাইপ করা"],
        ["ほぞん", "保存", "সংরক্ষণ / সেভ করা"],
        ["さくじょ", "削除", "মুছে ফেলা / ডিলিট"],
        ["いんさつ", "印刷", "প্রিন্ট করা"],
        ["がめん", "画面", "স্ক্রিন / পর্দা"],
        ["パスワード", "", "পাসওয়ার্ড"],
        ["ウイルス", "", "ভাইরাস"],
        ["こしょう", "故障", "নষ্ট হওয়া / বিকল হওয়া"],

        // DAY 40 (391-400)
        ["しゅうり", "修理", "মেরামত"],
        ["こうかん", "交換", "বদলানো / বিনিময়"],
        ["へんぴん", "返品", "পণ্য ফেরত দেওয়া"],
        ["ちゅうもん", "注文", "অর্ডার"],
        ["はいたつ", "配達", "ডেলিভারি / বিতরণ"],
        ["うけとり", "受け取り", "গ্রহণ করা"],
        ["そうりょう", "送料", "ডেলিভারি চার্জ"],
        ["ほうそう", "包装", "প্যাকেজিং / মোড়ানো"],
        ["ふくろ", "袋", "ব্যাগ / থলে"],
        ["はこ", "箱", "বাক্স"],

        // DAY 41 (401-410)
        ["ふくそう", "服装", "পোশাক-পরিচ্ছদ"],
        ["スーツ", "", "স্যুট"],
        ["ネクタイ", "", "টাই"],
        ["コート", "", "কোট"],
        ["セーター", "", "সোয়েটার"],
        ["ズボン", "", "প্যান্ট"],
        ["スカート", "", "স্কার্ট"],
        ["くつした", "靴下", "মোজা"],
        ["てぶくろ", "手袋", "হাতমোজা"],
        ["マフラー", "", "মাফলার"],

        // DAY 42 (411-420)
        ["サイズ", "", "আকার / সাইজ"],
        ["デザイン", "", "নকশা / ডিজাইন"],
        ["いろ", "色", "রঙ"],
        ["かたち", "形", "আকৃতি"],
        ["もよう", "模様", "প্যাটার্ন / নকশা"],
        ["せんたく", "洗濯", "কাপড় ধোয়া"],
        ["アイロン", "", "ইস্ত্রি"],
        ["かわく", "乾く", "শুকিয়ে যাওয়া"],
        ["ぬれる", "濡れる", "ভিজে যাওয়া"],
        ["よごれる", "汚れる", "ময়লা হওয়া"],

        // DAY 43 (421-430)
        ["かお", "顔", "মুখমণ্ডল"],
        ["かみ", "髪", "চুল"],
        ["ひたい", "額", "কপাল"],
        ["まゆげ", "眉毛", "ভ্রু"],
        ["め", "目", "চোখ"],
        ["はな", "鼻", "নাক"],
        ["くち", "口", "মুখ"],
        ["は", "歯", "দাঁত"],
        ["みみ", "耳", "কান"],
        ["くび", "首", "ঘাড় / গলা"],

        // DAY 44 (431-440)
        ["かた", "肩", "কাঁধ"],
        ["うで", "腕", "বাহু"],
        ["ゆび", "指", "আঙুল"],
        ["つめ", "爪", "নখ"],
        ["むね", "胸", "বুক"],
        ["おなか", "お腹", "পেট"],
        ["せなか", "背中", "পিঠ"],
        ["こし", "腰", "কোমর"],
        ["あし", "足", "পা"],
        ["ひざ", "膝", "হাঁটু"],

        // DAY 45 (441-450)
        ["たいじゅう", "体重", "শরীরের ওজন"],
        ["しんちょう", "身長", "উচ্চতা"],
        ["たいりょく", "体力", "শারীরিক শক্তি"],
        ["うんどう", "運動", "ব্যায়াম / খেলাধুলা"],
        ["ダイエット", "", "ডায়েট / ওজন কমানো"],
        ["すいみん", "睡眠", "ঘুম"],
        ["ひろう", "疲労", "ক্লান্তি"],
        ["ストレス", "", "মানসিক চাপ"],
        ["リラックス", "", "বিশ্রাম / আরাম"],
        ["きぶんてんかん", "気分転換", "মন ভালো করা / বিনোদন"],

        // DAY 46 (451-460)
        ["えいが", "映画", "সিনেমা"],
        ["おんがく", "音楽", "সঙ্গীত"],
        ["びじゅつ", "美術", "চারুকলা / শিল্পকলা"],
        ["しょうせつ", "小説", "উপন্যাস"],
        ["まんが", "漫画", "কমিকস / মাঙ্গা"],
        ["アニメ", "", "অ্যানিমেশন"],
        ["コンサート", "", "কনসার্ট"],
        ["びじゅつかん", "美術館", "আর্ট মিউজিয়াম"],
        ["はくぶつかん", "博物館", "জাদুঘর"],
        ["どうぶつえん", "動物園", "চিড়িয়াখানা"],

        // DAY 47 (461-470)
        ["あつめる", "集める", "সংগ্রহ করা / জড়ো করা"],
        ["あつまる", "集まる", "একত্রিত হওয়া"],
        ["ならべる", "並べる", "সারিবদ্ধভাবে সাজানো"],
        ["ならぶ", "並ぶ", "লাইনে দাঁড়ানো"],
        ["かたづける", "片付ける", "গুছিয়ে রাখা / পরিষ্কার করা"],
        ["かたづく", "片付く", "গোছানো হওয়া"],
        ["すてる", "捨てる", "ফেলে দেওয়া"],
        ["ひろう", "拾う", "কুড়িয়ে নেওয়া"],
        ["おとす", "落とす", "ফেলে দেওয়া / হারানো"],
        ["おちる", "落ちる", "পড়ে যাওয়া"],

        // DAY 48 (471-480)
        ["こわす", "壊す", "ভাঙা (নিজে)"],
        ["こわれる", "壊れる", "ভেঙে যাওয়া"],
        ["よごす", "汚す", "ময়লা করা"],
        ["なおす", "直す", "ঠিক করা / মেরামত করা"],
        ["なおる", "直る", "ঠিক হওয়া / সুস্থ হওয়া"],
        ["かえる", "変える", "পরিবর্তন করা"],
        ["かわる", "変わる", "পরিবর্তিত হওয়া"],
        ["ふやす", "増やす", "বাড়ানো"],
        ["ふえる", "増える", "বেড়ে যাওয়া"],
        ["へらす", "減らす", "কমানো"],

        // DAY 49 (481-490)
        ["へる", "減る", "কমে যাওয়া"],
        ["つづける", "続ける", "চালিয়ে যাওয়া"],
        ["つづく", "続く", "চলতে থাকা"],
        ["はじめる", "始める", "শুরু করা"],
        ["はじまる", "始まる", "শুরু হওয়া"],
        ["おわる", "終わる", "শেষ হওয়া"],
        ["おえる", "終える", "শেষ করা"],
        ["きめる", "決める", "সিদ্ধান্ত নেওয়া"],
        ["きまる", "決まる", "সিদ্ধান্ত হওয়া"],
        ["みつける", "見つける", "খুঁজে পাওয়া"],

        // DAY 50 (491-500)
        ["みつかる", "見つかる", "খুঁজে পাওয়া যাওয়া"],
        ["たすける", "助ける", "সাহায্য করা / বাঁচানো"],
        ["たすかる", "助かる", "রক্ষা পাওয়া / উপকৃত হওয়া"],
        ["そだてる", "育てる", "লালন-পালন করা"],
        ["そだつ", "育つ", "বেড়ে ওঠা"],
        ["つたえる", "伝える", "বার্তা পৌঁছে দেওয়া"],
        ["つたわる", "伝わる", "পৌঁছে যাওয়া"],
        ["とどける", "届ける", "পৌঁছে দেওয়া / ডেলিভারি করা"],
        ["とどく", "届く", "পৌঁছানো"],
        ["あずける", "預ける", "জমা রাখা"],

        // DAY 51 (501-510)
        ["あずかる", "預かる", "জিম্মায় রাখা"],
        ["かりる", "借りる", "ধার নেওয়া"],
        ["かす", "貸す", "ধার দেওয়া"],
        ["かえす", "返す", "ফেরত দেওয়া"],
        ["もどる", "戻る", "ফিরে আসা"],
        ["もどす", "戻す", "ফিরিয়ে রাখা"],
        ["すすむ", "進む", "এগিয়ে যাওয়া"],
        ["すすめる", "進める", "এগিয়ে নেওয়া / সুপারিশ করা"],
        ["おくれる", "遅れる", "দেরি হওয়া"],
        ["まにあう", "間に合う", "সময়মতো পৌঁছানো"],

        // DAY 52 (511-520)
        ["いそぐ", "急ぐ", "তাড়াহুড়ো করা"],
        ["あわてる", "慌てる", "ঘাবড়ে যাওয়া / তাড়াহুড়ো করা"],
        ["おどろく", "驚く", "অবাক হওয়া"],
        ["よろこぶ", "喜ぶ", "আনন্দিত হওয়া"],
        ["かなしむ", "悲しむ", "দুঃখ পাওয়া"],
        ["おこる", "怒る", "রেগে যাওয়া"],
        ["わらう", "笑う", "হাসা"],
        ["なく", "泣く", "কাঁদা"],
        ["ほめる", "褒める", "প্রশংসা করা"],
        ["しかる", "叱る", "বকা দেওয়া"],

        // DAY 53 (521-530)
        ["あやまる", "謝る", "ক্ষমা চাওয়া"],
        ["ゆるす", "許す", "ক্ষমা করা / অনুমতি দেওয়া"],
        ["たのむ", "頼む", "অনুরোধ করা"],
        ["ことわる", "断る", "প্রত্যাখ্যান করা / না করা"],
        ["さそう", "誘う", "আমন্ত্রণ জানানো / ডাকা"],
        ["うけとる", "受け取る", "গ্রহণ করা"],
        ["わたす", "渡す", "হস্তান্তর করা"],
        ["おくる", "送る", "পাঠানো"],
        ["むかえる", "迎える", "স্বাগত জানানো / রিসিভ করা"],
        ["みおくる", "見送る", "বিদায় জানানো"],

        // DAY 54 (531-540)
        ["しらべる", "調べる", "তদন্ত করা / খুঁজে দেখা"],
        ["さがす", "探す", "খোঁজা"],
        ["えらぶ", "選ぶ", "বেছে নেওয়া"],
        ["くらべる", "比べる", "তুলনা করা"],
        ["かぞえる", "数える", "গণনা করা"],
        ["はかる", "測る", "মাপা / ওজন করা"],
        ["ためす", "試す", "পরীক্ষা করে দেখা"],
        ["まもる", "守る", "রক্ষা করা / নিয়ম মানা"],
        ["やぶる", "破る", "ছেঁড়া / নিয়ম ভঙ্গ করা"],
        ["うしなう", "失う", "হারানো"],

        // DAY 55 (541-550)
        ["える", "得る", "অর্জন করা / পাওয়া"],
        ["あたえる", "与える", "প্রদান করা"],
        ["うける", "受ける", "গ্রহণ করা / পরীক্ষা দেওয়া"],
        ["ふくむ", "含む", "অন্তর্ভুক্ত করা"],
        ["のぞく", "除く", "বাদ দেওয়া / ছাড়া"],
        ["わける", "分ける", "ভাগ করা"],
        ["わかれる", "別れる", "আলাদা হওয়া / বিদায় নেওয়া"],
        ["むすぶ", "結ぶ", "বাঁধা / চুক্তি করা"],
        ["ほどく", "解く", "খোলা (গিঁট)"],
        ["つなぐ", "繋ぐ", "সংযুক্ত করা"],

        // DAY 56 (551-560)
        ["かならず", "必ず", "অবশ্যই"],
        ["ぜったい", "絶対", "একেবারে / নিশ্চিতভাবে"],
        ["たぶん", "多分", "সম্ভবত"],
        ["おそらく", "恐らく", "সম্ভবত / আমার ধারণা"],
        ["たしか", "確か", "সম্ভবত / আমার মনে পড়ে"],
        ["じつは", "実は", "আসলে / প্রকৃতপক্ষে"],
        ["とくに", "特に", "বিশেষ করে"],
        ["べつに", "別に", "বিশেষ কিছু না"],
        ["やっと", "やっと", "অবশেষে / অনেক কষ্টে"],
        ["ようやく", "漸く", "অবশেষে"],

        // DAY 57 (561-570)
        ["いきなり", "", "হঠাৎ করে"],
        ["とつぜん", "突然", "হঠাৎ"],
        ["いつのまにか", "いつの間にか", "কখন যে (অজান্তেই)"],
        ["しだいに", "次第に", "ধীরে ধীরে / ক্রমান্বয়ে"],
        ["ますます", "益々", "আরও বেশি করে"],
        ["だんだん", "段々", "ক্রমান্বয়ে"],
        ["ほとんど", "殆ど", "প্রায় সব / প্রায়ই না"],
        ["だいたい", "大体", "মোটামুটি / প্রায়"],
        ["かなり", "", "বেশ / যথেষ্ট"],
        ["ずいぶん", "随分", "অনেক / বেশ"],

        // DAY 58 (571-580)
        ["ちっとも", "", "একটুও না"],
        ["めったに", "滅多に", "খুব কমই / কদাচিৎ"],
        ["せっかく", "折角", "অনেক কষ্ট করে / বিশেষ সুযোগে"],
        ["わざわざ", "", "বিশেষভাবে কষ্ট করে"],
        ["うっかり", "", "ভুলবশত / অসাবধানতাবশত"],
        ["しっかり", "", "ভালোভাবে / দৃঢ়ভাবে"],
        ["はっきり", "", "স্পষ্টভাবে"],
        ["すっきり", "", "সতেজ / ঝরঝরে ভাব"],
        ["ぴったり", "", "একদম ঠিকঠাক / মানানসই"],
        ["ぐっすり", "", "গভীরভাবে (ঘুমানো)"],

        // DAY 59 (581-590)
        ["のんびり", "", "আরাম করে / নিশ্চিন্তে"],
        ["ゆっくり", "", "ধীরে ধীরে / আরামে"],
        ["こっそり", "", "চুপিসারে / গোপনে"],
        ["そっくり", "", "হুবহু একই রকম"],
        ["びっくり", "", "চমকে যাওয়া / অবাক হওয়া"],
        ["がっかり", "", "হতাশ হওয়া"],
        ["ほっと", "", "স্বস্তির নিঃশ্বাস ফেলা"],
        ["いらいら", "", "বিরক্ত বা অস্থির বোধ করা"],
        ["どきどき", "", "বুক ধড়ফড় করা"],
        ["わくわく", "", "আনন্দে রোমাঞ্চিত হওয়া"],

        // DAY 60 (591-600)
        ["けっきょく", "結局", "শেষ পর্যন্ত / পরিণামে"],
        ["つまり", "", "অর্থাৎ / সংক্ষেপে"],
        ["たとえば", "例えば", "উদাহরণস্বরূপ"],
        ["たしかに", "確かに", "নিশ্চিতভাবে / ঠিকই"],
        ["しかし", "", "কিন্তু / তবে"],
        ["それでも", "", "তবুও"],
        ["それなのに", "", "তা সত্ত্বেও"],
        ["したがって", "従って", "অতএব / সুতরাং"],
        ["ところで", "", "যাই হোক (প্রসঙ্গ পরিবর্তন)"],
        ["おかげで", "お陰で", "কল্যাণে / কৃপায়"]
      ];

      // Structure into 60 Days x 10 Words
      const VOCAB_BY_DAY = {};
      for (let day = 1; day <= 60; day++) {
        const startIdx = (day - 1) * 10;
        VOCAB_BY_DAY[day] = RAW_VOCAB_LIST.slice(startIdx, startIdx + 10).map((item, idx) => ({
          id: startIdx + idx + 1,
          numInDay: idx + 1,
          reading: item[0],
          kanji: item[1],
          bangla: item[2]
        }));
      }

      // =========================================================================
      // 2. APPLICATION STATE & BILINGUAL UI DICTIONARY
      // Note: Vocabulary meanings/translations (word.bangla) ALWAYS remain in Bengali.
      // =========================================================================
      let state = {
        lang: @json(auth()->user()->preferred_lang === 'jp' ? 'ja' : 'bn'), // "bn" (বাংলা) or "ja" (日本語)
        currentDay: 1,
        completedDays: @json($completedDayNumbers),
        selectedMinutes: 5,
        hideMeanings: false,
        hideKanji: false,
        showAllDayPills: false
      };

      const bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
      function toBnNum(num) {
        return String(num).replace(/\d/g, d => bnDigits[d]);
      }

      function fmtNum(n) {
        return state.lang === "bn" ? toBnNum(n) : String(n);
      }

      const UI_TEXT = {
        bn: {
          appTitle: "JLPT N3 Vocabulary Tracker",
          headerBadge: "৬০ দিন • ৬০০ শব্দ",
          appSubtitle: "N3 ভোকাবুলারি স্টাডি অ্যাপ — প্রতিদিন ১০টি শব্দ, মক টেস্ট ও ফাইনাল এক্সাম",
          progressLabel: "মোট অগ্রগতি:",
          progressFormat: (pct, done, words) => `${toBnNum(pct)}% সম্পন্ন (${toBnNum(done)}/৬০ দিন • ${toBnNum(words)}/৬০০ শব্দ)`,
          dayKicker: "Day Toggle System",
          dayTitle: (d) => `Day ${d} (দিন ${toBnNum(d)})`,
          dayOption: (d, done) => `Day ${d} (দিন ${toBnNum(d)}) ${done ? "✓" : ""}`,
          dayCompletedBadge: "✅ সম্পন্ন",
          dayInProgressBadge: "চলমান",
          prevDay: "◀ আগের দিন",
          nextDay: "পরের দিন ▶",
          quickDayLabel: "দ্রুত দিন পরিবর্তন করুন (Day 1 – 60):",
          toggleGridBtn: "সব ৬০ দিন দেখুন / লুকান",
          timerKicker: "Study Timer",
          timerHeading: "দৈনিক অধ্যয়নের সময়",
          minPreset: (m) => `${toBnNum(m)} মি.`,
          timerRemaining: "অবশিষ্ট সময়",
          timerStart: "▶ শুরু",
          timerPause: "⏸ বিরতি",
          timerResetTitle: "রিসেট",
          timerFinishedToast: (m) => `দারুণ! আপনার ${toBnNum(m)} মিনিটের স্টাডি সেশন সম্পন্ন হয়েছে। এবার মক টেস্ট দিন!`,
          tabStudy: "📖 স্টাডি মোড (১০টি শব্দ)",
          tabMock: "📝 ডেইলি মক টেস্ট (Mock Test)",
          tabExam: "🏆 ফাইনাল মেগা এক্সাম (Day 1–60)",
          resetProgressBtn: "প্রোগ্রেস রিসেট",
          toggleMeaningsBtn: "👁️ অর্থ লুকান / দেখুন (Self-Test)",
          toggleKanjiBtn: "🈳 কঞ্জি লুকান / দেখুন",
          kanjiHiddenText: "কঞ্জি লুকানো",
          kanjiHiddenTitle: "ক্লিক করে কঞ্জি দেখুন",
          revealMeaningBtn: "অর্থ দেখুন 👁️",
          speakTitle: "উচ্চারণ শুনুন",
          markCompleteBtn: "✅ Day Complete মার্ক করুন",
          unmarkCompleteBtn: "↩️ অসম্পূর্ণ হিসেবে চিহ্নিত করুন",
          startDayMockCta: "🎯 মক টেস্ট দিন →",
          dayMarkedDoneToast: (d, pct) => `অভিনন্দন! Day ${d} সম্পন্ন হয়েছে। মোট অগ্রগতি: ${toBnNum(pct)}%`,
          dayMarkedUndoneToast: (d) => `Day ${d} অসম্পূর্ণ হিসেবে আপডেট করা হয়েছে।`,
          mockKicker: "Daily Mock Test",
          mockTitle: (d) => `Day ${d} (দিন ${toBnNum(d)}) — ভোকাবুলারি মক টেস্ট (১০টি প্রশ্ন)`,
          mockSubtitle: "সঠিক উত্তরটি নির্বাচন করুন। সবগুলো উত্তর দিলে ফলাফল দেখতে পাবেন।",
          restartMockBtn: "🔄 নতুন করে প্রশ্ন সাজান",
          submitMockBtn: "ফলাফল যাচাই করুন (Submit)",
          mockPromptJpToBn: (jpLabel) => `「${jpLabel}」 শব্দটির সঠিক বাংলা অর্থ কোনটি?`,
          mockPromptBnToJp: (bangla) => `“${bangla}” — এর সঠিক জাপানি শব্দ কোনটি?`,
          mockUnansweredToast: (n) => `দয়া করে সবগুলো প্রশ্নের উত্তর দিন (আরও ${toBnNum(n)}টি বাকি আছে)।`,
          mockCorrect: "✅ সঠিক উত্তর!",
          mockWrong: (ans) => `❌ ভুল উত্তর। সঠিক উত্তর: ${ans}`,
          mockScore: (score, pct) => `আপনার স্কোর: ${toBnNum(score)} / ১০ (${toBnNum(pct)}%)`,
          mockPassFeedback: (d) => `অসাধারণ! আপনি Day ${d} খুব ভালোভাবে আয়ত্ত করেছেন। স্বয়ংক্রিয়ভাবে দিনটি Complete মার্ক করা হলো।`,
          mockRetryFeedback: "ভালো চেষ্টা! ৭ বা তার বেশি পেতে শব্দগুলো আরেকবার রিভিশন দিয়ে মক টেস্ট দিন।",
          examMainTitle: "JLPT N3 গ্র্যান্ড ফাইনাল এক্সাম (Day 1 – Day 60)",
          examDescIncomplete: (done) => `বর্তমানে আপনার <strong>${toBnNum(done)}/৬০ দিন</strong> সম্পন্ন হয়েছে। সব ৬০ দিন শেষ করে ফাইনাল এক্সাম দিলে সেরা প্রস্তুতি হবে (তবে আপনি এখনই প্র্যাকটিস এক্সাম দিতে পারবেন)।`,
          examDescComplete: "🎉 অভিনন্দন! আপনি <strong>৬০/৬০ দিন (১০০%)</strong> সম্পন্ন করেছেন। এখন চূড়ান্ত পরীক্ষায় অংশগ্রহণ করুন!",
          startFinalExamBtn: "🚀 ফাইনাল এক্সাম শুরু করুন",
          examQCount: "মোট প্রশ্ন: ২০টি (Day 1 – 60)",
          examTimePrefix: (timeStr) => `সময়: ${timeStr}`,
          submitFinalExamBtn: "চূড়ান্ত পরীক্ষা জমা দিন (Finish Exam)",
          examUnansweredToast: (n) => `ফাইনাল এক্সামের আরও ${toBnNum(n)}টি প্রশ্নের উত্তর বাকি আছে।`,
          examCorrect: "✅ সঠিক",
          examWrong: (ans) => `❌ ভুল (সঠিক উত্তর: ${ans})`,
          examPassTitle: "🏆 অভিনন্দন! আপনি JLPT N3 ভোকাবুলারি পরীক্ষায় উত্তীর্ণ (PASS) হয়েছেন!",
          examPassSummary: (score, pct) => `প্রাপ্ত নম্বর: ${toBnNum(score)} / ২০ (${toBnNum(pct)}%)। আপনার ৬০০ শব্দের প্রস্তুতি চমৎকার হয়েছে!`,
          examFailTitle: (score, pct) => `📚 ফলাফল: ${toBnNum(score)} / ২০ (${toBnNum(pct)}%)`,
          examFailSummary: "পাস করার জন্য অন্তত ১৬টি (৮০%) সঠিক উত্তর প্রয়োজন। যে শব্দগুলো ভুল হয়েছে সেগুলো রিভিশন দিয়ে আবার পরীক্ষা দিন।",
          confirmTitle: "সম্পূর্ণ প্রোগ্রেস রিসেট করবেন?",
          confirmDesc: "এতে আপনার সম্পন্ন করা সব দিনের (Day 1–60) অগ্রগতি মুছে ০% হয়ে যাবে। আপনি কি নিশ্চিত?",
          confirmCancel: "বাতিল",
          confirmOk: "হ্যাঁ, রিসেট করুন",
          resetDoneToast: "সব প্রোগ্রেস রিসেট করা হয়েছে। নতুন করে Day 1 থেকে শুরু করুন!",
          footerText: "JLPT N3 Vocabulary Tracker • ৬০ দিন × ১০ শব্দ = ৬০০টি N3 ভোকাবুলারি • নিয়মিত চর্চা ও মক টেস্টের মাধ্যমে N3 প্রস্তুতি সম্পন্ন করুন।"
        },
        ja: {
          appTitle: "JLPT N3 語彙トラッカー",
          headerBadge: "60日間 • 600語",
          appSubtitle: "N3語彙学習アプリ — 毎日10語・模擬試験・最終試験",
          progressLabel: "達成率:",
          progressFormat: (pct, done, words) => `${pct}% 完了 (${done}/60日 • ${words}/600語)`,
          dayKicker: "学習日切り替えシステム",
          dayTitle: (d) => `${d}日`,
          dayOption: (d, done) => `${d}日 ${done ? "✓" : ""}`,
          dayCompletedBadge: "✅ 完了",
          dayInProgressBadge: "学習中",
          prevDay: "◀ 前の日",
          nextDay: "次の日 ▶",
          quickDayLabel: "日をすばやく切り替え (1日 – 60日):",
          toggleGridBtn: "全60日を表示 / 隠す",
          timerKicker: "学習タイマー",
          timerHeading: "毎日の学習時間",
          minPreset: (m) => `${m}分`,
          timerRemaining: "残り時間",
          timerStart: "▶ 開始",
          timerPause: "⏸ 一時停止",
          timerResetTitle: "リセット",
          timerFinishedToast: (m) => `お疲れ様でした！${m}分の学習セッションが完了しました。模擬試験に挑戦しましょう！`,
          tabStudy: "📖 学習モード (10語)",
          tabMock: "📝 模擬試験 (Mock Test)",
          tabExam: "🏆 最終試験 (Final Exam)",
          resetProgressBtn: "進捗リセット",
          toggleMeaningsBtn: "👁️ 意味を隠す / 表示",
          toggleKanjiBtn: "🈳 漢字を隠す / 表示",
          kanjiHiddenText: "漢字非表示",
          kanjiHiddenTitle: "クリックして漢字を表示",
          revealMeaningBtn: "意味を表示 👁️",
          speakTitle: "発音を聞く",
          markCompleteBtn: "✅ この日を完了にする",
          unmarkCompleteBtn: "↩️ 未完了に戻す",
          startDayMockCta: "🎯 模擬試験へ →",
          dayMarkedDoneToast: (d, pct) => `おめでとうございます！${d}日が完了しました。達成率: ${pct}%`,
          dayMarkedUndoneToast: (d) => `${d}日を未完了に更新しました。`,
          mockKicker: "デイリー模擬試験",
          mockTitle: (d) => `${d}日 — 語彙模擬試験 (10問)`,
          mockSubtitle: "正しい答えを選んでください。すべて回答すると結果が表示されます。",
          restartMockBtn: "🔄 問題をシャッフル",
          submitMockBtn: "結果を確認する (提出)",
          mockPromptJpToBn: (jpLabel) => `「${jpLabel}」の正しい意味はどれですか？`,
          mockPromptBnToJp: (bangla) => `「${bangla}」にあたる正しい日本語はどれですか？`,
          mockUnansweredToast: (n) => `すべての問題に回答してください（残り ${n} 問）。`,
          mockCorrect: "✅ 正解！",
          mockWrong: (ans) => `❌ 不正解。正解: ${ans}`,
          mockScore: (score, pct) => `スコア: ${score} / 10 (${pct}%)`,
          mockPassFeedback: (d) => `素晴らしいです！${d}日をしっかり身につけました。自動的に完了としてマークしました。`,
          mockRetryFeedback: "よく頑張りました！7点以上を目指して、もう一度復習してから挑戦しましょう。",
          examMainTitle: "JLPT N3 総合最終試験 (1日 – 60日)",
          examDescIncomplete: (done) => `現在 <strong>${done}/60日</strong> が完了しています。全60日を終えてから受験するのが理想ですが、練習として今すぐ受験することもできます。`,
          examDescComplete: "🎉 おめでとうございます！<strong>60/60日 (100%)</strong> を達成しました。最終試験に挑戦しましょう！",
          startFinalExamBtn: "🚀 最終試験を開始する",
          examQCount: "問題数: 20問 (1日 – 60日)",
          examTimePrefix: (timeStr) => `残り時間: ${timeStr}`,
          submitFinalExamBtn: "最終試験を提出する",
          examUnansweredToast: (n) => `最終試験の未回答問題が ${n} 問あります。`,
          examCorrect: "✅ 正解",
          examWrong: (ans) => `❌ 不正解 (正解: ${ans})`,
          examPassTitle: "🏆 おめでとうございます！JLPT N3 語彙試験に合格 (PASS) しました！",
          examPassSummary: (score, pct) => `得点: ${score} / 20 (${pct}%)。600語の学習成果がしっかり出ています！`,
          examFailTitle: (score, pct) => `📚 結果: ${score} / 20 (${pct}%)`,
          examFailSummary: "合格ラインは16問 (80%) 以上です。間違えた語彙を復習して再挑戦しましょう。",
          confirmTitle: "すべての進捗をリセットしますか？",
          confirmDesc: "完了したすべての日 (1日–60日) の記録が消去され、0%に戻ります。よろしいですか？",
          confirmCancel: "キャンセル",
          confirmOk: "はい、リセットする",
          resetDoneToast: "すべての進捗をリセットしました。1日から新しく始めましょう！",
          footerText: "JLPT N3 語彙トラッカー • 60日 × 10語 = 600語 • 毎日の学習と模擬試験でN3合格を目指しましょう。"
        }
      };

      function t() {
        return UI_TEXT[state.lang] || UI_TEXT.bn;
      }

    
      // Custom Toast Notification
      let toastTimeout = null;
      function showToast(message, icon = "✨") {
        const box = document.getElementById("toast-box");
        document.getElementById("toast-message").textContent = message;
        document.getElementById("toast-icon").textContent = icon;
        box.classList.remove("hidden");
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
          box.classList.add("hidden");
        }, 3500);
      }

      async function postProgress(url, payload) {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(payload)
        });

        let data;
        try {
          data = await response.json();
        } catch {
          throw new Error("The server returned an invalid response. Please try again.");
        }

        if (!response.ok) {
          throw new Error(data.message || "Could not save progress. Please try again.");
        }

        if (data.status !== "success") {
          throw new Error("Could not save progress. Please try again.");
        }

        return data;
      }

      // Native Web Audio Chime (Zero external libraries needed)
      function playChime() {
        try {
          const AudioCtx = window.AudioContext || window.webkitAudioContext;
          if (!AudioCtx) return;
          const ctx = new AudioCtx();
          const notes = [523.25, 659.25, 783.99]; // C5, E5, G5
          notes.forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = "sine";
            osc.frequency.value = freq;
            gain.gain.setValueAtTime(0.12, ctx.currentTime + i * 0.15);
            gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + i * 0.15 + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(ctx.currentTime + i * 0.15);
            osc.stop(ctx.currentTime + i * 0.15 + 0.36);
          });
        } catch (e) {}
      }

      // Japanese Speech Pronunciation via Web Speech API
      function speakJapanese(text) {
        try {
          if (!("speechSynthesis" in window)) {
            showToast(state.lang === "ja" ? "音声再生に対応していません。" : "আপনার ব্রাউজারে অডিও উচ্চারণ সমর্থিত নয়।", "🔇");
            return;
          }
          window.speechSynthesis.cancel();
          const utter = new SpeechSynthesisUtterance(text);
          utter.lang = "ja-JP";
          utter.rate = 0.92;
          window.speechSynthesis.speak(utter);
        } catch (e) {}
      }

      // Helper to format Japanese word cleanly: ひらがな (漢字) or カタカナ
      function formatJpWord(word) {
        return word.kanji ? `${word.reading} (${word.kanji})` : word.reading;
      }

      // =========================================================================
      // 3. STUDY TIMER LOGIC (5 / 10 / 15 Minutes)
      // =========================================================================
      let timerSecondsLeft = state.selectedMinutes * 60;
      let timerInterval = null;
      let isTimerRunning = false;

      const timerDisplayEl = document.getElementById("timer-display");
      const timerStartBtn = document.getElementById("timer-start-btn");
      const timerPauseBtn = document.getElementById("timer-pause-btn");
      const timerResetBtn = document.getElementById("timer-reset-btn");

      function updateTimerDisplay() {
        const mins = Math.floor(timerSecondsLeft / 60);
        const secs = timerSecondsLeft % 60;
        timerDisplayEl.textContent = `${String(mins).padStart(2, "0")}:${String(secs).padStart(2, "0")}`;
      }

      function setTimerPreset(minutes) {
        pauseTimer();
        state.selectedMinutes = minutes;
        timerSecondsLeft = minutes * 60;
        updateTimerDisplay();

        document.querySelectorAll(".timer-preset-btn").forEach(btn => {
          const m = parseInt(btn.dataset.mins, 10);
          btn.textContent = t().minPreset(m);
          if (m === minutes) {
            btn.className = "timer-preset-btn px-3 py-1.5 rounded-lg text-xs font-bold transition bg-indigo-600 text-white shadow-sm";
          } else {
            btn.className = "timer-preset-btn px-3 py-1.5 rounded-lg text-xs font-bold transition text-slate-600 hover:text-slate-900";
          }
        });
      }

      function startTimer() {
        if (isTimerRunning) return;
        if (timerSecondsLeft <= 0) {
          timerSecondsLeft = state.selectedMinutes * 60;
        }
        isTimerRunning = true;
        timerStartBtn.classList.add("hidden");
        timerPauseBtn.classList.remove("hidden");

        timerInterval = setInterval(() => {
          timerSecondsLeft--;
          updateTimerDisplay();
          if (timerSecondsLeft <= 0) {
            pauseTimer();
            playChime();
            showToast(t().timerFinishedToast(state.selectedMinutes), "⏰");
          }
        }, 1000);
      }

      function pauseTimer() {
        isTimerRunning = false;
        clearInterval(timerInterval);
        timerStartBtn.classList.remove("hidden");
        timerPauseBtn.classList.add("hidden");
      }

      function resetTimer() {
        pauseTimer();
        timerSecondsLeft = state.selectedMinutes * 60;
        updateTimerDisplay();
      }

      document.querySelectorAll(".timer-preset-btn").forEach(btn => {
        btn.addEventListener("click", () => setTimerPreset(parseInt(btn.dataset.mins, 10)));
      });
      timerStartBtn.addEventListener("click", startTimer);
      timerPauseBtn.addEventListener("click", pauseTimer);
      timerResetBtn.addEventListener("click", resetTimer);

      // =========================================================================
      // 4. PROGRESS & DAY TOGGLE RENDERING
      // =========================================================================
      const daySelectEl = document.getElementById("day-select");
      const dayPillsContainer = document.getElementById("day-pills-container");

      function updateProgressUI() {
        const dict = t();
        const completedCount = state.completedDays.length;
        const learnedWords = completedCount * 10;
        const percent = Math.round((completedCount / 60) * 100);
        document.getElementById("progress-text").textContent = dict.progressFormat(percent, completedCount, learnedWords);
        document.getElementById("progress-bar").style.width = `${percent}%`;

        // Update Exam tab badge
        const examBadge = document.getElementById("exam-lock-icon");
        examBadge.textContent = `${completedCount}/60`;
        if (completedCount === 60) {
          examBadge.className = "text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold";
        } else {
          examBadge.className = "text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full";
        }
      }

      function populateDayDropdown() {
        const dict = t();
        daySelectEl.innerHTML = "";
        for (let d = 1; d <= 60; d++) {
          const isDone = state.completedDays.includes(d);
          const opt = document.createElement("option");
          opt.value = d;
          opt.textContent = dict.dayOption(d, isDone);
          if (d === state.currentDay) opt.selected = true;
          daySelectEl.appendChild(opt);
        }
      }

      function renderDayPills() {
        dayPillsContainer.innerHTML = "";
        if (state.showAllDayPills) {
          dayPillsContainer.className = "grid grid-cols-6 sm:grid-cols-10 md:grid-cols-12 gap-1.5 pt-1";
        } else {
          dayPillsContainer.className = "flex gap-1.5 overflow-x-auto pb-2";
        }

        for (let d = 1; d <= 60; d++) {
          const isDone = state.completedDays.includes(d);
          const isActive = d === state.currentDay;
          const btn = document.createElement("button");
          btn.type = "button";
          btn.textContent = state.lang === "ja"
            ? `${d}日${isDone ? "✓" : ""}`
            : `D${d}${isDone ? "✓" : ""}`;
          btn.title = state.lang === "ja"
            ? `${d}日 ${isDone ? "(完了)" : ""}`
            : `Day ${d} ${isDone ? "(সম্পন্ন)" : ""}`;

          let classes = "px-2.5 py-1.5 rounded-lg text-xs font-bold shrink-0 transition border ";
          if (isActive) {
            classes += "bg-indigo-600 text-white border-indigo-600 shadow-sm scale-105";
          } else if (isDone) {
            classes += "bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100";
          } else {
            classes += "bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100";
          }
          btn.className = classes;
          btn.addEventListener("click", () => selectDay(d));
          dayPillsContainer.appendChild(btn);
        }

        if (!state.showAllDayPills) {
          const activeBtn = dayPillsContainer.children[state.currentDay - 1];
          if (activeBtn) {
            activeBtn.scrollIntoView({ behavior: "smooth", block: "nearest", inline: "center" });
          }
        }
      }

      function selectDay(dayNum) {
        if (dayNum < 1) dayNum = 1;
        if (dayNum > 60) dayNum = 60;
        state.currentDay = dayNum;

        const dict = t();

        document.getElementById("active-day-title").textContent = dict.dayTitle(dayNum);
        document.getElementById("prev-day-btn").disabled = dayNum === 1;
        document.getElementById("next-day-btn").disabled = dayNum === 60;

        const isCompleted = state.completedDays.includes(dayNum);
        const statusBadge = document.getElementById("day-status-badge");
        const completeBtn = document.getElementById("mark-complete-btn");

        if (isCompleted) {
          statusBadge.textContent = dict.dayCompletedBadge;
          statusBadge.className = "px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800";
          completeBtn.textContent = dict.unmarkCompleteBtn;
          completeBtn.className = "px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm transition flex items-center gap-2";
        } else {
          statusBadge.textContent = dict.dayInProgressBadge;
          statusBadge.className = "px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800";
          completeBtn.textContent = dict.markCompleteBtn;
          completeBtn.className = "px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-sm transition flex items-center gap-2";
        }

        populateDayDropdown();
        renderDayPills();
        renderStudyCards();
        renderDailyMockTest();
      }

      document.getElementById("prev-day-btn").addEventListener("click", () => selectDay(state.currentDay - 1));
      document.getElementById("next-day-btn").addEventListener("click", () => selectDay(state.currentDay + 1));
      daySelectEl.addEventListener("change", (e) => selectDay(parseInt(e.target.value, 10)));
      document.getElementById("toggle-grid-btn").addEventListener("click", () => {
        state.showAllDayPills = !state.showAllDayPills;
        renderDayPills();
      });

      document.getElementById("mark-complete-btn").addEventListener("click", async (event) => {
        const dict = t();
        const d = state.currentDay;
        const completed = !state.completedDays.includes(d);
        const button = event.currentTarget;
        button.disabled = true;

        try {
          const result = await postProgress(
            document.querySelector('meta[name="progress-complete-url"]').content,
            { day_number: d, completed }
          );
          state.completedDays = result.completed_day_numbers.map(Number);
          updateProgressUI();
          selectDay(d);

          if (completed) {
            showToast(dict.dayMarkedDoneToast(d, result.percentage), "🎉");
          } else {
            showToast(dict.dayMarkedUndoneToast(d), "ℹ️");
          }
        } catch (error) {
          showToast(error.message, "⚠️");
        } finally {
          button.disabled = false;
        }
      });

      // =========================================================================
      // 5. STUDY MODE CARDS (10 WORDS)
      // Format: ひらがな (漢字) - বাংলা অর্থ
      // =========================================================================
      function renderStudyCards() {
        const dict = t();
        const grid = document.getElementById("vocab-cards-grid");
        grid.innerHTML = "";
        const words = VOCAB_BY_DAY[state.currentDay] || [];

        words.forEach((word) => {
          const card = document.createElement("div");
          card.className = "bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200 hover:border-indigo-300 transition flex items-center justify-between gap-3";

          let kanjiHtml = "";
          if (word.kanji) {
            kanjiHtml = state.hideKanji
              ? `<span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-400 text-xs font-medium cursor-pointer reveal-kanji-btn" title="${dict.kanjiHiddenTitle}">${dict.kanjiHiddenText}</span>`
              : `<span class="font-bold font-jp text-indigo-800">(${word.kanji})</span>`;
          }

          // Vocabulary meaning (word.bangla) ALWAYS stays in Bengali
          const meaningHtml = state.hideMeanings
            ? `<button type="button" class="px-3 py-1 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition reveal-meaning-btn">${dict.revealMeaningBtn}</button>`
            : `<span class="font-bold text-slate-900 font-sans">${word.bangla}</span>`;

          card.innerHTML = `
            <div class="flex items-center gap-3.5 min-w-0 flex-1">
              <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-sm flex items-center justify-center shrink-0 border border-indigo-100">
                ${fmtNum(word.numInDay)}
              </div>
              <div class="text-base sm:text-lg md:text-xl leading-snug flex flex-wrap items-center gap-x-2 gap-y-1">
                <span class="font-bold font-jp text-slate-900">${word.reading}</span>
                ${word.kanji ? `<span class="kanji-slot">${kanjiHtml}</span>` : ""}
                <span class="text-slate-400 font-bold">-</span>
                <span class="meaning-slot">${meaningHtml}</span>
              </div>
            </div>
            <button type="button" class="speak-btn p-2.5 rounded-xl bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 transition shrink-0" title="${dict.speakTitle}" aria-label="${dict.speakTitle}">
              🔊
            </button>
          `;

          card.querySelector(".speak-btn").addEventListener("click", () => {
            speakJapanese(word.reading);
          });

          const revealMeaningBtn = card.querySelector(".reveal-meaning-btn");
          if (revealMeaningBtn) {
            revealMeaningBtn.addEventListener("click", () => {
              card.querySelector(".meaning-slot").innerHTML = `<span class="font-bold text-slate-900 font-sans">${word.bangla}</span>`;
            });
          }

          const revealKanjiBtn = card.querySelector(".reveal-kanji-btn");
          if (revealKanjiBtn && word.kanji) {
            revealKanjiBtn.addEventListener("click", () => {
              card.querySelector(".kanji-slot").innerHTML = `<span class="font-bold font-jp text-indigo-800">(${word.kanji})</span>`;
            });
          }

          grid.appendChild(card);
        });
      }

      document.getElementById("toggle-meanings-btn").addEventListener("click", () => {
        state.hideMeanings = !state.hideMeanings;
        renderStudyCards();
      });

      document.getElementById("toggle-kanji-btn").addEventListener("click", () => {
        state.hideKanji = !state.hideKanji;
        renderStudyCards();
      });

      // =========================================================================
      // 6. DAILY MOCK TEST GENERATOR & GRADER (7/10 Pass -> Auto-Complete Day)
      // =========================================================================
      let currentMockQuestions = [];

      function shuffleArray(arr) {
        const copy = [...arr];
        for (let i = copy.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [copy[i], copy[j]] = [copy[j], copy[i]];
        }
        return copy;
      }

      function generateMCQSet(targetWords, poolWords) {
        const dict = t();
        return shuffleArray(targetWords).map((word, index) => {
          const isJpToBn = index % 2 === 0;
          const distractors = shuffleArray(poolWords.filter(w => w.id !== word.id)).slice(0, 3);
          const jpLabel = formatJpWord(word);

          if (isJpToBn) {
            const options = shuffleArray([word.bangla, ...distractors.map(d => d.bangla)]);
            return {
              qNum: index + 1,
              prompt: dict.mockPromptJpToBn(jpLabel),
              correctAnswer: word.bangla,
              options,
              userAnswer: null
            };
          } else {
            const options = shuffleArray([
              jpLabel,
              ...distractors.map(d => formatJpWord(d))
            ]);
            return {
              qNum: index + 1,
              prompt: dict.mockPromptBnToJp(word.bangla),
              correctAnswer: jpLabel,
              options,
              userAnswer: null
            };
          }
        });
      }

      function renderDailyMockTest() {
        const dict = t();
        const d = state.currentDay;
        document.getElementById("submit-mock-btn").disabled = false;
        document.getElementById("mock-header-title").textContent = dict.mockTitle(d);
        document.getElementById("mock-result-banner").classList.add("hidden");

        const dayWords = VOCAB_BY_DAY[d];
        const allWords = Object.values(VOCAB_BY_DAY).flat();
        currentMockQuestions = generateMCQSet(dayWords, allWords);

        const container = document.getElementById("mock-questions-list");
        container.innerHTML = "";

        currentMockQuestions.forEach((q, qIdx) => {
          const qBox = document.createElement("div");
          qBox.className = "p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3";
          qBox.innerHTML = `
            <p class="font-bold text-slate-900 text-base">
              <span class="text-indigo-600 mr-1.5">${fmtNum(q.qNum)}.</span> ${q.prompt}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" data-qidx="${qIdx}">
              ${q.options.map((opt) => `
                <button type="button" data-opt="${opt.replace(/"/g, '&quot;')}" class="mock-opt-btn text-left px-4 py-3 rounded-xl bg-white border border-slate-200 hover:border-indigo-400 text-sm font-medium text-slate-800 transition">
                  ${opt}
                </button>
              `).join("")}
            </div>
            <div class="q-feedback hidden text-xs font-bold pt-1"></div>
          `;

          qBox.querySelectorAll(".mock-opt-btn").forEach(btn => {
            btn.addEventListener("click", () => {
              q.userAnswer = btn.dataset.opt;
              qBox.querySelectorAll(".mock-opt-btn").forEach(b => {
                b.classList.remove("ring-2", "ring-indigo-600", "bg-indigo-50", "border-indigo-600");
              });
              btn.classList.add("ring-2", "ring-indigo-600", "bg-indigo-50", "border-indigo-600");
            });
          });

          container.appendChild(qBox);
        });
      }

      document.getElementById("restart-mock-btn").addEventListener("click", renderDailyMockTest);

      document.getElementById("submit-mock-btn").addEventListener("click", async (event) => {
        const dict = t();
        const unanswered = currentMockQuestions.filter(q => q.userAnswer === null).length;
        if (unanswered > 0) {
          showToast(dict.mockUnansweredToast(unanswered), "⚠️");
          return;
        }

        let score = 0;
        const qBoxes = document.getElementById("mock-questions-list").children;

        currentMockQuestions.forEach((q, idx) => {
          const box = qBoxes[idx];
          const fb = box.querySelector(".q-feedback");
          fb.classList.remove("hidden");

          box.querySelectorAll(".mock-opt-btn").forEach(btn => {
            btn.disabled = true;
            const val = btn.dataset.opt;
            if (val === q.correctAnswer) {
              btn.className = "mock-opt-btn text-left px-4 py-3 rounded-xl bg-emerald-100 border-2 border-emerald-500 text-sm font-bold text-emerald-900";
            } else if (val === q.userAnswer && val !== q.correctAnswer) {
              btn.className = "mock-opt-btn text-left px-4 py-3 rounded-xl bg-rose-100 border-2 border-rose-500 text-sm font-bold text-rose-900";
            }
          });

          if (q.userAnswer === q.correctAnswer) {
            score++;
            fb.textContent = dict.mockCorrect;
            fb.className = "q-feedback text-xs font-bold pt-1 text-emerald-700";
          } else {
            fb.textContent = dict.mockWrong(q.correctAnswer);
            fb.className = "q-feedback text-xs font-bold pt-1 text-rose-700";
          }
        });

        const submitButton = event.currentTarget;
        submitButton.disabled = true;
        let savedProgress;
        try {
          savedProgress = await postProgress(
            document.querySelector('meta[name="mock-test-url"]').content,
            { day_number: state.currentDay, score }
          );
        } catch (error) {
          showToast(error.message, "⚠️");
          submitButton.disabled = false;
          return;
        }
        state.completedDays = savedProgress.completed_day_numbers.map(Number);

        const banner = document.getElementById("mock-result-banner");
        banner.classList.remove("hidden");
        document.getElementById("mock-score-text").textContent = dict.mockScore(score, score * 10);

        if (savedProgress.passed) {
          document.getElementById("mock-feedback-text").textContent = dict.mockPassFeedback(state.currentDay);
          updateProgressUI();
          populateDayDropdown();
          renderDayPills();
          const statusBadge = document.getElementById("day-status-badge");
          const completeBtn = document.getElementById("mark-complete-btn");
          statusBadge.textContent = dict.dayCompletedBadge;
          statusBadge.className = "px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800";
          completeBtn.textContent = dict.unmarkCompleteBtn;
          completeBtn.className = "px-5 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm transition flex items-center gap-2";
          playChime();
        } else {
          document.getElementById("mock-feedback-text").textContent = dict.mockRetryFeedback;
        }
      });

      // =========================================================================
      // 7. GRAND FINAL EXAM (DAY 1 - DAY 60)
      // =========================================================================
      let finalExamQuestions = [];
      let examTimerInterval = null;
      let examSecondsLeft = 600;

      function formatExamClock(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        const clock = `${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;
        return t().examTimePrefix(clock);
      }

      function updateExamTabView() {
        const dict = t();
        const doneCount = state.completedDays.length;
        const desc = document.getElementById("exam-unlock-desc");
        if (doneCount < 60) {
          desc.innerHTML = dict.examDescIncomplete(doneCount);
        } else {
          desc.innerHTML = dict.examDescComplete;
        }
      }

      document.getElementById("start-final-exam-btn").addEventListener("click", () => {
        const allWords = Object.values(VOCAB_BY_DAY).flat();
        const sampled20 = shuffleArray(allWords).slice(0, 20);
        finalExamQuestions = generateMCQSet(sampled20, allWords);

        document.getElementById("final-exam-body").classList.remove("hidden");
        document.getElementById("final-exam-result").classList.add("hidden");
        document.getElementById("submit-final-exam-btn").disabled = false;

        const qContainer = document.getElementById("final-exam-questions");
        qContainer.innerHTML = "";

        finalExamQuestions.forEach((q) => {
          const box = document.createElement("div");
          box.className = "p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3";
          box.innerHTML = `
            <p class="font-bold text-slate-900 text-base">
              <span class="text-amber-600 mr-1.5">${fmtNum(q.qNum)}.</span> ${q.prompt}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
              ${q.options.map(opt => `
                <button type="button" data-opt="${opt.replace(/"/g, '&quot;')}" class="exam-opt-btn text-left px-4 py-3 rounded-xl bg-white border border-slate-200 hover:border-amber-500 text-sm font-medium text-slate-800 transition">
                  ${opt}
                </button>
              `).join("")}
            </div>
            <div class="exam-fb hidden text-xs font-bold pt-1"></div>
          `;

          box.querySelectorAll(".exam-opt-btn").forEach(btn => {
            btn.addEventListener("click", () => {
              q.userAnswer = btn.dataset.opt;
              box.querySelectorAll(".exam-opt-btn").forEach(b => {
                b.classList.remove("ring-2", "ring-amber-500", "bg-amber-50", "border-amber-500");
              });
              btn.classList.add("ring-2", "ring-amber-500", "bg-amber-50", "border-amber-500");
            });
          });

          qContainer.appendChild(box);
        });

        clearInterval(examTimerInterval);
        examSecondsLeft = 600;
        const timerEl = document.getElementById("exam-timer-display");
        timerEl.textContent = formatExamClock(examSecondsLeft);
        examTimerInterval = setInterval(() => {
          examSecondsLeft--;
          timerEl.textContent = formatExamClock(examSecondsLeft);
          if (examSecondsLeft <= 0) {
            clearInterval(examTimerInterval);
            gradeFinalExam(true);
          }
        }, 1000);
      });

      function gradeFinalExam(autoSubmitted = false) {
        const dict = t();
        if (!autoSubmitted) {
          const left = finalExamQuestions.filter(q => q.userAnswer === null).length;
          if (left > 0) {
            showToast(dict.examUnansweredToast(left), "⚠️");
            return;
          }
        }

        clearInterval(examTimerInterval);
        document.getElementById("submit-final-exam-btn").disabled = true;

        let score = 0;
        const boxes = document.getElementById("final-exam-questions").children;

        finalExamQuestions.forEach((q, idx) => {
          const box = boxes[idx];
          const fb = box.querySelector(".exam-fb");
          fb.classList.remove("hidden");

          box.querySelectorAll(".exam-opt-btn").forEach(btn => {
            btn.disabled = true;
            const val = btn.dataset.opt;
            if (val === q.correctAnswer) {
              btn.className = "exam-opt-btn text-left px-4 py-3 rounded-xl bg-emerald-100 border-2 border-emerald-500 text-sm font-bold text-emerald-900";
            } else if (val === q.userAnswer && val !== q.correctAnswer) {
              btn.className = "exam-opt-btn text-left px-4 py-3 rounded-xl bg-rose-100 border-2 border-rose-500 text-sm font-bold text-rose-900";
            }
          });

          if (q.userAnswer === q.correctAnswer) {
            score++;
            fb.textContent = dict.examCorrect;
            fb.className = "exam-fb text-xs font-bold pt-1 text-emerald-700";
          } else {
            fb.textContent = dict.examWrong(q.correctAnswer);
            fb.className = "exam-fb text-xs font-bold pt-1 text-rose-700";
          }
        });

        const resBox = document.getElementById("final-exam-result");
        resBox.classList.remove("hidden");
        const percent = Math.round((score / 20) * 100);
        const passed = score >= 16;

        if (passed) {
          resBox.className = "flex-1 rounded-2xl p-5 border bg-emerald-50 border-emerald-300 text-emerald-950";
          document.getElementById("final-exam-grade").textContent = dict.examPassTitle;
          document.getElementById("final-exam-summary").textContent = dict.examPassSummary(score, percent);
          playChime();
        } else {
          resBox.className = "flex-1 rounded-2xl p-5 border bg-amber-50 border-amber-300 text-amber-950";
          document.getElementById("final-exam-grade").textContent = dict.examFailTitle(score, percent);
          document.getElementById("final-exam-summary").textContent = dict.examFailSummary;
        }
      }

      document.getElementById("submit-final-exam-btn").addEventListener("click", () => gradeFinalExam(false));

      // =========================================================================
      // 8. BILINGUAL UI SWITCHER, TAB SWITCHING & RESET HANDLERS
      // =========================================================================
      function applyLanguageUI() {
        const dict = t();
        document.documentElement.lang = state.lang === "ja" ? "ja" : "bn";

        const bnBtn = document.getElementById("lang-bn-btn");
        const jaBtn = document.getElementById("lang-ja-btn");
        if (state.lang === "bn") {
          bnBtn.className = "px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition bg-white text-slate-950 shadow";
          jaBtn.className = "px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition text-indigo-200 hover:text-white font-jp";
        } else {
          jaBtn.className = "px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition bg-white text-slate-950 shadow font-jp";
          bnBtn.className = "px-3 sm:px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold transition text-indigo-200 hover:text-white";
        }

        document.getElementById("app-title").textContent = dict.appTitle;
        document.getElementById("header-badge").textContent = dict.headerBadge;
        document.getElementById("app-subtitle").textContent = dict.appSubtitle;
        document.getElementById("progress-label").textContent = dict.progressLabel;

        document.getElementById("day-kicker").textContent = dict.dayKicker;
        document.getElementById("prev-day-btn").textContent = dict.prevDay;
        document.getElementById("prev-day-btn").title = dict.prevDay;
        document.getElementById("next-day-btn").textContent = dict.nextDay;
        document.getElementById("next-day-btn").title = dict.nextDay;
        document.getElementById("quick-day-label").textContent = dict.quickDayLabel;
        document.getElementById("toggle-grid-btn").textContent = dict.toggleGridBtn;

        document.getElementById("timer-kicker").textContent = dict.timerKicker;
        document.getElementById("timer-heading").textContent = dict.timerHeading;
        document.getElementById("timer-remaining-label").textContent = dict.timerRemaining;
        document.getElementById("timer-start-btn").textContent = dict.timerStart;
        document.getElementById("timer-pause-btn").textContent = dict.timerPause;
        document.getElementById("timer-reset-btn").title = dict.timerResetTitle;

        document.querySelectorAll(".timer-preset-btn").forEach(btn => {
          const m = parseInt(btn.dataset.mins, 10);
          btn.textContent = dict.minPreset(m);
        });

        document.getElementById("tab-study").textContent = dict.tabStudy;
        document.getElementById("tab-mock").textContent = dict.tabMock;
        document.getElementById("tab-exam-label").textContent = dict.tabExam;
        document.getElementById("reset-all-progress-btn").textContent = dict.resetProgressBtn;

        document.getElementById("toggle-meanings-btn").textContent = dict.toggleMeaningsBtn;
        document.getElementById("toggle-kanji-btn").textContent = dict.toggleKanjiBtn;
        document.getElementById("start-day-mock-cta").textContent = dict.startDayMockCta;

        document.getElementById("mock-kicker").textContent = dict.mockKicker;
        document.getElementById("mock-subtitle").textContent = dict.mockSubtitle;
        document.getElementById("restart-mock-btn").textContent = dict.restartMockBtn;
        document.getElementById("submit-mock-btn").textContent = dict.submitMockBtn;

        document.getElementById("exam-main-title").textContent = dict.examMainTitle;
        document.getElementById("start-final-exam-btn").textContent = dict.startFinalExamBtn;
        document.getElementById("exam-q-count-label").textContent = dict.examQCount;
        document.getElementById("submit-final-exam-btn").textContent = dict.submitFinalExamBtn;

        document.getElementById("confirm-title").textContent = dict.confirmTitle;
        document.getElementById("confirm-desc").textContent = dict.confirmDesc;
        document.getElementById("confirm-cancel-btn").textContent = dict.confirmCancel;
        document.getElementById("confirm-ok-btn").textContent = dict.confirmOk;
        document.getElementById("app-footer").textContent = dict.footerText;

        updateProgressUI();
        selectDay(state.currentDay);
        updateExamTabView();
      }

      function setLanguage(langCode) {
        if (langCode !== "bn" && langCode !== "ja") return;
        state.lang = langCode;
        applyLanguageUI();
      }

      document.getElementById("lang-bn-btn").addEventListener("click", () => setLanguage("bn"));
      document.getElementById("lang-ja-btn").addEventListener("click", () => setLanguage("ja"));

      function switchTab(tabName) {
        const views = {
          study: document.getElementById("view-study"),
          mock: document.getElementById("view-mock"),
          exam: document.getElementById("view-exam")
        };
        const tabs = {
          study: document.getElementById("tab-study"),
          mock: document.getElementById("tab-mock"),
          exam: document.getElementById("tab-exam")
        };

        Object.keys(views).forEach(k => {
          if (k === tabName) {
            views[k].classList.remove("hidden");
            tabs[k].className = "mode-tab px-4 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 text-white shadow-sm transition flex items-center gap-1.5";
          } else {
            views[k].classList.add("hidden");
            tabs[k].className = "mode-tab px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition flex items-center gap-1.5";
          }
        });

        if (tabName === "exam") {
          updateExamTabView();
        }
      }

      document.getElementById("tab-study").addEventListener("click", () => switchTab("study"));
      document.getElementById("tab-mock").addEventListener("click", () => switchTab("mock"));
      document.getElementById("tab-exam").addEventListener("click", () => switchTab("exam"));
      document.getElementById("start-day-mock-cta").addEventListener("click", () => switchTab("mock"));

      // Custom Confirmation Modal for Reset
      const confirmModal = document.getElementById("confirm-modal");
      document.getElementById("reset-all-progress-btn").addEventListener("click", () => {
        confirmModal.classList.remove("hidden");
      });

      document.getElementById("confirm-cancel-btn").addEventListener("click", () => {
        confirmModal.classList.add("hidden");
      });

      document.getElementById("confirm-ok-btn").addEventListener("click", async (event) => {
        const button = event.currentTarget;
        button.disabled = true;
        try {
          await postProgress(
            document.querySelector('meta[name="progress-reset-url"]').content,
            {}
          );
          state.completedDays = [];
          state.currentDay = 1;
          updateProgressUI();
          selectDay(1);
          confirmModal.classList.add("hidden");
          showToast(t().resetDoneToast, "🔄");
        } catch (error) {
          showToast(error.message, "⚠️");
        } finally {
          button.disabled = false;
        }
     });

      // =========================================================================
      // 9. INITIALIZE APP ON DOM CONTENT LOADED
      // =========================================================================
      function initApp() {
        applyLanguageUI();
        setTimerPreset(state.selectedMinutes || 5);
        updateProgressUI();
        selectDay(state.currentDay || 1);
      }

      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initApp);
      } else {
        initApp();
      }
    })();
  </script>
</body>
</html>