<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-stone-50 text-stone-900 antialiased dark:bg-neutral-950 dark:text-stone-100">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_right,_#fbbf24_0%,_transparent_40%),,linear-gradient(180deg,_#fffbeb_0%,_#f5f5f4_60%,_#fafaf9_100%)] dark:bg-[radial-gradient(circle_at_top_right,_#78350f_0%,_transparent_45%),linear-gradient(180deg,_#0a0a0a_0%,_#171717_60%,_#0f0f0f_100%)]"></div>

        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 text-sm font-bold text-white shadow-lg shadow-teal-800/20">SS</div>
                <div>
                    <p class="text-lg font-semibold tracking-tight">SmartSpend</p>
                    <p class="text-xs text-stone-600 dark:text-stone-400">Personal finance tracker</p>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="rounded-lg border border-stone-300 bg-white/80 px-4 py-2 text-sm font-medium text-stone-800 backdrop-blur transition hover:border-stone-400 hover:bg-white dark:border-neutral-700 dark:bg-neutral-900/70 dark:text-stone-100 dark:hover:border-neutral-500">
                        Login
                    </a>
                @endif

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-teal-900/20 transition hover:bg-teal-500">
                        Get Started
                    </a>
                @elseif (Route::has('login'))
                    <a href="{{ route('login') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-teal-900/20 transition hover:bg-teal-500">
                        Get Started
                    </a>
                @endif
            </nav>
        </header>

        <main class="mx-auto grid max-w-6xl gap-10 px-6 pb-20 pt-8 lg:grid-cols-2 lg:items-center lg:px-8">
            <section>
                <p class="mb-3 inline-flex rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-700 dark:border-teal-900/60 dark:bg-teal-900/20 dark:text-teal-300">
                    Spend smarter every day
                </p>
                <h1 class="text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Take control of your money with clarity and confidence.
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-stone-700 dark:text-stone-300 sm:text-lg">
                    SmartSpend helps you track expenses, build budgets, manage wallet balances, and lock savings goals so you can make better financial decisions every month.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-6 py-3 text-sm font-bold text-stone-950 shadow-xl shadow-amber-900/20 transition hover:bg-amber-400">
                            Create Free Account
                        </a>
                    @endif

                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="rounded-xl border border-stone-300 bg-white px-6 py-3 text-sm font-semibold text-stone-800 transition hover:border-stone-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-stone-100 dark:hover:border-neutral-500">
                            Login to Dashboard
                        </a>
                    @endif
                </div>

                <div class="mt-10 grid grid-cols-3 gap-4">
                    <div class="rounded-xl border border-stone-200 bg-white/85 p-4 backdrop-blur dark:border-neutral-800 dark:bg-neutral-900/70">
                        <p class="text-2xl font-black">Budgets</p>
                        <p class="text-xs text-stone-600 dark:text-stone-400">Category and monthly controls</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white/85 p-4 backdrop-blur dark:border-neutral-800 dark:bg-neutral-900/70">
                        <p class="text-2xl font-black">Wallet</p>
                        <p class="text-xs text-stone-600 dark:text-stone-400">Deposits, withdrawals, lock funds</p>
                    </div>
                    <div class="rounded-xl border border-stone-200 bg-white/85 p-4 backdrop-blur dark:border-neutral-800 dark:bg-neutral-900/70">
                        <p class="text-2xl font-black">Alerts</p>
                        <p class="text-xs text-stone-600 dark:text-stone-400">Email notifications for key actions</p>
                    </div>
                </div>
            </section>

            <section class="grid gap-4">
                <article class="rounded-2xl border border-stone-200 bg-white/90 p-6 shadow-lg backdrop-blur dark:border-neutral-800 dark:bg-neutral-900/80">
                    <h2 class="text-lg font-bold">What SmartSpend does</h2>
                    <ul class="mt-4 space-y-3 text-sm text-stone-700 dark:text-stone-300">
                        <li class="rounded-lg bg-stone-100 px-3 py-2 dark:bg-neutral-800">Track one-time and recurring expenses with categories.</li>
                        <li class="rounded-lg bg-stone-100 px-3 py-2 dark:bg-neutral-800">Set monthly budgets and monitor over-limit spending.</li>
                        <li class="rounded-lg bg-stone-100 px-3 py-2 dark:bg-neutral-800">Use wallet balances to lock money toward savings goals.</li>
                        <li class="rounded-lg bg-stone-100 px-3 py-2 dark:bg-neutral-800">Receive email alerts for withdrawals, locked funds, and budget breaches.</li>
                    </ul>
                </article>

                <article class="rounded-2xl border border-stone-200 bg-teal-600 p-6 text-white shadow-lg shadow-cyan-900/20">
                    <p class="text-sm font-semibold uppercase tracking-wider text-cyan-100">Start now</p>
                    <p class="mt-2 text-xl font-black">Build healthier money habits with a simple daily flow.</p>
                    <p class="mt-2 text-sm text-cyan-50">Add expenses, check budgets, and grow your locked savings consistently.</p>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
