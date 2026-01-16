<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft, Search, X } from 'lucide-vue-next';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Button } from '@/Components/ui/button';
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
  middle_name?: string;
  last_name: string;
  full_name?: string;
  date_of_birth?: string;
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
  patient?: Patient;
  title: string;
  type: string,
  description: string | null;
  appointment_date: string;
  start_time: string;
  end_time: string;
  status: string;
  users: User[];
}

const props = defineProps<{
  appointment?: Appointment;
  users: User[];
}>();

const is_editing = computed(() => !!props.appointment);
const page_title = computed(() => is_editing.value ? 'Edit Appointment' : 'Create Appointment');

// Extract user IDs if editing
const initial_user_ids = props.appointment?.users.map(u => u.id) || [];

// Patient search
const patient_search_query = ref('');
const patient_search_results = ref<Patient[]>([]);
const show_patient_results = ref(false);
const selected_patient = ref<Patient | null>(props.appointment?.patient || null);
const search_timeout = ref<number | null>(null);

const form = useForm({
  patient_id: props.appointment?.patient_id || null,
  title: props.appointment?.title || '',
  type: props.appointment?.type || '',
  description: props.appointment?.description || '',
  appointment_date: props.appointment?.appointment_date || '',
  start_time: props.appointment?.start_time || '',
  end_time: props.appointment?.end_time || '',
  status: props.appointment?.status || 'scheduled',
  user_ids: initial_user_ids,
});

// Set initial search query if editing
if (props.appointment?.patient) {
  const patient = props.appointment.patient;
  const name_parts = [patient.first_name, patient.middle_name, patient.last_name].filter(Boolean);
  patient_search_query.value = name_parts.join(' ');
}

// Watch search query and perform debounced search
watch(patient_search_query, (new_query) => {
  if (search_timeout.value) {
    clearTimeout(search_timeout.value);
  }

  if (new_query.length < 2) {
    patient_search_results.value = [];
    show_patient_results.value = false;
    return;
  }

  search_timeout.value = window.setTimeout(async () => {
    try {
      const response = await axios.get(route('patients.search'), {
        params: { q: new_query }
      });
      patient_search_results.value = response.data;
      show_patient_results.value = response.data.length > 0;
    } catch (error) {
      console.error('Patient search error:', error);
      patient_search_results.value = [];
      show_patient_results.value = false;
    }
  }, 300);
});

const select_patient = (patient: Patient) => {
  selected_patient.value = patient;
  form.patient_id = patient.id;
  const name_parts = [patient.first_name, patient.middle_name, patient.last_name].filter(Boolean);
  patient_search_query.value = name_parts.join(' ');
  show_patient_results.value = false;
};

const clear_patient_selection = () => {
  selected_patient.value = null;
  form.patient_id = null;
  patient_search_query.value = '';
  patient_search_results.value = [];
  show_patient_results.value = false;
};

const format_patient_display = (patient: Patient) => {
  const name_parts = [patient.first_name, patient.middle_name, patient.last_name].filter(Boolean);
  const name = name_parts.join(' ');
  const dob = patient.date_of_birth ? ` (DOB: ${patient.date_of_birth})` : '';
  return `${name}${dob}`;
};

const submit = () => {
  // Ensure patient_id is a number
  if (form.patient_id) {
    form.patient_id = parseInt(form.patient_id as any);
  }

  // Ensure user_ids are numbers (v-model on checkboxes may return strings)
  form.user_ids = form.user_ids.map((id: any) => parseInt(id));

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
        <!-- Patient Search -->
        <div class="space-y-2">
          <Label for="patient_search" class="font-bold">
            Patient <span class="text-destructive">*</span>
          </Label>
          <p class="text-sm text-muted-foreground">
            Search by name, date of birth (YYYY-MM-DD), or patient ID
          </p>
          <div class="relative">
            <div class="relative">
              <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                id="patient_search"
                v-model="patient_search_query"
                :disabled="is_editing"
                placeholder="Type to search patients..."
                class="pl-9 pr-9"
                @focus="() => { if (patient_search_results.length > 0) show_patient_results = true; }"
              />
              <button
                v-if="selected_patient && !is_editing"
                type="button"
                @click="clear_patient_selection"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
              >
                <X class="h-4 w-4" />
              </button>
            </div>

            <!-- Search Results Dropdown -->
            <div
              v-if="show_patient_results && patient_search_results.length > 0"
              class="absolute z-50 mt-1 w-full rounded-md border border-border bg-popover shadow-lg"
            >
              <ul class="max-h-60 overflow-y-auto py-1">
                <li
                  v-for="patient in patient_search_results"
                  :key="patient.id"
                  @click="select_patient(patient)"
                  class="cursor-pointer px-3 py-2 text-sm hover:bg-accent hover:text-accent-foreground"
                >
                  <div class="font-medium">{{ format_patient_display(patient) }}</div>
                  <div class="text-xs text-muted-foreground">ID: {{ patient.id }}</div>
                </li>
              </ul>
            </div>
          </div>
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

        <!-- Status & Type -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
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

          <!-- Type -->
          <div class="space-y-2">
            <Label for="type" class="font-bold">
              Type <span class="text-destructive">*</span>
            </Label>
            <Input
              id="type"
              type="text"
              v-model="form.type"
            />
            <p v-if="form.errors.type" class="text-sm text-destructive">
              {{ form.errors.type }}
            </p>
          </div>
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
              <input
                type="checkbox"
                :id="`user-${user.id}`"
                :value="user.id"
                v-model="form.user_ids"
                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
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
