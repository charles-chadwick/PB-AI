<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Trash2, Mail, Cake, Calendar } from 'lucide-vue-next';
import AvatarWithModal from '@/Components/AvatarWithModal.vue';
import PatientAppointments from '@/Components/PatientAppointments.vue';
import { Button } from '@/Components/ui/button';

interface User {
    id: number;
    first_name: string;
    last_name: string;
    role: string;
}

interface Appointment {
    id: number;
    patient_id: number;
    title: string;
    type: string;
    description: string | null;
    appointment_date: string;
    start_time: string;
    end_time: string;
    status: string;
    formatted_date: string;
    formatted_time: string;
    users: User[];
}

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
}

const props = defineProps<{
    patient: Patient;
    appointments: Appointment[];
    users: User[];
}>();

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
        <div class="mb-6">
            <Link
                :href="route('patients.index')"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back to Patients
            </Link>
        </div>

        <!-- Profile Card -->
        <div class="overflow-hidden rounded-xl border border-border bg-card">
            <!-- Header with gradient background -->
            <div class="relative bg-linear-to-r from-primary/10 via-primary/5 to-transparent px-6 pb-0 pt-6">
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-end sm:gap-6">
                    <!-- Avatar -->
                    <AvatarWithModal
                        :avatar_url="patient.avatar_url"
                        :initials="patient.initials"
                        :full_name="patient.full_name"
                        size="lg"
                    />

                    <!-- Name -->
                    <div class="flex flex-1 flex-col items-center pb-4 text-center sm:items-start sm:text-left">
                        <h1 class="text-2xl font-semibold text-foreground">{{ patient.full_name }}</h1>
                        <span class="mt-1 text-sm text-muted-foreground">Patient</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pb-4">
                        <Button as-child variant="outline" size="sm">
                            <Link :href="route('patients.edit', patient.id)">
                                <Pencil class="mr-2 h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="destructive" size="sm" @click="deletePatient">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid gap-4 p-6 sm:grid-cols-3">
                <!-- Email -->
                <div class="flex items-center gap-3 rounded-lg bg-muted/50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <Mail class="h-5 w-5 text-primary" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">Email</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ patient.email }}</p>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="flex items-center gap-3 rounded-lg bg-muted/50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <Cake class="h-5 w-5 text-primary" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">Date of Birth</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ patient.date_of_birth }}</p>
                    </div>
                </div>

                <!-- Created -->
                <div class="flex items-center gap-3 rounded-lg bg-muted/50 p-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10">
                        <Calendar class="h-5 w-5 text-primary" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-muted-foreground">Patient Since</p>
                        <p class="truncate text-sm font-medium text-foreground">{{ patient.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="mt-6 rounded-xl border border-border bg-card p-6">
            <PatientAppointments
                :patient_id="patient.id"
                :appointments="appointments"
                :users="users"
            />
        </div>
    </AppLayout>
</template>
