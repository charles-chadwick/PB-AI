<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Trash2 } from 'lucide-vue-next';
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
    updated_at: string;
    created_by?: {
        id: number;
        first_name: string;
        last_name: string;
    };
}

const props = defineProps<{
    patient: Patient;
}>();

const created_by_name = computed(() => {
    if (!props.patient.created_by) return 'System';
    return `${props.patient.created_by.first_name} ${props.patient.created_by.last_name}`;
});

const deletePatient = () => {
    if (confirm('Are you sure you want to delete this patient?')) {
        router.delete(route('patients.destroy', props.patient.id));
    }
};
</script>

<template>
    <Head :title="patient.full_name" />

    <AppLayout :title="patient.full_name">
        <!-- Back Link -->
        <div class="mb-6 flex items-center justify-between">
            <Link
                :href="route('patients.index')"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Patients
            </Link>

            <div class="flex items-center gap-2">
                <Link
                    :href="route('patients.edit', patient.id)"
                    class="inline-flex items-center gap-2 rounded-md bg-secondary px-3 py-2 text-sm font-medium text-secondary-foreground transition-colors hover:bg-secondary/80"
                >
                    <Pencil class="h-4 w-4" />
                    Edit
                </Link>
                <button
                    @click="deletePatient"
                    class="inline-flex items-center gap-2 rounded-md bg-destructive px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-destructive/90"
                >
                    <Trash2 class="h-4 w-4" />
                    Delete
                </button>
            </div>
        </div>

        <!-- Patient Details -->
        <div class="rounded-lg border border-border bg-card">
            <div class="border-b border-border px-6 py-4">
                <div class="flex items-center gap-4">
                    <Avatar class="h-16 w-16 text-lg">
                        <AvatarImage v-if="patient.avatar_url" :src="patient.avatar_url" />
                        <AvatarFallback>{{ patient.initials }}</AvatarFallback>
                    </Avatar>
                    <div>
                        <h2 class="text-lg font-medium text-foreground">{{ patient.full_name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ patient.email }}</p>
                    </div>
                </div>
            </div>
            <dl class="divide-y divide-border">
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">First Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.first_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Middle Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">
                        {{ patient.middle_name || '-' }}
                    </dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Last Name</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.last_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Email</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.email }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Date of Birth</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.date_of_birth }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Created By</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ created_by_name }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Created At</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.created_at }}</dd>
                </div>
                <div class="grid grid-cols-3 gap-4 px-6 py-4">
                    <dt class="text-sm font-medium text-muted-foreground">Updated At</dt>
                    <dd class="col-span-2 text-sm text-foreground">{{ patient.updated_at }}</dd>
                </div>
            </dl>
        </div>
    </AppLayout>
</template>
