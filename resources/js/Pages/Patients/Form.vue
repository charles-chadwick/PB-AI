<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import { DatePicker } from '@/Components/ui/date-picker';

interface Patient {
    id: number;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    date_of_birth: string;
}

const props = defineProps<{
    patient?: Patient;
}>();

const is_editing = computed(() => !!props.patient);
const page_title = computed(() => is_editing.value ? 'Edit Patient' : 'Create Patient');

const form = useForm({
    first_name: props.patient?.first_name || '',
    middle_name: props.patient?.middle_name || '',
    last_name: props.patient?.last_name || '',
    email: props.patient?.email || '',
    date_of_birth: props.patient?.date_of_birth || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    if (is_editing.value) {
        form.put(route('patients.update', props.patient!.id));
    } else {
        form.post(route('patients.store'));
    }
};

const back_url = computed(() => {
    return is_editing.value ? route('patients.show', props.patient!.id) : route('patients.index');
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
        <div class="mx-auto">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name Fields -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- First Name -->
                    <div class="space-y-2">
                        <Label for="first_name" class="font-bold">
                            First Name <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="first_name"
                            v-model="form.first_name"
                        />
                        <p v-if="form.errors.first_name" class="text-sm text-destructive">
                            {{ form.errors.first_name }}
                        </p>
                    </div>

                    <!-- Middle Name -->
                    <div class="space-y-2">
                        <Label for="middle_name" class="font-bold">
                            Middle Name
                            <span class="font-normal text-muted-foreground">(optional)</span>
                        </Label>
                        <Input
                            id="middle_name"
                            v-model="form.middle_name"
                        />
                        <p v-if="form.errors.middle_name" class="text-sm text-destructive">
                            {{ form.errors.middle_name }}
                        </p>
                    </div>

                    <!-- Last Name -->
                    <div class="space-y-2">
                        <Label for="last_name" class="font-bold">
                            Last Name <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="last_name"
                            v-model="form.last_name"
                        />
                        <p v-if="form.errors.last_name" class="text-sm text-destructive">
                            {{ form.errors.last_name }}
                        </p>
                    </div>
                </div>

                <!-- Email & Date of Birth -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email" class="font-bold">
                            Email <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            v-model="form.email"
                        />
                        <p v-if="form.errors.email" class="text-sm text-destructive">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Date of Birth -->
                    <div class="space-y-2">
                        <Label for="date_of_birth" class="font-bold">
                            Date of Birth <span class="text-destructive">*</span>
                        </Label>
                        <DatePicker
                            id="date_of_birth"
                            v-model="form.date_of_birth"
                            placeholder="Select date of birth"
                        />
                        <p v-if="form.errors.date_of_birth" class="text-sm text-destructive">
                            {{ form.errors.date_of_birth }}
                        </p>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Password -->
                    <div class="space-y-2">
                        <Label for="password" class="font-bold">
                            Password
                            <span v-if="!is_editing" class="text-destructive">*</span>
                            <span v-else class="font-normal text-muted-foreground">(leave blank to keep current)</span>
                        </Label>
                        <Input
                            id="password"
                            type="password"
                            v-model="form.password"
                        />
                        <p v-if="form.errors.password" class="text-sm text-destructive">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div class="space-y-2">
                        <Label for="password_confirmation" class="font-bold">Confirm Password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            v-model="form.password_confirmation"
                        />
                    </div>
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
                        {{ is_editing ? 'Update Patient' : 'Create Patient' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
