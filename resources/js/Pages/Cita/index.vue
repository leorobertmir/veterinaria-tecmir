<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

interface Cita {
  id: string
  mascotaId: string
  veterinarioId: string
  fechaHora: string
  motivo: string
  estado: string
  tipoConsulta: string
  mascota: {
    id: string
    nombre: string
    especie: string
    raza: string | null
    cliente: {
      id: string
      razonSocial: string
      telefono: string
    } | null
  } | null
  veterinario: {
    id: string
    name: string
  } | null
}

interface Props {
  citas: {
    data: Cita[]
    meta: { total: number }
  }
  veterinarios: { id: string; name: string }[]
  stats: {
    hoy: number
    programadas: number
    completadas: number
    canceladas: number
  }
  filters: {
    fecha: string
    estado: string
    veterinario_id: string | null
  }
}

const props = defineProps<Props>()

const fecha = ref(props.filters.fecha)
const estadoFilter = ref(props.filters.estado)
const veterinarioFilter = ref(props.filters.veterinario_id || '_all')
const deleteModal = ref(false)
const citaToDelete = ref<Cita | null>(null)

const estadoOptions = [
  { label: 'Todos', value: 'todos' },
  { label: 'Programadas', value: 'programada' },
  { label: 'Confirmadas', value: 'confirmada' },
  { label: 'En curso', value: 'en_curso' },
  { label: 'Completadas', value: 'completada' },
  { label: 'Canceladas', value: 'cancelada' },
  { label: 'No asistio', value: 'no_asistio' }
]

const veterinarioOptions = computed(() => [
  { label: 'Todos los veterinarios', value: '_all' },
  ...props.veterinarios.map(v => ({ label: v.name, value: v.id }))
])

const applyFilters = () => {
  router.get(route('citas.index'), {
    fecha: fecha.value,
    estado: estadoFilter.value,
    veterinario_id: veterinarioFilter.value !== '_all' ? veterinarioFilter.value : undefined
  }, { preserveState: true })
}

watch([fecha, estadoFilter, veterinarioFilter], () => {
  applyFilters()
})

const columns = [
  { id: 'hora', accessorKey: 'fechaHora', header: 'Hora' },
  { id: 'mascota', accessorKey: 'mascota', header: 'Paciente' },
  { id: 'tipoConsulta', accessorKey: 'tipoConsulta', header: 'Tipo' },
  { id: 'veterinario', accessorKey: 'veterinario', header: 'Veterinario' },
  { id: 'estado', accessorKey: 'estado', header: 'Estado' },
  { id: 'actions', accessorKey: 'actions', header: '' }
]

const getEstadoColor = (estado: string) => {
  const colors: Record<string, 'primary' | 'success' | 'warning' | 'error' | 'neutral'> = {
    programada: 'primary',
    confirmada: 'success',
    en_curso: 'warning',
    completada: 'success',
    cancelada: 'error',
    no_asistio: 'neutral'
  }
  return colors[estado] || 'neutral'
}

const getTipoConsultaLabel = (tipo: string) => {
  const labels: Record<string, string> = {
    consulta_general: 'Consulta General',
    vacunacion: 'Vacunacion',
    cirugia: 'Cirugia',
    emergencia: 'Emergencia',
    control: 'Control',
    peluqueria: 'Peluqueria',
    desparasitacion: 'Desparasitacion'
  }
  return labels[tipo] || tipo
}

const formatHora = (fechaHora: string) => {
  return format(new Date(fechaHora), 'HH:mm', { locale: es })
}

const cambiarEstado = (cita: Cita, nuevoEstado: string) => {
  router.patch(route('citas.update-estado', cita.id), { estado: nuevoEstado })
}

const getDropdownActions = (cita: Cita) => {
  const actions = [[
    {
      label: 'Ver detalles',
      icon: 'i-lucide-eye',
      onSelect: () => router.visit(route('citas.show', cita.id))
    },
    {
      label: 'Editar',
      icon: 'i-lucide-pencil',
      onSelect: () => router.visit(route('citas.edit', cita.id))
    }
  ]]

  if (cita.estado === 'programada' || cita.estado === 'confirmada') {
    actions[0].push({
      label: 'Crear historia clinica',
      icon: 'i-lucide-file-plus',
      onSelect: () => router.visit(route('historias-clinicas.create', { cita_id: cita.id }))
    })
  }

  actions.push([
    {
      label: 'Cancelar cita',
      icon: 'i-lucide-x-circle',
      color: 'error' as const,
      onSelect: () => cambiarEstado(cita, 'cancelada')
    },
    {
      label: 'Eliminar',
      icon: 'i-lucide-trash-2',
      color: 'error' as const,
      onSelect: () => {
        citaToDelete.value = cita
        deleteModal.value = true
      }
    }
  ])

  return actions
}

const confirmDelete = () => {
  if (citaToDelete.value) {
    router.delete(route('citas.destroy', citaToDelete.value.id), {
      onSuccess: () => {
        deleteModal.value = false
        citaToDelete.value = null
      }
    })
  }
}
</script>

<template>
  <UDashboardPanel id="citas">
    <template #header>
      <UDashboardNavbar title="Agenda de Citas" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="neutral"
            variant="outline"
            icon="i-lucide-calendar"
            label="Ver Calendario"
            @click="router.visit(route('citas.calendar'))"
          />
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nueva Cita"
            @click="router.visit(route('citas.create'))"
          />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <UInput
            v-model="fecha"
            type="date"
            class="w-40"
          />
          <USelect
            v-model="estadoFilter"
            :items="estadoOptions"
            class="w-40"
          />
          <USelect
            v-model="veterinarioFilter"
            :items="veterinarioOptions"
            class="w-48"
          />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
              <UIcon name="i-lucide-calendar" class="w-6 h-6 text-primary-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Citas Hoy</p>
              <p class="text-2xl font-bold">{{ stats.hoy }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
              <UIcon name="i-lucide-clock" class="w-6 h-6 text-blue-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Programadas</p>
              <p class="text-2xl font-bold">{{ stats.programadas }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <UIcon name="i-lucide-check-circle" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Completadas</p>
              <p class="text-2xl font-bold">{{ stats.completadas }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
              <UIcon name="i-lucide-x-circle" class="w-6 h-6 text-red-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Canceladas</p>
              <p class="text-2xl font-bold">{{ stats.canceladas }}</p>
            </div>
          </div>
        </UCard>
      </div>

      <UCard>
        <UTable :data="citas.data" :columns="columns">
          <template #hora-cell="{ row }">
            <div class="flex items-center gap-2">
              <UIcon name="i-lucide-clock" class="w-4 h-4 text-muted" />
              <span class="font-mono font-medium">{{ formatHora(row.original.fechaHora) }}</span>
            </div>
          </template>

          <template #mascota-cell="{ row }">
            <div v-if="row.original.mascota">
              <p class="font-medium">{{ row.original.mascota.nombre }}</p>
              <p class="text-xs text-muted">
                {{ row.original.mascota.especie }}
                <span v-if="row.original.mascota.cliente"> - {{ row.original.mascota.cliente.razonSocial }}</span>
              </p>
            </div>
            <span v-else class="text-muted">-</span>
          </template>

          <template #tipoConsulta-cell="{ row }">
            <UBadge variant="subtle" color="neutral">
              {{ getTipoConsultaLabel(row.original.tipoConsulta) }}
            </UBadge>
          </template>

          <template #veterinario-cell="{ row }">
            <span v-if="row.original.veterinario">{{ row.original.veterinario.name }}</span>
            <span v-else class="text-muted">-</span>
          </template>

          <template #estado-cell="{ row }">
            <UBadge :color="getEstadoColor(row.original.estado)">
              {{ row.original.estado }}
            </UBadge>
          </template>

          <template #actions-cell="{ row }">
            <UDropdownMenu :items="getDropdownActions(row.original)">
              <UButton
                icon="i-lucide-ellipsis-vertical"
                color="neutral"
                variant="ghost"
                size="sm"
              />
            </UDropdownMenu>
          </template>
        </UTable>

        <div v-if="citas.data.length === 0" class="text-center py-8">
          <UIcon name="i-lucide-calendar-x" class="w-12 h-12 text-muted mx-auto mb-4" />
          <p class="text-muted">No hay citas para esta fecha</p>
        </div>
      </UCard>
    </template>
  </UDashboardPanel>

  <UModal v-model:open="deleteModal">
    <template #content>
      <UCard>
        <template #header>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
              <UIcon name="i-lucide-alert-triangle" class="w-6 h-6 text-red-500" />
            </div>
            <h3 class="text-lg font-semibold">Eliminar Cita</h3>
          </div>
        </template>

        <p>¿Esta seguro que desea eliminar esta cita?</p>
        <p class="text-sm text-muted mt-2">Esta accion no se puede deshacer.</p>

        <template #footer>
          <div class="flex justify-end gap-3">
            <UButton color="neutral" variant="outline" label="Cancelar" @click="deleteModal = false" />
            <UButton color="error" label="Eliminar" @click="confirmDelete" />
          </div>
        </template>
      </UCard>
    </template>
  </UModal>
</template>
