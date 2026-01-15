<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Plus, Eye, Pencil, Trash2, Calendar, Clock, User } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/Components/ui/table';

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
  patient: Patient;
  users: User[];
}

interface PaginatedAppointments {
  data: Appointment[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const props = defineProps<{
  appointments: PaginatedAppointments;
}>();

const delete_appointment = (appointment: Appointment) => {
  if (confirm('Are you sure you want to delete this appointment?')) {
    router.delete(route('appointments.destroy', appointment.id));
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
  <Head title="Appointments" />

  <AppLayout title="Appointments">
    <!-- Header Actions -->
    <div class="mb-6 flex items-center justify-between">
      <p class="text-sm text-muted-foreground">
        Manage patient appointments
      </p>
      <Button as-child>
        <Link :href="route('appointments.create')">
          <Plus class="mr-2 h-4 w-4" />
          New Appointment
        </Link>
      </Button>
    </div>

    <!-- Appointments Table -->
    <div class="rounded-md border border-border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Patient</TableHead>
            <TableHead>Title</TableHead>
            <TableHead>Date & Time</TableHead>
            <TableHead>Assigned Staff</TableHead>
            <TableHead>Status</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-if="appointments.data.length === 0">
            <TableCell colspan="6" class="h-24 text-center">
              No appointments found.
            </TableCell>
          </TableRow>
          <TableRow v-for="appointment in appointments.data" :key="appointment.id">
            <!-- Patient -->
            <TableCell>
              <Link
                :href="route('patients.show', appointment.patient.id)"
                class="flex items-center gap-3 hover:underline"
              >
                <Avatar class="h-8 w-8">
                  <AvatarImage v-if="appointment.patient.avatar_url" :src="appointment.patient.avatar_url" />
                  <AvatarFallback class="bg-primary/10 text-primary text-xs">
                    {{ appointment.patient.initials }}
                  </AvatarFallback>
                </Avatar>
                <span class="font-medium">{{ appointment.patient.full_name }}</span>
              </Link>
            </TableCell>

            <!-- Title -->
            <TableCell>
              <div class="font-medium">{{ appointment.title }}</div>
              <div v-if="appointment.description" class="text-sm text-muted-foreground line-clamp-1">
                {{ appointment.description }}
              </div>
            </TableCell>

            <!-- Date & Time -->
            <TableCell>
              <div class="flex items-center gap-2 text-sm">
                <Calendar class="h-4 w-4 text-muted-foreground" />
                {{ appointment.formatted_date }}
              </div>
              <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <Clock class="h-4 w-4" />
                {{ appointment.formatted_time }}
              </div>
            </TableCell>

            <!-- Assigned Staff -->
            <TableCell>
              <div class="flex items-center gap-1">
                <Avatar
                  v-for="user in appointment.users.slice(0, 3)"
                  :key="user.id"
                  class="h-7 w-7 border-2 border-card -ml-2 first:ml-0"
                  :title="user.full_name"
                >
                  <AvatarImage v-if="user.avatar_url" :src="user.avatar_url" />
                  <AvatarFallback class="bg-primary/10 text-primary text-xs">
                    {{ user.initials }}
                  </AvatarFallback>
                </Avatar>
                <span v-if="appointment.users.length > 3" class="ml-1 text-xs text-muted-foreground">
                  +{{ appointment.users.length - 3 }}
                </span>
              </div>
            </TableCell>

            <!-- Status -->
            <TableCell>
              <Badge :variant="get_status_variant(appointment.status)">
                {{ appointment.status.replace('_', ' ') }}
              </Badge>
            </TableCell>

            <!-- Actions -->
            <TableCell class="text-right">
              <div class="flex items-center justify-end gap-2">
                <Button as-child variant="ghost" size="sm">
                  <Link :href="route('appointments.show', appointment.id)">
                    <Eye class="h-4 w-4" />
                  </Link>
                </Button>
                <Button as-child variant="ghost" size="sm">
                  <Link :href="route('appointments.edit', appointment.id)">
                    <Pencil class="h-4 w-4" />
                  </Link>
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  @click="delete_appointment(appointment)"
                >
                  <Trash2 class="h-4 w-4" />
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <!-- Pagination -->
    <div v-if="appointments.last_page > 1" class="mt-4 flex items-center justify-between">
      <p class="text-sm text-muted-foreground">
        Showing {{ (appointments.current_page - 1) * appointments.per_page + 1 }} to
        {{ Math.min(appointments.current_page * appointments.per_page, appointments.total) }} of
        {{ appointments.total }} results
      </p>
      <div class="flex gap-2">
        <Button
          variant="outline"
          size="sm"
          :disabled="appointments.current_page === 1"
          @click="router.visit(route('appointments.index', { page: appointments.current_page - 1 }))"
        >
          Previous
        </Button>
        <Button
          variant="outline"
          size="sm"
          :disabled="appointments.current_page === appointments.last_page"
          @click="router.visit(route('appointments.index', { page: appointments.current_page + 1 }))"
        >
          Next
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
