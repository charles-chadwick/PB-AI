<script lang="ts" setup>
import type { ToasterProps } from "vue-sonner"
import { CircleCheckIcon, InfoIcon, Loader2Icon, OctagonXIcon, TriangleAlertIcon, XIcon } from "lucide-vue-next"
import { Toaster as Sonner } from "vue-sonner"
import { cn } from "@/lib/utils"

const props = defineProps<ToasterProps>()
</script>

<template>
  <Sonner
    :class="cn('toaster group', props.class)"
    :style="{
      '--normal-bg': 'var(--popover)',
      '--normal-text': 'var(--popover-foreground)',
      '--normal-border': 'var(--border)',
      '--border-radius': 'var(--radius)',
    }"
    v-bind="props"
  >
    <template #success-icon>
      <CircleCheckIcon class="size-4 text-lime-600" />
    </template>
    <template #info-icon>
      <InfoIcon class="size-4 text-sky-500" />
    </template>
    <template #warning-icon>
      <TriangleAlertIcon class="size-4 text-amber-600" />
    </template>
    <template #error-icon>
      <OctagonXIcon class="size-4 text-red-600" />
    </template>
    <template #loading-icon>
      <div>
        <Loader2Icon class="size-4 animate-spin" />
      </div>
    </template>
    <template #close-icon>
      <XIcon class="size-4" />
    </template>
  </Sonner>
</template>

<style>
[data-sonner-toast][data-type="success"] {
  --normal-bg: #f7fee7;
  --normal-border: #d9f99d;
  --normal-text: #365314;
}

[data-sonner-toast][data-type="error"] {
  --normal-bg: #fef2f2;
  --normal-border: #fecaca;
  --normal-text: #7f1d1d;
}

[data-sonner-toast][data-type="warning"] {
  --normal-bg: #fffbeb;
  --normal-border: #fde68a;
  --normal-text: #78350f;
}

[data-sonner-toast][data-type="info"] {
  --normal-bg: #f0f9ff;
  --normal-border: #bae6fd;
  --normal-text: #0c4a6e;
}
</style>
