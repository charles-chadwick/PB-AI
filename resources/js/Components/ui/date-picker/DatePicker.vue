<script setup lang="ts">
import type { DateValue } from '@internationalized/date'
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
import { CalendarIcon } from 'lucide-vue-next'
import { computed, ref, type Ref } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Calendar } from '@/components/ui/calendar'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'

const model = defineModel<string>()

const props = defineProps<{
    id?: string
    placeholder?: string
    disabled?: boolean
}>()

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
})

const date_value = computed({
    get: (): DateValue | undefined => {
        if (!model.value) return undefined
        const [year, month, day] = model.value.split('-').map(Number)
        return new CalendarDate(year, month, day)
    },
    set: (value: DateValue | undefined) => {
        if (value) {
            const year = value.year
            const month = String(value.month).padStart(2, '0')
            const day = String(value.day).padStart(2, '0')
            model.value = `${year}-${month}-${day}`
        } else {
            model.value = ''
        }
    },
})

const formatted_date = computed(() => {
    if (!date_value.value) return ''
    return df.format(date_value.value.toDate(getLocalTimeZone()))
})
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button
                :id="id"
                variant="outline"
                :disabled="disabled"
                :class="cn('w-full justify-start text-left font-normal', !model && 'text-muted-foreground')"
            >
                <CalendarIcon class="mr-2 h-4 w-4" />
                {{ formatted_date || placeholder || 'Pick a date' }}
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0" align="start">
            <Calendar
                v-model="date_value"
                layout="month-and-year"
                initial-focus
            />
        </PopoverContent>
    </Popover>
</template>
