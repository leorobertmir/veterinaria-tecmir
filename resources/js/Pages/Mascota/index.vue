<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface Mascota {
  id: string
  nombre: string
  especie: string
  raza: string | null
  color: string | null
  sexo: string | null
  fechaNacimiento: string | null
  peso: number | null
  activo: boolean
  cliente: {
    id: string
    razonSocial: string
    telefono: string
  } | null
}

interface Props {
  mascotas: {
    data: Mascota[]
    meta: { total: number }
  }
  stats: {
    total: number
    perros: number
    gatos: number
    otros: number
  }
}

const props = defineProps<Props>()

const search = ref('')
const especieFilter = ref('todos')
const deleteModal = ref(false)
const mascotaToDelete = ref<Mascota | null>(null)

const especieOptions = [
  { label: 'Todos', value: 'todos' },
  { label: 'Perros', value: 'perro' },
  { label: 'Gatos', value: 'gato' },
  { label: 'Aves', value: 'ave' },
  { label: 'Roedores', value: 'roedor' },
  { label: 'Reptiles', value: 'reptil' },
  { label: 'Otros', value: 'otro' }
]

const filteredMascotas = computed(() => {
  let result = props.mascotas.data

  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(m =>
      m.nombre.toLowerCase().includes(searchLower) ||
      m.cliente?.razonSocial.toLowerCase().includes(searchLower) ||
      m.raza?.toLowerCase().includes(searchLower)
    )
  }

  if (especieFilter.value !== 'todos') {
    result = result.filter(m => m.especie === especieFilter.value)
  }

  return result
})

const columns = [
  { id: 'nombre', accessorKey: 'nombre', header: 'Mascota' },
  { id: 'especie', accessorKey: 'especie', header: 'Especie' },
  { id: 'cliente', accessorKey: 'cliente', header: 'Propietario' },
  { id: 'peso', accessorKey: 'peso', header: 'Peso' },
  { id: 'actions', accessorKey: 'actions', header: '' }
]

const getEspecieIcon = (especie: string) => {
  const icons: Record<string, string> = {
    perro: 'i-lucide-dog',
    gato: 'i-lucide-cat',
    ave: 'i-lucide-bird',
    roedor: 'i-lucide-rabbit',
    reptil: 'i-lucide-turtle',
    otro: 'i-lucide-paw-print'
  }
  return icons[especie] || 'i-lucide-paw-print'
}

const getEspecieColor = (especie: string) => {
  const colors: Record<string, string> = {
    perro: 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
    gato: 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
    ave: 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
    roedor: 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
    reptil: 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
    otro: 'bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300'
  }
  return colors[especie] || colors.otro
}

const getDropdownActions = (mascota: Mascota) => [[
  {
    label: 'Ver historial clinico',
    icon: 'i-lucide-file-text',
    onSelect: () => router.visit(route('historias-clinicas.por-mascota', mascota.id))
  },
  {
    label: 'Agendar cita',
    icon: 'i-lucide-calendar-plus',
    onSelect: () => router.visit(route('citas.create', { mascota_id: mascota.id }))
  },
  {
    label: 'Editar',
    icon: 'i-lucide-pencil',
    onSelect: () => router.visit(route('mascotas.edit', mascota.id))
  }
], [
  {
    label: 'Eliminar',
    icon: 'i-lucide-trash-2',
    color: 'error' as const,
    onSelect: () => {
      mascotaToDelete.value = mascota
      deleteModal.value = true
    }
  }
]]

const confirmDelete = () => {
  if (mascotaToDelete.value) {
    router.delete(route('mascotas.destroy', mascotaToDelete.value.id), {
      onSuccess: () => {
        deleteModal.value = false
        mascotaToDelete.value = null
      }
    })
  }
}
</script>

<template>
  <UDashboardPanel id="mascotas">
    <template #header>
      <UDashboardNavbar title="Mascotas" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nueva Mascota"
            @click="router.visit(route('mascotas.create'))"
          />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <UInput
            v-model="search"
            icon="i-lucide-search"
            placeholder="Buscar mascota..."
            class="w-64"
          />
          <USelect
            v-model="especieFilter"
            :items="especieOptions"
            class="w-40"
          />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
              <UIcon name="i-lucide-paw-print" class="w-6 h-6 text-primary-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Total</p>
              <p class="text-2xl font-bold">{{ stats.total }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg">
              <UIcon name="i-lucide-dog" class="w-6 h-6 text-amber-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Perros</p>
              <p class="text-2xl font-bold">{{ stats.perros }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
              <UIcon name="i-lucide-cat" class="w-6 h-6 text-purple-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Gatos</p>
              <p class="text-2xl font-bold">{{ stats.gatos }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <UIcon name="i-lucide-heart-pulse" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Otros</p>
              <p class="text-2xl font-bold">{{ stats.otros }}</p>
            </div>
          </div>
        </UCard>
      </div>

      <UCard>
        <UTable :data="filteredMascotas" :columns="columns">
          <template #nombre-cell="{ row }">
            <div class="flex items-center gap-3">
              <div :class="['p-2 rounded-lg', getEspecieColor(row.original.especie)]">
                <UIcon :name="getEspecieIcon(row.original.especie)" class="w-5 h-5" />
              </div>
              <div>
                <p class="font-medium">{{ row.original.nombre }}</p>
                <p class="text-xs text-muted">{{ row.original.raza || 'Sin especificar' }}</p>
              </div>
            </div>
          </template>

          <template #especie-cell="{ row }">
            <UBadge :color="row.original.especie === 'perro' ? 'warning' : row.original.especie === 'gato' ? 'primary' : 'neutral'">
              {{ row.original.especie }}
            </UBadge>
          </template>

          <template #cliente-cell="{ row }">
            <div v-if="row.original.cliente">
              <p class="font-medium">{{ row.original.cliente.razonSocial }}</p>
              <p class="text-xs text-muted">{{ row.original.cliente.telefono }}</p>
            </div>
            <span v-else class="text-muted">-</span>
          </template>

          <template #peso-cell="{ row }">
            <span v-if="row.original.peso">{{ row.original.peso }} kg</span>
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

        <div v-if="filteredMascotas.length === 0" class="text-center py-8">
          <UIcon name="i-lucide-paw-print" class="w-12 h-12 text-muted mx-auto mb-4" />
          <p class="text-muted">No se encontraron mascotas</p>
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
            <h3 class="text-lg font-semibold">Eliminar Mascota</h3>
          </div>
        </template>

        <p>
          ¿Esta seguro que desea eliminar a
          <strong>{{ mascotaToDelete?.nombre }}</strong>?
        </p>
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
