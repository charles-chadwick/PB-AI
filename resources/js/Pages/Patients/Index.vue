<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Search, Plus, Eye, Pencil, ChevronUp, ChevronDown, ChevronsUpDown } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';

interface Patient {
    id: number;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    date_of_birth: string;
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

interface PaginatedPatients {
    data: Patient[];
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
    patients: PaginatedPatients;
}>();

const url_params = new URLSearchParams(window.location.search);
const search = ref(url_params.get('search') || '');
const sort_by = ref(url_params.get('sort_by') || 'created_at');
const sort_direction = ref(url_params.get('sort_direction') || 'desc');

watch(search, (value) => {
    router.get(route('patients.index'), {
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

    router.get(route('patients.index'), {
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
    <Head title="Patients" />

    <AppLayout title="Patients">
        <!-- Actions Bar -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Search -->
            <div class="relative">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search patients..."
                    class="h-10 w-full rounded-md border border-input bg-background pl-10 pr-4 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 sm:w-64"
                />
            </div>

            <!-- Create Button -->
            <Link
                :href="route('patients.create')"
                class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
            >
                <Plus class="h-4 w-4" />
                Add Patient
            </Link>
        </div>

        <!-- Patients Table -->
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
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">
                            <button
                                @click="sortBy('date_of_birth')"
                                class="inline-flex items-center gap-1 hover:text-foreground"
                            >
                                Date of Birth
                                <ChevronUp v-if="sort_by === 'date_of_birth' && sort_direction === 'asc'" class="h-4 w-4" />
                                <ChevronDown v-else-if="sort_by === 'date_of_birth' && sort_direction === 'desc'" class="h-4 w-4" />
                                <ChevronsUpDown v-else class="h-4 w-4 opacity-50" />
                            </button>
                        </th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-muted-foreground">Created</th>
                        <th class="px-4 py-3 text-right text-sm font-medium text-muted-foreground">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="patient in patients.data"
                        :key="patient.id"
                        class="border-b border-border last:border-0"
                    >
                        <td class="px-4 py-3">
                            <Avatar class="h-8 w-8">
                                <AvatarImage v-if="patient.avatar_url" :src="patient.avatar_url" />
                                <AvatarFallback>{{ patient.initials }}</AvatarFallback>
                            </Avatar>
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ patient.first_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ patient.last_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ patient.email }}
                        </td>
                        <td class="px-4 py-3 text-sm text-foreground">
                            {{ patient.date_of_birth }}
                        </td>
                        <td class="px-4 py-3 text-sm text-muted-foreground">
                            {{ patient.created_at }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('patients.show', patient.id)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Eye class="h-4 w-4" />
                                </Link>
                                <Link
                                    :href="route('patients.edit', patient.id)"
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                                >
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="patients.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-muted-foreground">
                            No patients found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="patients.last_page > 1" class="mt-4 flex items-center justify-between">
            <p class="text-sm text-muted-foreground">
                Showing {{ (patients.current_page - 1) * patients.per_page + 1 }} to
                {{ Math.min(patients.current_page * patients.per_page, patients.total) }} of
                {{ patients.total }} results
            </p>
            <div class="flex items-center gap-1">
                <Link
                    v-for="link in patients.links"
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
