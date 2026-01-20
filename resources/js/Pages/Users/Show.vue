<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Trash2, Mail, Shield, Calendar } from 'lucide-vue-next';
import AvatarWithModal from '@/Components/AvatarWithModal.vue';
import { Button } from '@/Components/ui/button';

interface User {
    id: number;
    role: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    avatar_url: string | null;
    initials: string;
    full_name: string;
    created_at: string;
}

const props = defineProps<{
    user: User;
}>();

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
        <div class="mb-6">
            <Link
                :href="route('users.index')"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Users
            </Link>
        </div>

        <!-- Profile Card -->
        <div class="overflow-hidden rounded-xl border border-border bg-card">
            <!-- Header with gradient background -->
            <div class="relative bg-gradient-to-r from-primary/10 via-primary/5 to-transparent px-6 pb-0 pt-6">
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-end sm:gap-6">
                    <!-- Avatar -->
                    <AvatarWithModal
                        :avatar_url="user.avatar_url"
                        :initials="user.initials"
                        :full_name="user.full_name"
                        size="lg"
                    />

                    <!-- Name and Role -->
                    <div class="flex flex-1 flex-col items-center pb-4 text-center sm:items-start sm:text-left">
                        <h1 class="text-2xl font-semibold text-foreground">{{ user.full_name }}</h1>
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                            <Shield class="h-3.5 w-3.5" />
                            {{ user.role }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pb-4">
                        <Button as-child variant="outline" size="sm">
                            <Link :href="route('users.edit', user.id)">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="destructive" size="sm" @click="deleteUser">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <!-- Email -->
                <div class="flex items-center gap-3 rounded-lg bg-muted/50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <Mail class="h-5 w-5 text-primary" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">Email</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ user.email }}</p>
                    </div>
                </div>

                <!-- Created -->
                <div class="flex items-center gap-3 rounded-lg bg-muted/50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <Calendar class="h-5 w-5 text-primary" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">Member Since</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ user.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
