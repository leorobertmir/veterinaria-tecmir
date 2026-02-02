<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format, startOfMonth, endOfMonth, eachDayOfInterval, isSameMonth, isSameDay, addMonths, subMonths, startOfWeek, endOfWeek } from 'date-fns'
import { es } from 'date-fns/locale'

interface CitaCalendar {
  id: string
  title: string
  start: string
  end: string
  estado: string
  tipoConsulta: string
  mascota: { id: string; nombre: string }
  veterinario: { id: string; name: string } | null
}

interface Props {
  citas: CitaCalendar[]
  veterinarios: { id: string; name: string }[]
}

const props = defineProps<Props>()

const currentDate = ref(new Date())

const calendarDays = computed(() => {
  const start = startOfWeek(startOfMonth(currentDate.value), { locale: es })
  const end = endOfWeek(endOfMonth(currentDate.value), { locale: es })
  return eachDayOfInterval({ start, end })
})

const getCitasForDay = (day: Date) => {
  return props.citas.filter(cita => isSameDay(new Date(cita.start), day))
}

const prevMonth = () => {
  currentDate.value = subMonths(currentDate.value, 1)
}

const nextMonth = () => {
  currentDate.value = addMonths(currentDate.value, 1)
}

const goToToday = () => {
  currentDate.value = new Date()
}

const getEstadoColor = (estado: string) => {
  const colors: Record<string, string> = {
    programada: 'bg-blue-500',
    confirmada: 'bg-green-500',
    en_curso: 'bg-amber-500',
    completada: 'bg-emerald-500',
    cancelada: 'bg-red-500',
    no_asistio: 'bg-gray-500'
  }
  return colors[estado] || 'bg-gray-500'
}

const weekDays = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']
</script>

<template>
  <UDashboardPanel id="citas-calendar">
    <template #header>
      <UDashboardNavbar title="Calendario de Citas" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="neutral"
            variant="outline"
            icon="i-lucide-list"
            label="Ver Lista"
            @click="router.visit(route('citas.index'))"
          />
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nueva Cita"
            @click="router.visit(route('citas.create'))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <UCard>
        <template #header>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <UButton
                icon="i-lucide-chevron-left"
                color="neutral"
                variant="ghost"
                size="sm"
                @click="prevMonth"
              />
              <h2 class="text-xl font-semibold capitalize min-w-48 text-center">
                {{ format(currentDate, 'MMMM yyyy', { locale: es }) }}
              </h2>
              <UButton
                icon="i-lucide-chevron-right"
                color="neutral"
                variant="ghost"
                size="sm"
                @click="nextMonth"
              />
            </div>
            <UButton
              color="neutral"
              variant="outline"
              size="sm"
              label="Hoy"
              @click="goToToday"
            />
          </div>
        </template>

        <div class="grid grid-cols-7 gap-px bg-muted rounded-lg overflow-hidden">
          <div
            v-for="day in weekDays"
            :key="day"
            class="bg-elevated p-2 text-center text-sm font-medium text-muted"
          >
            {{ day }}
          </div>

          <div
            v-for="day in calendarDays"
            :key="day.toISOString()"
            :class="[
              'bg-background min-h-28 p-2 transition-colors',
              !isSameMonth(day, currentDate) && 'opacity-40',
              isSameDay(day, new Date()) && 'ring-2 ring-primary-500 ring-inset'
            ]"
          >
            <div class="flex items-center justify-between mb-1">
              <span
                :class="[
                  'text-sm font-medium',
                  isSameDay(day, new Date()) && 'text-primary-500'
                ]"
              >
                {{ format(day, 'd') }}
              </span>
              <span
                v-if="getCitasForDay(day).length > 0"
                class="text-xs bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 px-1.5 py-0.5 rounded-full"
              >
                {{ getCitasForDay(day).length }}
              </span>
            </div>

            <div class="space-y-1">
              <div
                v-for="cita in getCitasForDay(day).slice(0, 3)"
                :key="cita.id"
                :class="[
                  'text-xs p-1 rounded cursor-pointer truncate text-white',
                  getEstadoColor(cita.estado)
                ]"
                @click="router.visit(route('citas.show', cita.id))"
              >
                {{ format(new Date(cita.start), 'HH:mm') }} - {{ cita.mascota.nombre }}
              </div>
              <div
                v-if="getCitasForDay(day).length > 3"
                class="text-xs text-muted text-center"
              >
                +{{ getCitasForDay(day).length - 3 }} mas
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-blue-500"></div>
              <span class="text-sm">Programada</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-green-500"></div>
              <span class="text-sm">Confirmada</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-amber-500"></div>
              <span class="text-sm">En curso</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
              <span class="text-sm">Completada</span>
            </div>
            <div class="flex items-center gap-2">
              <div class="w-3 h-3 rounded-full bg-red-500"></div>
              <span class="text-sm">Cancelada</span>
            </div>
          </div>
        </template>
      </UCard>
    </template>
  </UDashboardPanel>
</template>
