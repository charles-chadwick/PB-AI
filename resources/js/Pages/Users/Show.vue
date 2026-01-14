<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Trash2 } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';

interface User {
    id: number;
    role: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    email_verified_at: string | null;
    avatar_url: string | null;
    initials: string;
    full_name: string;
    created_at: string;
    updated_at: string;
    created_by?: {
        id: number;
        first_name: string;
        last_name: string;
    };
}

const props = defineProps<{
    user: User;
}>();

const created_by_name = computed(() => {
    if (!props.user.created_by) return 'System';
    return `${props.user.created_by.first_name} ${props.user.created_by.last_name}`;
});

const deleteUser = () => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('users.destroy', props.user.id));
    }
};
</script>

<template>
    <Head :title="user.full_name" />

    <AppLayout :title="user.full_name">
        <!-- Back Link -->
        <div class="mb-6 flex items-center justify-between">
            <Link
                :href="route('users.index')"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>

            <div class="flex items-center gap-2">
                <Link
                    :href="route('users.edit', user.id)"
                    class="inline-flex items-center gap-2 rounded-md bg-secondary px-3 py-2 text-sm font-medium text-secondary-foreground transition-colors hover:bg-secondary/80"
                >
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>
                <button
                    @click="deleteUser"
                    class="inline-flex items-center gap-2 rounded-md bg-destructive px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-destructive/90"
                >
                    <Trash2 class="h-4 w-4" />
                    Delete
                </button>
            </div>
        </div>

        <!-- User Details -->
        <div class="rounded-lg border border-border bg-card">
            <div class="border-b border-border px-6 py-4">
                <div class="flex items-center gap-4">
                    <Avatar class="h-16 w-16 text-lg">
                        <AvatarImage v-if="user.avatar_url" :src="user.avatar_url" />
                        <AvatarFallback>{{ user.initials }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <h2 class="text-lg font-medium text-foreground">{{ user.full_name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                    </div>
                </div>
            </div>
            <dl class="divide-y divide-border">
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Role</dt>
                    <dd class="col-span-2 text-sm text-foreground">
                        <span class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-medium text-secondary-foreground">
                            {{ user.role }}
                        </span>
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">First Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ user.first_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Middle Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">
                        {{ user.middle_name || '-' }}
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Last Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ user.last_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ user.email }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Email Verified</dt>
                    <dd class="col-span-2 text-sm text-foreground">
                        {{ user.email_verified_at || 'Not verified' }}
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Created By</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ created_by_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Created At</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ user.created_at }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Updated At</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ user.updated_at }}</dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
