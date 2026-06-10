@php
    $variant = $variant ?? 'generic';
@endphp

<div id="page-loading-overlay" class="page-loading-overlay" aria-hidden="true">
    <div class="page-loading-shell">
        @switch($variant)
            @case('customer-home')
                <div class="page-loading-grid" style="gap: 1.25rem;">
                    <div class="skeleton-card">
                        <div class="skeleton-row">
                            <div class="skeleton skeleton-circle" style="width: 3rem; height: 3rem;"></div>
                            <div class="flex-1 space-y-2">
                                <div class="skeleton skeleton-line" style="width: 52%;"></div>
                                <div class="skeleton skeleton-line skeleton-muted" style="width: 38%; height: 0.7rem;"></div>
                            </div>
                            <div class="skeleton skeleton-line skeleton-circle" style="width: 5rem; height: 1.9rem;"></div>
                        </div>
                    </div>

                    <div class="page-loading-grid cols-2">
                        <div class="space-y-3">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="skeleton-card">
                                    <div class="skeleton-row" style="align-items: flex-start;">
                                        <div class="skeleton skeleton-circle" style="width: 6rem; height: 6rem;"></div>
                                        <div class="flex-1 space-y-3">
                                            <div class="skeleton skeleton-line" style="width: 72%;"></div>
                                            <div class="skeleton skeleton-line skeleton-muted" style="width: 92%; height: 0.75rem;"></div>
                                            <div class="skeleton skeleton-line skeleton-muted" style="width: 64%; height: 0.75rem;"></div>
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="skeleton skeleton-line" style="width: 38%; height: 1rem;"></div>
                                                <div class="skeleton skeleton-line skeleton-circle" style="width: 4.4rem; height: 2rem;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="space-y-3">
                            @for ($i = 0; $i < 4; $i++)
                                <div class="skeleton-card">
                                    <div class="skeleton-row">
                                        <div class="skeleton skeleton-circle" style="width: 3rem; height: 3rem;"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="skeleton skeleton-line" style="width: 70%;"></div>
                                            <div class="skeleton skeleton-line skeleton-muted" style="width: 45%; height: 0.75rem;"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                @break

            @case('admin-dashboard')
                <div class="page-loading-grid" style="gap: 1.25rem;">
                    <div class="skeleton-card">
                        <div class="skeleton-row" style="justify-content: space-between;">
                            <div class="space-y-2 flex-1">
                                <div class="skeleton skeleton-line" style="width: 34%; height: 1.2rem;"></div>
                                <div class="skeleton skeleton-line skeleton-muted" style="width: 58%; height: 0.75rem;"></div>
                            </div>
                            <div class="skeleton skeleton-line skeleton-circle" style="width: 8rem; height: 2.2rem;"></div>
                        </div>
                    </div>

                    <div class="page-loading-grid cols-3">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="skeleton-card space-y-3">
                                <div class="skeleton-row" style="justify-content: space-between;">
                                    <div class="skeleton skeleton-line" style="width: 40%; height: 0.7rem;"></div>
                                    <div class="skeleton skeleton-circle" style="width: 2rem; height: 2rem;"></div>
                                </div>
                                <div class="skeleton skeleton-line" style="width: 58%; height: 1.5rem;"></div>
                                <div class="skeleton skeleton-line skeleton-muted" style="width: 74%; height: 0.75rem;"></div>
                            </div>
                        @endfor
                    </div>

                    <div class="page-loading-grid cols-2">
                        <div class="skeleton-card space-y-3">
                            <div class="skeleton skeleton-line" style="width: 42%; height: 1rem;"></div>
                            @for ($i = 0; $i < 4; $i++)
                                <div class="skeleton-row">
                                    <div class="skeleton skeleton-circle" style="width: 2.6rem; height: 2.6rem;"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="skeleton skeleton-line" style="width: 72%;"></div>
                                        <div class="skeleton skeleton-line skeleton-muted" style="width: 48%; height: 0.7rem;"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="skeleton-card space-y-3">
                            <div class="skeleton skeleton-line" style="width: 46%; height: 1rem;"></div>
                            @for ($i = 0; $i < 5; $i++)
                                <div class="skeleton-row">
                                    <div class="skeleton skeleton-line" style="width: 70%; height: 0.8rem;"></div>
                                    <div class="skeleton skeleton-line skeleton-muted" style="width: 18%; height: 0.8rem;"></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                @break

            @case('menu-table')
                <div class="skeleton-card space-y-4">
                    <div class="skeleton-row" style="justify-content: space-between;">
                        <div class="space-y-2">
                            <div class="skeleton skeleton-line" style="width: 40%; height: 1.1rem;"></div>
                            <div class="skeleton skeleton-line skeleton-muted" style="width: 56%; height: 0.72rem;"></div>
                        </div>
                        <div class="skeleton skeleton-line skeleton-circle" style="width: 7rem; height: 2.2rem;"></div>
                    </div>

                    <div class="space-y-3">
                        @for ($i = 0; $i < 6; $i++)
                            <div class="skeleton-row">
                                <div class="skeleton skeleton-circle" style="width: 3rem; height: 3rem;"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton skeleton-line" style="width: 46%;"></div>
                                    <div class="skeleton skeleton-line skeleton-muted" style="width: 76%; height: 0.7rem;"></div>
                                </div>
                                <div class="skeleton skeleton-line" style="width: 5rem; height: 1rem;"></div>
                                <div class="skeleton skeleton-line skeleton-circle" style="width: 5.5rem; height: 2rem;"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('tracking')
                <div class="page-loading-grid" style="gap: 1rem;">
                    <div class="skeleton-card space-y-3">
                        <div class="skeleton-row">
                            <div class="skeleton skeleton-circle" style="width: 2.6rem; height: 2.6rem;"></div>
                            <div class="flex-1 space-y-2">
                                <div class="skeleton skeleton-line" style="width: 34%;"></div>
                                <div class="skeleton skeleton-line skeleton-muted" style="width: 62%; height: 0.7rem;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="skeleton-card space-y-4">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="skeleton-row" style="align-items: flex-start;">
                                <div class="skeleton skeleton-circle" style="width: 1.1rem; height: 1.1rem;"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton skeleton-line" style="width: 42%;"></div>
                                    <div class="skeleton skeleton-line skeleton-muted" style="width: 78%; height: 0.75rem;"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('notifications')
                <div class="page-loading-grid cols-2">
                    <div class="space-y-3">
                        <div class="skeleton-card space-y-3">
                            <div class="skeleton skeleton-line" style="width: 48%; height: 1rem;"></div>
                            @for ($i = 0; $i < 4; $i++)
                                <div class="skeleton-card skeleton-muted">
                                    <div class="skeleton-row">
                                        <div class="skeleton skeleton-circle" style="width: 2.5rem; height: 2.5rem;"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="skeleton skeleton-line" style="width: 58%;"></div>
                                            <div class="skeleton skeleton-line skeleton-muted" style="width: 82%; height: 0.72rem;"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="skeleton-card space-y-3">
                        <div class="skeleton skeleton-line" style="width: 45%; height: 1rem;"></div>
                        @for ($i = 0; $i < 7; $i++)
                            <div class="skeleton-row">
                                <div class="skeleton skeleton-circle" style="width: 0.8rem; height: 0.8rem;"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton skeleton-line" style="width: 72%;"></div>
                                    <div class="skeleton skeleton-line skeleton-muted" style="width: 52%; height: 0.68rem;"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('auth')
                <div class="skeleton-card space-y-4" style="width: min(100%, 26rem); margin-inline: auto;">
                    <div class="flex items-center justify-center">
                        <div class="skeleton skeleton-circle" style="width: 5rem; height: 5rem;"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="skeleton skeleton-line" style="width: 48%; height: 1.2rem; margin-inline: auto;"></div>
                        <div class="skeleton skeleton-line skeleton-muted" style="width: 68%; height: 0.75rem; margin-inline: auto;"></div>
                    </div>
                    <div class="space-y-3">
                        <div class="skeleton skeleton-line" style="width: 28%; height: 0.7rem;"></div>
                        <div class="skeleton skeleton-line" style="width: 100%; height: 3rem;"></div>
                        <div class="skeleton skeleton-line" style="width: 28%; height: 0.7rem;"></div>
                        <div class="skeleton skeleton-line" style="width: 100%; height: 3rem;"></div>
                        <div class="skeleton skeleton-line" style="width: 54%; height: 0.8rem;"></div>
                        <div class="skeleton skeleton-line" style="width: 100%; height: 3rem;"></div>
                    </div>
                </div>
                @break

            @default
                <div class="page-loading-grid cols-2">
                    <div class="skeleton-card space-y-3">
                        <div class="skeleton-row">
                            <div class="skeleton skeleton-circle" style="width: 2.8rem; height: 2.8rem;"></div>
                            <div class="flex-1 space-y-2">
                                <div class="skeleton skeleton-line" style="width: 36%;"></div>
                                <div class="skeleton skeleton-line skeleton-muted" style="width: 62%; height: 0.72rem;"></div>
                            </div>
                        </div>
                        <div class="skeleton skeleton-line" style="width: 88%; height: 8rem;"></div>
                    </div>
                    <div class="skeleton-card space-y-3">
                        <div class="skeleton skeleton-line" style="width: 48%; height: 1rem;"></div>
                        @for ($i = 0; $i < 4; $i++)
                            <div class="skeleton-row">
                                <div class="skeleton skeleton-circle" style="width: 2rem; height: 2rem;"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="skeleton skeleton-line" style="width: 68%;"></div>
                                    <div class="skeleton skeleton-line skeleton-muted" style="width: 48%; height: 0.68rem;"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
        @endswitch
    </div>
</div>
