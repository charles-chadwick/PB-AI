<script setup lang="ts">
import { ref } from 'vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import {
  Dialog,
  DialogContent,
  DialogTrigger,
} from '@/Components/ui/dialog'

interface Props {
  avatar_url: string | null
  initials: string
  full_name: string
  size?: 'sm' | 'md' | 'lg' | 'xl'
}

const props = withDefaults(defineProps<Props>(), {
  size: 'lg'
})

const is_open = ref(false)

const size_classes = {
  sm: 'h-12 w-12 text-base',
  md: 'h-16 w-16 text-lg',
  lg: 'h-24 w-24 text-2xl sm:h-28 sm:w-28',
  xl: 'h-32 w-32 text-3xl sm:h-36 sm:w-36'
}
</script>

<template>
  <Dialog v-model:open="is_open">
    <DialogTrigger as-child>
      <button
        type="button"
        class="group relative cursor-pointer rounded-full transition-all hover:ring-4 hover:ring-primary/20"
        :class="[
          size_classes[size],
          'border-4 border-card shadow-lg'
        ]"
      >
        <Avatar :class="['h-full w-full', size_classes[size]]">
          <AvatarImage v-if="avatar_url" :src="avatar_url" :alt="full_name" />
          <AvatarFallback class="bg-primary/10 text-primary">{{ initials }}</AvatarFallback>
        </Avatar>

        <!-- Hover overlay -->
        <div
          v-if="avatar_url"
          class="absolute inset-0 flex items-center justify-center rounded-full bg-black/50 opacity-0 transition-opacity group-hover:opacity-100"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            class="h-8 w-8 text-white"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
          </svg>
        </div>
      </button>
    </DialogTrigger>

    <DialogContent class="max-w-3xl">
      <div class="flex flex-col items-center gap-4 p-4">
        <h2 class="text-xl font-semibold">{{ full_name }}</h2>
        <div class="relative max-h-[70vh] w-full overflow-hidden rounded-lg">
          <img
            v-if="avatar_url"
            :src="avatar_url"
            :alt="full_name"
            class="h-full w-full object-contain"
          />
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>
