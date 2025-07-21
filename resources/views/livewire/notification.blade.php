<div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-900 dark:to-slate-800">
    <div class="w-full max-w-lg p-8 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 dark:border-slate-700/50">
        <div class="flex items-center justify-center mb-6">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full mr-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5zm6 10V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h6a2 2 0 002-2z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Notificaciones</h2>
        </div>

        <div class="space-y-3 max-h-96 overflow-y-auto scrollbar-thin scrollbar-thumb-blue-200 dark:scrollbar-thumb-slate-600">
            @foreach(array_slice(array_reverse($messages), 0, 5) as $index => $msg)
                <div class="group relative bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-slate-700 dark:to-slate-600 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-slate-600 dark:hover:to-slate-500 rounded-xl px-5 py-4 shadow-sm hover:shadow-md transition-all duration-300 border border-blue-100 dark:border-slate-600 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full animate-pulse"></div>
                        </div>
                        <div class="flex-1">
                            <p class="text-slate-700 dark:text-slate-200 text-sm leading-relaxed font-medium">
                                {{ $msg }}
                            </p>
                            <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">
                                Hace {{ $index + 1 }} minuto{{ $index > 0 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($messages) > 5)
            <div class="mt-6 text-center">
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-200">
                    Ver todas las notificaciones ({{ count($messages) - 5 }} más)
                </button>
            </div>
        @endif
    </div>
</div>
