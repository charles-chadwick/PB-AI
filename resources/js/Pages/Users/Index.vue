<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Search, Plus, Eye, Pencil, ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface User {
    id: number;
    role: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    created_at: string;
    created_by?: {
        id: number;
        first_name: string;
        last_name: string;
    };
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

const props = defineProps<{
    users: PaginatedUsers;
}>();

const search = ref(new URLSearchParams(window.location.search).get('search') || '');

watch(search, (value) => {
    router.get('/users', { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const fullName = (user: User) => {
    const parts = [user.first_name, user.middle_name, user.last_name].filter(Boolean);
    return parts.join(' ');
};
</script>

<template>
    <Head title="Users" />

    <AppLayout title="Users">
        <!-- Actions Bar -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search users..."
                    class="h-10 w-full rounded-md border border-input bg-background pl-10 pr-4 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 sm:w-64"
                />
            </div>

            <!-- Create Button -->
            <Link
                href="/users/create"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
            >
                <Plus class="h-4 w-4" />
                Add User
            </Link>
        </div>

        <!-- Users Table -->
        <div class="rounded-md border border-border">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border bg-muted/50">
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Name</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Role</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Created</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b border-border last:border-0"
                    >
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ fullName(user) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ user.email }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center rounded-full bg-secondary px-2.5 py-0.5 text-xs font-medium text-secondary-foreground">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">
                            {{ user.created_at }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="`/users/${user.id}`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Eye class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="`/users/${user.id}/edit`"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-foreground">
                            No users found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="mt-4 flex items-center justify-between">
            <p class="text-sm text-muted-foreground">
                Showing {{ (users.current_page - 1) * users.per_page + 1 }} to
                {{ Math.min(users.current_page * users.per_page, users.total) }} of
                {{ users.total }} results
            </p>
            <div class="flex items-center gap-1">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url || ''"
                    :class="[
                        'inline-flex h-8 min-w-8 items-center justify-center rounded-md px-3 text-sm',
                        link.active
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground',
                        !link.url && 'pointer-events-none opacity-50',
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
