<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Pencil, Trash2, Calendar, Clock, User as UserIcon } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';

interface User {
  id: number;
  role: string;
  first_name: string;
  last_name: string;
  full_name: string;
  avatar_url: string | null;
  initials: string;
}

interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  avatar_url: string | null;
  initials: string;
  email: string;
}

interface Appointment {
  id: number;
  patient_id: number;
  title: string;
  description: string | null;
  appointment_date: string;
  start_time: string;
  end_time: string;
  status: string;
  formatted_date: string;
  formatted_time: string;
  formatted_datetime: string;
  created_at: string;
  patient: Patient;
  users: User[];
  created_by?: User;
}

const props = defineProps<{
  appointment: Appointment;
}>();

const delete_appointment = () => {
  if (confirm('Are you sure you want to delete this appointment?')) {
    router.delete(route('appointments.destroy', props.appointment.id));
  }
};

const get_status_variant = (status: string): 'default' | 'secondary' | 'destructive' | 'outline' => {
  switch (status) {
    case 'scheduled':
      return 'default';
    case 'completed':
      return 'secondary';
    case 'cancelled':
      return 'destructive';
    case 'no_show':
      return 'outline';
    default:
      return 'default';
  }
};
</script>

<template>
  <Head :title="appointment.title" />

  <AppLayout :title="appointment.title">
    <!-- Back Link -->
    <div class="mb-6">
      <Link
        :href="route('appointments.index')"
        class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft class="h-4 w-4" />
        Back to Appointments
      </Link>
    </div>

    <!-- Appointment Card -->
    <div class="overflow-hidden rounded-xl border border-border bg-card">
      <!-- Header -->
      <div class="border-b border-border bg-muted/50 px-6 py-4">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h2 class="text-2xl font-semibold text-foreground">{{ appointment.title }}</h2>
              <Badge :variant="get_status_variant(appointment.status)">
                {{ appointment.status.replace('_', ' ') }}
              </Badge>
            </div>
            <div class="flex items-center gap-4 text-sm text-muted-foreground">
              <div class="flex items-center gap-2">
                <Calendar class="h-4 w-4" />
                {{ appointment.formatted_date }}
              </div>
              <div class="flex items-center gap-2">
                <Clock class="h-4 w-4" />
                {{ appointment.formatted_time }}
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <Button as-child variant="outline" size="sm">
              <Link :href="route('appointments.edit', appointment.id)">
                <Pencil class="mr-2 h-4 w-4" />
                Edit
              </Link>
            </Button>
            <Button variant="destructive" size="sm" @click="delete_appointment">
              <Trash2 class="mr-2 h-4 w-4" />
              Delete
            </Button>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="p-6 space-y-6">
        <!-- Patient -->
        <div>
          <h3 class="text-sm font-semibold text-muted-foreground mb-3">Patient</h3>
          <Link
            :href="route('patients.show', appointment.patient.id)"
            class="flex items-center gap-3 rounded-lg bg-muted/50 p-4 transition-colors hover:bg-muted"
          >
            <Avatar class="h-12 w-12">
              <AvatarImage v-if="appointment.patient.avatar_url" :src="appointment.patient.avatar_url" />
              <AvatarFallback class="bg-primary/10 text-primary">
                {{ appointment.patient.initials }}
              </AvatarFallback>
            </Avatar>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-foreground">{{ appointment.patient.full_name }}</p>
              <p class="text-sm text-muted-foreground truncate">{{ appointment.patient.email }}</p>
            </div>
          </Link>
        </div>

        <!-- Description -->
        <div v-if="appointment.description">
          <h3 class="text-sm font-semibold text-muted-foreground mb-2">Description</h3>
          <p class="text-foreground whitespace-pre-wrap">{{ appointment.description }}</p>
        </div>

        <!-- Assigned Staff -->
        <div>
          <h3 class="text-sm font-semibold text-muted-foreground mb-3">Assigned Staff</h3>
          <div class="space-y-2">
            <Link
              v-for="user in appointment.users"
              :key="user.id"
              :href="route('users.show', user.id)"
              class="flex items-center gap-3 rounded-lg bg-muted/50 p-3 transition-colors hover:bg-muted"
            >
              <Avatar class="h-10 w-10">
                <AvatarImage v-if="user.avatar_url" :src="user.avatar_url" />
                <AvatarFallback class="bg-primary/10 text-primary">
                  {{ user.initials }}
                </AvatarFallback>
              </Avatar>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-foreground">{{ user.full_name }}</p>
                <p class="text-sm text-muted-foreground">{{ user.role }}</p>
              </div>
            </Link>
          </div>
        </div>

        <!-- Metadata -->
        <div class="border-t border-border pt-4">
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <UserIcon class="h-4 w-4" />
            <span>
              Created by
              <span v-if="appointment.created_by" class="font-medium text-foreground">
                {{ appointment.created_by.full_name }}
              </span>
              <span v-else class="font-medium text-foreground">System</span>
              on {{ appointment.created_at }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
