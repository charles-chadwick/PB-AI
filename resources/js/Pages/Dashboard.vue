<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Users, UserPlus, Calendar, ArrowRight } from 'lucide-vue-next';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/Components/ui/card';

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
  created_by?: User;
}

const props = defineProps<{
  total_patients: number;
  total_users: number;
  new_patients_this_month: number;
  recent_patients: Patient[];
}>();
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout title="Dashboard">
    <!-- Stats Grid -->
    <div class="grid gap-4 md:grid-cols-3 mb-6">
      <!-- Total Patients -->
      <Card>
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-sm font-medium text-muted-foreground">
            Total Patients
          </CardTitle>
          <Users class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ total_patients.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">
            Registered in the system
          </p>
        </CardContent>
      </Card>

      <!-- Total Users -->
      <Card>
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-sm font-medium text-muted-foreground">
            Total Users
          </CardTitle>
          <Users class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ total_users.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">
            Staff members
          </p>
        </CardContent>
      </Card>

      <!-- New This Month -->
      <Card>
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-sm font-medium text-muted-foreground">
            New This Month
          </CardTitle>
          <UserPlus class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ new_patients_this_month.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">
            Patients registered this month
          </p>
        </CardContent>
      </Card>
    </div>

    <!-- Recent Patients -->
    <Card>
      <CardHeader>
        <div class="flex items-center justify-between">
          <div>
            <CardTitle>Recent Patients</CardTitle>
            <CardDescription>Latest patient registrations</CardDescription>
          </div>
          <Button as-child variant="outline" size="sm">
            <Link :href="route('patients.index')">
              View All
              <ArrowRight class="ml-2 h-4 w-4" />
            </Link>
          </Button>
        </div>
      </CardHeader>
      <CardContent>
        <div v-if="recent_patients.length === 0" class="py-8 text-center text-muted-foreground">
          No patients registered yet.
        </div>
        <div v-else class="space-y-4">
          <Link
            v-for="patient in recent_patients"
            :key="patient.id"
            :href="route('patients.show', patient.id)"
            class="flex items-center gap-4 rounded-lg p-3 transition-colors hover:bg-muted/50"
          >
            <!-- Avatar -->
            <Avatar class="h-12 w-12">
              <AvatarImage v-if="patient.avatar_url" :src="patient.avatar_url" />
              <AvatarFallback class="bg-primary/10 text-primary">
                {{ patient.initials }}
              </AvatarFallback>
            </Avatar>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <p class="font-medium text-foreground">{{ patient.full_name }}</p>
              <p class="text-sm text-muted-foreground truncate">{{ patient.email }}</p>
            </div>

            <!-- Date -->
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
              <Calendar class="h-4 w-4" />
              {{ patient.created_at }}
            </div>
          </Link>
        </div>
      </CardContent>
    </Card>
  </AppLayout>
</template>
