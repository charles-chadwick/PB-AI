<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select';

interface Patient {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
}

interface User {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  role: string;
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
  users: User[];
}

const props = defineProps<{
  appointment?: Appointment;
  patients: Patient[];
  users: User[];
}>();

const is_editing = computed(() => !!props.appointment);
const page_title = computed(() => is_editing.value ? 'Edit Appointment' : 'Create Appointment');

// Extract user IDs if editing
const initial_user_ids = props.appointment?.users.map(u => u.id) || [];

const form = useForm({
  patient_id: props.appointment?.patient_id || null,
  title: props.appointment?.title || '',
  description: props.appointment?.description || '',
  appointment_date: props.appointment?.appointment_date || '',
  start_time: props.appointment?.start_time || '',
  end_time: props.appointment?.end_time || '',
  status: props.appointment?.status || 'scheduled',
  user_ids: initial_user_ids,
});

const submit = () => {
  if (is_editing.value) {
    form.put(route('appointments.update', props.appointment!.id));
  } else {
    form.post(route('appointments.store'));
  }
};

const back_url = computed(() => {
  return is_editing.value
    ? route('appointments.show', props.appointment!.id)
    : route('appointments.index');
});

const toggle_user = (user_id: number) => {
  const index = form.user_ids.indexOf(user_id);
  if (index === -1) {
    form.user_ids.push(user_id);
  } else {
    form.user_ids.splice(index, 1);
  }
};
</script>

<template>
  <Head :title="page_title" />

  <AppLayout :title="page_title">
    <!-- Back Link -->
    <div class="mb-6">
      <Link
        :href="back_url"
        class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
      >
        <ArrowLeft class="h-4 w-4" />
        Back
      </Link>
    </div>

    <!-- Form -->
    <div class="mx-auto max-w-3xl">
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Patient -->
        <div class="space-y-2">
          <Label for="patient_id" class="font-bold">
            Patient <span class="text-destructive">*</span>
          </Label>
          <Select v-model="form.patient_id" :disabled="is_editing">
            <SelectTrigger id="patient_id" class="w-full">
              <SelectValue placeholder="Select a patient" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="patient in patients"
                :key="patient.id"
                :value="patient.id.toString()"
              >
                {{ patient.full_name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <p v-if="form.errors.patient_id" class="text-sm text-destructive">
            {{ form.errors.patient_id }}
          </p>
        </div>

        <!-- Title -->
        <div class="space-y-2">
          <Label for="title" class="font-bold">
            Title <span class="text-destructive">*</span>
          </Label>
          <Input
            id="title"
            v-model="form.title"
            placeholder="e.g., Annual Checkup"
          />
          <p v-if="form.errors.title" class="text-sm text-destructive">
            {{ form.errors.title }}
          </p>
        </div>

        <!-- Description -->
        <div class="space-y-2">
          <Label for="description" class="font-bold">
            Description
            <span class="font-normal text-muted-foreground">(optional)</span>
          </Label>
          <Textarea
            id="description"
            v-model="form.description"
            placeholder="Add any additional notes about this appointment"
            rows="3"
          />
          <p v-if="form.errors.description" class="text-sm text-destructive">
            {{ form.errors.description }}
          </p>
        </div>

        <!-- Date & Time -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- Appointment Date -->
          <div class="space-y-2">
            <Label for="appointment_date" class="font-bold">
              Date <span class="text-destructive">*</span>
            </Label>
            <Input
              id="appointment_date"
              type="date"
              v-model="form.appointment_date"
            />
            <p v-if="form.errors.appointment_date" class="text-sm text-destructive">
              {{ form.errors.appointment_date }}
            </p>
          </div>

          <!-- Start Time -->
          <div class="space-y-2">
            <Label for="start_time" class="font-bold">
              Start Time <span class="text-destructive">*</span>
            </Label>
            <Input
              id="start_time"
              type="time"
              v-model="form.start_time"
            />
            <p v-if="form.errors.start_time" class="text-sm text-destructive">
              {{ form.errors.start_time }}
            </p>
          </div>

          <!-- End Time -->
          <div class="space-y-2">
            <Label for="end_time" class="font-bold">
              End Time <span class="text-destructive">*</span>
            </Label>
            <Input
              id="end_time"
              type="time"
              v-model="form.end_time"
            />
            <p v-if="form.errors.end_time" class="text-sm text-destructive">
              {{ form.errors.end_time }}
            </p>
          </div>
        </div>

        <!-- Status -->
        <div class="space-y-2">
          <Label for="status" class="font-bold">
            Status <span class="text-destructive">*</span>
          </Label>
          <Select v-model="form.status">
            <SelectTrigger id="status" class="w-full">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="scheduled">Scheduled</SelectItem>
              <SelectItem value="completed">Completed</SelectItem>
              <SelectItem value="cancelled">Cancelled</SelectItem>
              <SelectItem value="no_show">No Show</SelectItem>
            </SelectContent>
          </Select>
          <p v-if="form.errors.status" class="text-sm text-destructive">
            {{ form.errors.status }}
          </p>
        </div>

        <!-- Assigned Staff -->
        <div class="space-y-2">
          <Label class="font-bold">
            Assigned Staff <span class="text-destructive">*</span>
          </Label>
          <p class="text-sm text-muted-foreground">
            Select one or more staff members for this appointment
          </p>
          <div class="rounded-lg border border-border p-4 space-y-3 max-h-60 overflow-y-auto">
            <div
              v-for="user in users"
              :key="user.id"
              class="flex items-center space-x-3"
            >
              <Checkbox
                :id="`user-${user.id}`"
                :checked="form.user_ids.includes(user.id)"
                @update:checked="toggle_user(user.id)"
              />
              <Label
                :for="`user-${user.id}`"
                class="flex-1 cursor-pointer font-normal"
              >
                <span class="font-medium">{{ user.full_name }}</span>
                <span class="ml-2 text-sm text-muted-foreground">({{ user.role }})</span>
              </Label>
            </div>
          </div>
          <p v-if="form.errors.user_ids" class="text-sm text-destructive">
            {{ form.errors.user_ids }}
          </p>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
          <Link
            :href="back_url"
            class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground"
          >
            Cancel
          </Link>
          <Button type="submit" :disabled="form.processing">
            {{ is_editing ? 'Update Appointment' : 'Create Appointment' }}
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
