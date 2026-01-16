<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import { toast } from 'vue-sonner';
import { Card } from '@/Components/ui/card';
import { Toaster } from '@/Components/ui/sonner';

defineProps<{
    title?: string;
}>();

const page = usePage<{
    flash: {
        message: string | null;
        type: 'success' | 'error' | 'info' | 'warning' | null;
    };
}>();

const navigation_items = [
  {href: route('dashboard'), label: 'Dashboard'},
  {href: route('appointments.index'), label: 'Appointments'},
  {href: route('patients.index'), label: 'Patients'},
  {href: route('users.index'), label: 'Users'},
];

const showFlashMessage = () => {
    const flash = page.props.flash;
    if (flash?.message) {
        const type = flash.type || 'info';
        if (type === 'success') {
            toast.success(flash.message);
        } else if (type === 'error') {
            toast.error(flash.message);
        } else if (type === 'warning') {
            toast.warning(flash.message);
        } else {
            toast.info(flash.message);
        }
    }
};

let removeFinishListener: (() => void) | null = null;

onMounted(() => {
    removeFinishListener = router.on('finish', (event) => {
        if (event.detail.visit.completed) {
            showFlashMessage();
        }
    });
})

onUnmounted(() => {
    removeFinishListener?.();
});
</script>

<template>
    <div class="min-h-screen bg-stone-200">
        <!-- Toast Container -->
        <Toaster position="top-right" :duration="4000" />

        <!-- Navigation -->
        <nav class="border-b border-border bg-card">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex shrink-0 items-center">
                            <span class="text-xl font-bold text-foreground">EHR</span>
                        </div>
                      <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <Link
                            v-for="item in navigation_items"
                            :key="item.href"
                            :href="item.href"
                            :class="[
                              'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium transition-colors',
                              item.href === '/'
                                ? ($page.url === '/' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:border-muted hover:text-foreground')
                                : ($page.url === item.href || $page.url.startsWith(item.href + '/') ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:border-muted hover:text-foreground')
                            ]"
                        >
                          {{ item.label }}
                        </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Header -->
        <header v-if="title" class="bg-card shadow">
            <div class="mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-foreground">
                    {{ title }}
                </h1>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <Card class="p-6">
                    <slot />
                </Card>
            </div>
        </main>
    </div>
</template>
