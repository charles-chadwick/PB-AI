<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Search, Plus, Eye, Pencil, ChevronUp, ChevronDown, ChevronsUpDown } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';

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

const url_params = new URLSearchParams(window.location.search);
const search = ref(url_params.get('search') || '');
const sort_by = ref(url_params.get('sort_by') || 'created_at');
const sort_direction = ref(url_params.get('sort_direction') || 'desc');

watch(search, (value) => {
    router.get(route('users.index'), {
        search: value,
        sort_by: sort_by.value,
        sort_direction: sort_direction.value,
    }, {
        preserveState: true,
        replace: true,
    });
});

const sortBy = (column: string) => {
    if (sort_by.value === column) {
        sort_direction.value = sort_direction.value === 'asc' ? 'desc' : 'asc';
    } else {
        sort_by.value = column;
        sort_direction.value = 'asc';
    }

    router.get(route('users.index'), {
        search: search.value,
        sort_by: sort_by.value,
        sort_direction: sort_direction.value,
    }, {
        preserveState: true,
        replace: true,
    });
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
                :href="route('users.create')"
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
                        <th class="w-12 px-4 py-3"></th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                            <button
                                @click="sortBy('first_name')"
                                class="inline-flex items-center gap-1 hover:text-foreground"
                            >
                                First Name
                                <ChevronUp v-if="sort_by === 'first_name' && sort_direction === 'asc'" class="h-4 w-4" />
                                <ChevronDown v-else-if="sort_by === 'first_name' && sort_direction === 'desc'" class="h-4 w-4" />
                                <ChevronsUpDown v-else class="h-4 w-4 opacity-50" />
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                            <button
                                @click="sortBy('last_name')"
                                class="inline-flex items-center gap-1 hover:text-foreground"
                            >
                                Last Name
                                <ChevronUp v-if="sort_by === 'last_name' && sort_direction === 'asc'" class="h-4 w-4" />
                                <ChevronDown v-else-if="sort_by === 'last_name' && sort_direction === 'desc'" class="h-4 w-4" />
                                <ChevronsUpDown v-else class="h-4 w-4 opacity-50" />
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                            <button
                                @click="sortBy('email')"
                                class="inline-flex items-center gap-1 hover:text-foreground"
                            >
                                Email
                                <ChevronUp v-if="sort_by === 'email' && sort_direction === 'asc'" class="h-4 w-4" />
                                <ChevronDown v-else-if="sort_by === 'email' && sort_direction === 'desc'" class="h-4 w-4" />
                                <ChevronsUpDown v-else class="h-4 w-4 opacity-50" />
                            </button>
                        </th>
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
                        <td class="px-4 py-3">
                            <Avatar class="h-8 w-8">
                                <AvatarImage v-if="user.avatar_url" :src="user.avatar_url" />
                                <AvatarFallback>{{ user.initials }}</AvatarFallback>
                            </Avatar>
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ user.first_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ user.last_name }}
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
                                    :href="route('users.show', user.id)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Eye class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="route('users.edit', user.id)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-foreground">
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
