<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-background">
        <!-- Navigation -->
        <nav class="border-b border-border bg-card">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <span class="text-xl font-bold text-foreground">EHR</span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link
                                href="/users"
                                class="inline-flex items-center border-b-2 border-primary px-1 pt-1 text-sm font-medium text-foreground"
                            >
                                Users
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        <header v-if="title" class="bg-card shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    {{ title }}
                </h1>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
