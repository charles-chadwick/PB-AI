<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Button } from '@/Components/ui/button';
import { Upload, X, User } from 'lucide-vue-next';

const props = defineProps<{
    avatar_url: string | null;
    entity_type: 'users' | 'patients';
    entity_id: number;
    initials?: string;
}>();

const file_input = ref<HTMLInputElement | null>(null);
const is_uploading = ref(false);
const is_removing = ref(false);

const handle_file_select = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        upload_avatar(file);
    }
};

const upload_avatar = (file: File) => {
    is_uploading.value = true;

    router.post(
        route(`${props.entity_type}.avatar.upload`, props.entity_id),
        { avatar: file },
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                is_uploading.value = false;
                if (file_input.value) {
                    file_input.value.value = '';
                }
            },
            onError: () => {
                is_uploading.value = false;
            },
        }
    );
};

const remove_avatar = () => {
    if (confirm('Are you sure you want to remove this avatar?')) {
        is_removing.value = true;

        router.delete(
            route(`${props.entity_type}.avatar.destroy`, props.entity_id),
            {
                preserveScroll: true,
                onFinish: () => {
                    is_removing.value = false;
                },
            }
        );
    }
};

const trigger_file_input = () => {
    file_input.value?.click();
};
</script>

<template>
    <div class="rounded-lg border border-border bg-card p-6">
        <h3 class="mb-4 text-sm font-semibold">Avatar</h3>

        <div class="flex flex-col items-center gap-4">
            <!-- Avatar Preview -->
            <Avatar class="h-32 w-32 text-3xl">
                <AvatarImage v-if="avatar_url" :src="avatar_url" />
                <AvatarFallback class="bg-muted">
                    <User v-if="!initials" class="h-16 w-16 text-muted-foreground" />
                    <span v-else class="text-muted-foreground">{{ initials }}</span>
                </AvatarFallback>
            </Avatar>

            <!-- Upload/Remove Actions -->
            <div class="flex w-full flex-col gap-2">
                <input
                    ref="file_input"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handle_file_select"
                />

                <Button
                    v-if="!avatar_url"
                    type="button"
                    variant="outline"
                    class="w-full"
                    :disabled="is_uploading"
                    @click="trigger_file_input"
                >
                    <Upload class="mr-2 h-4 w-4" />
                    {{ is_uploading ? 'Uploading...' : 'Upload Avatar' }}
                </Button>

                <template v-else>
                    <Button
                        type="button"
                        variant="outline"
                        class="w-full"
                        :disabled="is_uploading"
                        @click="trigger_file_input"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        {{ is_uploading ? 'Uploading...' : 'Change Avatar' }}
                    </Button>

                    <Button
                        type="button"
                        variant="destructive"
                        class="w-full"
                        :disabled="is_removing"
                        @click="remove_avatar"
                    >
                        <X class="mr-2 h-4 w-4" />
                        {{ is_removing ? 'Removing...' : 'Remove Avatar' }}
                    </Button>
                </template>
            </div>

            <p class="text-center text-xs text-muted-foreground">
                JPG, PNG or GIF. Max size 2MB.
            </p>
        </div>
    </div>
</template>
