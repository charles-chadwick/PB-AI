<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ArrowLeft } from 'lucide-vue-next';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Button } from '@/Components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import AvatarUpload from '@/Components/AvatarUpload.vue';

interface User {
    id: number;
    role: string;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    email: string;
    avatar_url: string | null;
    initials: string;
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
        form.put(route('users.update', props.user!.id));
    } else {
        form.post(route('users.store'));
    }
};

const back_url = computed(() => {
    return is_editing.value ? route('users.show', props.user!.id) : route('users.index');
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
        <div :class="is_editing ? 'mx-auto max-w-5xl' : 'mx-auto max-w-2xl'">
            <div :class="is_editing ? 'grid grid-cols-1 gap-6 lg:grid-cols-3' : ''">
                <form @submit.prevent="submit" :class="is_editing ? 'space-y-6 lg:col-span-2' : 'space-y-6'">
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

                <!-- Email & Role -->
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

                    <!-- Role -->
                    <div class="space-y-2">
                        <Label for="role" class="font-bold">
                            Role <span class="text-destructive">*</span>
                        </Label>
                        <Select v-model="form.role">
                            <SelectTrigger id="role" class="w-full">
                                <SelectValue placeholder="Select a role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="role in roles" :key="role" :value="role">
                                    {{ role }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.role" class="text-sm text-destructive">
                            {{ form.errors.role }}
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
                        {{ is_editing ? 'Update User' : 'Create User' }}
                    </Button>
                </div>
                </form>

                <!-- Avatar Upload (Only in Edit Mode) -->
                <div v-if="is_editing" class="lg:col-span-1">
                    <AvatarUpload
                        :avatar_url="user!.avatar_url"
                        :initials="user!.initials"
                        entity_type="users"
                        :entity_id="user!.id"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
