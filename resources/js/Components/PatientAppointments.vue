<script setup lang="ts">
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Calendar, Clock, Pencil, Trash2, Plus } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Textarea } from '@/Components/ui/textarea'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/Components/ui/select'

interface User {
  id: number
  first_name: string
  last_name: string
  full_name?: string
  role: string
}

interface Appointment {
  id: number
  patient_id: number
  title: string
  type: string
  description: string | null
  appointment_date: string
  start_time: string
  end_time: string
  status: string
  formatted_date: string
  formatted_time: string
  users: User[]
}

interface Props {
  patient_id: number
  appointments: Appointment[]
  users: User[]
}

const props = defineProps<Props>()

const is_open = ref(false)
const editing_appointment = ref<Appointment | null>(null)

const form = useForm({
  patient_id: props.patient_id,
  title: '',
  type: '',
  description: '',
  appointment_date: '',
  start_time: '',
  end_time: '',
  status: 'scheduled',
  user_ids: [] as number[],
})

const open_edit_modal = (appointment: Appointment) => {
  editing_appointment.value = appointment
  form.patient_id = appointment.patient_id
  form.title = appointment.title
  form.type = appointment.type
  form.description = appointment.description || ''
  form.appointment_date = appointment.appointment_date
  form.start_time = appointment.start_time
  form.end_time = appointment.end_time
  form.status = appointment.status
  form.user_ids = appointment.users.map(u => u.id)
  form.clearErrors()
  is_open.value = true
}

const open_create_modal = () => {
  editing_appointment.value = null
  form.reset()
  form.patient_id = props.patient_id
  form.status = 'scheduled'
  form.clearErrors()
  is_open.value = true
}

const close_modal = () => {
  is_open.value = false
  editing_appointment.value = null
  form.reset()
  form.clearErrors()
}

const submit = () => {
  // Ensure user_ids are numbers
  form.user_ids = form.user_ids.map((id: any) => parseInt(id))

  if (editing_appointment.value) {
    form.put(route('appointments.update', editing_appointment.value.id), {
      onSuccess: () => {
        close_modal()
      },
      preserveScroll: true,
    })
  } else {
    form.post(route('appointments.store'), {
      onSuccess: () => {
        close_modal()
      },
      preserveScroll: true,
    })
  }
}

const delete_appointment = (appointment: Appointment) => {
  if (confirm('Are you sure you want to delete this appointment?')) {
    form.delete(route('appointments.destroy', appointment.id), {
      preserveScroll: true,
    })
  }
}

const get_status_variant = (status: string): 'default' | 'secondary' | 'destructive' | 'outline' => {
  switch (status) {
    case 'scheduled':
      return 'default'
    case 'completed':
      return 'secondary'
    case 'cancelled':
      return 'destructive'
    case 'no_show':
      return 'outline'
    default:
      return 'default'
  }
}

const modal_title = computed(() => {
  return editing_appointment.value ? 'Edit Appointment' : 'New Appointment'
})

const get_user_full_name = (user: User) => {
  return user.full_name || `${user.first_name} ${user.last_name}`
}
</script>

<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold">Appointments</h2>
      <Button size="sm" @click="open_create_modal">
        <Plus class="mr-2 h-4 w-4" />
        New Appointment
      </Button>
    </div>

    <!-- Appointments List -->
    <div v-if="appointments.length > 0" class="space-y-3">
      <div
        v-for="appointment in appointments"
        :key="appointment.id"
        class="group rounded-lg border border-border bg-card p-4 transition-colors hover:bg-muted/50"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1 space-y-2">
            <!-- Title and Status -->
            <div class="flex items-center gap-2">
              <h3 class="font-semibold text-foreground">{{ appointment.title }}</h3>
              <Badge :variant="get_status_variant(appointment.status)">
                {{ appointment.status.replace('_', ' ') }}
              </Badge>
            </div>

            <!-- Date and Time -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
              <div class="flex items-center gap-2">
                <Calendar class="h-4 w-4" />
                {{ appointment.formatted_date }}
              </div>
              <div class="flex items-center gap-2">
                <Clock class="h-4 w-4" />
                {{ appointment.formatted_time }}
              </div>
            </div>

            <!-- Assigned Staff -->
            <div v-if="appointment.users.length > 0" class="text-sm">
              <span class="text-muted-foreground">Staff: </span>
              <span class="text-foreground">
                {{ appointment.users.map(u => get_user_full_name(u)).join(', ') }}
              </span>
            </div>

            <!-- Description -->
            <p v-if="appointment.description" class="text-sm text-muted-foreground line-clamp-2">
              {{ appointment.description }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 opacity-0 transition-opacity group-hover:opacity-100">
            <Button variant="ghost" size="sm" @click="open_edit_modal(appointment)">
              <Pencil class="h-4 w-4" />
            </Button>
            <Button variant="ghost" size="sm" @click="delete_appointment(appointment)">
              <Trash2 class="h-4 w-4 text-destructive" />
            </Button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="rounded-lg border-2 border-dashed border-border bg-muted/50 px-6 py-12 text-center"
    >
      <Calendar class="mx-auto h-12 w-12 text-muted-foreground" />
      <h3 class="mt-4 text-lg font-semibold">No appointments yet</h3>
      <p class="mt-2 text-sm text-muted-foreground">
        Create the first appointment for this patient
      </p>
      <Button class="mt-4" @click="open_create_modal">
        <Plus class="mr-2 h-4 w-4" />
        New Appointment
      </Button>
    </div>

    <!-- Edit/Create Modal -->
    <Dialog v-model:open="is_open">
      <DialogContent class="max-w-3xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle>{{ modal_title }}</DialogTitle>
        </DialogHeader>

        <form @submit.prevent="submit" class="space-y-6 pt-4">
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
                  <span class="font-medium">{{ get_user_full_name(user) }}</span>
                  <span class="ml-2 text-sm text-muted-foreground">({{ user.role }})</span>
                </Label>
              </div>
            </div>
            <p v-if="form.errors.user_ids" class="text-sm text-destructive">
              {{ form.errors.user_ids }}
            </p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 border-t pt-4">
            <Button
              type="button"
              variant="outline"
              @click="close_modal"
            >
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              {{ editing_appointment ? 'Update Appointment' : 'Create Appointment' }}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  </div>
</template>
