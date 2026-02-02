<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

interface Historia {
  id: string
  mascotaId: string
  fecha: string
  diagnostico: string | null
  tratamiento: string | null
  mascota: {
    id: string
    nombre: string
    especie: string
    raza: string | null
    cliente: { id: string; razonSocial: string } | null
  } | null
  veterinario: { id: string; name: string } | null
}

interface Props {
  historias: {
    data: Historia[]
    meta: { total: number }
  }
  mascotas: { id: string; nombre: string; cliente: string | null }[]
  veterinarios: { id: string; name: string }[]
  filters: {
    mascota_id: string | null
    veterinario_id: string | null
    search: string
  }
  stats: {
    total: number
    esteMes: number
  }
}

const props = defineProps<Props>()

const search = ref(props.filters.search)
const mascotaFilter = ref(props.filters.mascota_id || '_all')
const veterinarioFilter = ref(props.filters.veterinario_id || '_all')
const deleteModal = ref(false)
const historiaToDelete = ref<Historia | null>(null)

const mascotaOptions = computed(() => [
  { label: 'Todas las mascotas', value: '_all' },
  ...props.mascotas.map(m => ({
    label: `${m.nombre}${m.cliente ? ` - ${m.cliente}` : ''}`,
    value: m.id
  }))
])

const veterinarioOptions = computed(() => [
  { label: 'Todos los veterinarios', value: '_all' },
  ...props.veterinarios.map(v => ({ label: v.name, value: v.id }))
])

const applyFilters = () => {
  router.get(route('historias-clinicas.index'), {
    search: search.value || undefined,
    mascota_id: mascotaFilter.value !== '_all' ? mascotaFilter.value : undefined,
    veterinario_id: veterinarioFilter.value !== '_all' ? veterinarioFilter.value : undefined
  }, { preserveState: true })
}

let searchTimeout: ReturnType<typeof setTimeout>
watch(search, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(applyFilters, 500)
})

watch([mascotaFilter, veterinarioFilter], () => {
  applyFilters()
})

const columns = [
  { id: 'fecha', accessorKey: 'fecha', header: 'Fecha' },
  { id: 'mascota', accessorKey: 'mascota', header: 'Paciente' },
  { id: 'diagnostico', accessorKey: 'diagnostico', header: 'Diagnostico' },
  { id: 'veterinario', accessorKey: 'veterinario', header: 'Veterinario' },
  { id: 'actions', accessorKey: 'actions', header: '' }
]

const formatFecha = (fecha: string) => {
  return format(new Date(fecha), "d 'de' MMM, yyyy", { locale: es })
}

const getDropdownActions = (historia: Historia) => [[
  {
    label: 'Ver detalle',
    icon: 'i-lucide-eye',
    onSelect: () => router.visit(route('historias-clinicas.show', historia.id))
  },
  {
    label: 'Editar',
    icon: 'i-lucide-pencil',
    onSelect: () => router.visit(route('historias-clinicas.edit', historia.id))
  },
  {
    label: 'Ver historial completo',
    icon: 'i-lucide-file-text',
    onSelect: () => router.visit(route('historias-clinicas.por-mascota', historia.mascotaId))
  }
], [
  {
    label: 'Eliminar',
    icon: 'i-lucide-trash-2',
    color: 'error' as const,
    onSelect: () => {
      historiaToDelete.value = historia
      deleteModal.value = true
    }
  }
]]

const confirmDelete = () => {
  if (historiaToDelete.value) {
    router.delete(route('historias-clinicas.destroy', historiaToDelete.value.id), {
      onSuccess: () => {
        deleteModal.value = false
        historiaToDelete.value = null
      }
    })
  }
}
</script>

<template>
  <UDashboardPanel id="historias-clinicas">
    <template #header>
      <UDashboardNavbar title="Historias Clinicas" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nueva Historia"
            @click="router.visit(route('historias-clinicas.create'))"
          />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <UInput
            v-model="search"
            icon="i-lucide-search"
            placeholder="Buscar por diagnostico o mascota..."
            class="w-72"
          />
          <USelect
            v-model="mascotaFilter"
            :items="mascotaOptions"
            class="w-48"
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
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
              <UIcon name="i-lucide-file-text" class="w-6 h-6 text-primary-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Total Registros</p>
              <p class="text-2xl font-bold">{{ stats.total }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <UIcon name="i-lucide-calendar" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Este Mes</p>
              <p class="text-2xl font-bold">{{ stats.esteMes }}</p>
            </div>
          </div>
        </UCard>
      </div>

      <UCard>
        <UTable :data="historias.data" :columns="columns">
          <template #fecha-cell="{ row }">
            <div class="flex items-center gap-2">
              <UIcon name="i-lucide-calendar" class="w-4 h-4 text-muted" />
              <span>{{ formatFecha(row.original.fecha) }}</span>
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

          <template #diagnostico-cell="{ row }">
            <p v-if="row.original.diagnostico" class="line-clamp-2 max-w-xs">
              {{ row.original.diagnostico }}
            </p>
            <span v-else class="text-muted">Sin diagnostico</span>
          </template>

          <template #veterinario-cell="{ row }">
            <span v-if="row.original.veterinario">{{ row.original.veterinario.name }}</span>
            <span v-else class="text-muted">-</span>
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

        <div v-if="historias.data.length === 0" class="text-center py-8">
          <UIcon name="i-lucide-file-x" class="w-12 h-12 text-muted mx-auto mb-4" />
          <p class="text-muted">No se encontraron historias clinicas</p>
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
            <h3 class="text-lg font-semibold">Eliminar Historia Clinica</h3>
          </div>
        </template>

        <p>¿Esta seguro que desea eliminar esta historia clinica?</p>
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
