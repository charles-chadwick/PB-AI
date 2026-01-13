<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';

interface User {
    id: number;
    role: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
}

const props = defineProps<{
    user?: User;
    roles: string[];
}>();

const is_editing = computed(() => !!props.user);
const page_title = computed(() => is_editing.value ? 'Edit User' : 'Create User');

const form = useForm({
    role: props.user?.role || '',
    first_name: props.user?.first_name || '',
    middle_name: props.user?.middle_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    if (is_editing.value) {
        form.put(`/users/${props.user!.id}`);
    } else {
        form.post('/users');
    }
};
</script>

<template>
    <Head :title="page_title" />

    <AppLayout :title="page_title">
        <!-- Back Link -->
        <div class="mb-6">
            <Link
                :href="is_editing ? `/users/${user!.id}` : '/users'"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
            >
                <ArrowLeft class="h-4 w-4" />
                Back
            </Link>
        </div>

        <!-- Form -->
        <div class="mx-auto max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name Fields -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-foreground">
                            First Name
                        </label>
                        <input
                            id="first_name"
                            v-model="form.first_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                        <p v-if="form.errors.first_name" class="mt-1 text-sm text-destructive">
                            {{ form.errors.first_name }}
                        </p>
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-foreground">
                            Middle Name
                            <span class="text-muted-foreground">(optional)</span>
                        </label>
                        <input
                            id="middle_name"
                            v-model="form.middle_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                        <p v-if="form.errors.middle_name" class="mt-1 text-sm text-destructive">
                            {{ form.errors.middle_name }}
                        </p>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-foreground">
                            Last Name
                        </label>
                        <input
                            id="last_name"
                            v-model="form.last_name"
                            type="text"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                        <p v-if="form.errors.last_name" class="mt-1 text-sm text-destructive">
                            {{ form.errors.last_name }}
                        </p>
                    </div>
                </div>

                <!-- Email & Role -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-foreground">
                            Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-destructive">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-foreground">
                            Role
                        </label>
                        <select
                            id="role"
                            v-model="form.role"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        >
                            <option value="">Select a role</option>
                            <option v-for="role in roles" :key="role" :value="role">
                                {{ role }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1 text-sm text-destructive">
                            {{ form.errors.role }}
                        </p>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-foreground">
                            Password
                            <span v-if="is_editing" class="text-muted-foreground">(leave blank to keep current)</span>
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-destructive">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-foreground">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                        />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="is_editing ? `/users/${user!.id}` : '/users'"
                        class="rounded-md px-4 py-2 text-sm font-medium text-muted-foreground hover:text-foreground"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:pointer-events-none disabled:opacity-50"
                    >
                        {{ is_editing ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
