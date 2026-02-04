<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Producto } from '../../types'

interface Props {
  productos: {
    data: Producto[]
    meta: { total: number }
  }
  stats: {
    total: number
    active: number
    lowStock: number
  }
}

const props = defineProps<Props>()

const search = ref('')
const deleteModal = ref(false)
const productoToDelete = ref<Producto | null>(null)

const filteredProductos = computed(() => {
  if (!search.value) return props.productos.data
  const searchLower = search.value.toLowerCase()
  return props.productos.data.filter(p =>
    p.nombre.toLowerCase().includes(searchLower) ||
    p.codigo.toLowerCase().includes(searchLower)
  )
})

const columns = [
  { id: 'nombre', accessorKey: 'nombre', header: 'Producto' },
  { id: 'codigo', accessorKey: 'codigo', header: 'Codigo' },
  { id: 'precioUnitario', accessorKey: 'precioUnitario', header: 'Precio' },
  { id: 'stock', accessorKey: 'stock', header: 'Stock' },
  { id: 'actions', accessorKey: 'actions', header: '' }
]

const getDropdownActions = (producto: Producto) => [[
  {
    label: 'Ver detalles',
    icon: 'i-lucide-eye',
    onSelect: () => router.visit(route('productos.edit', producto.id))
  },
  {
    label: 'Editar',
    icon: 'i-lucide-pencil',
    onSelect: () => router.visit(route('productos.edit', producto.id))
  }
], [
  {
    label: 'Eliminar',
    icon: 'i-lucide-trash-2',
    color: 'error' as const,
    onSelect: () => {
      productoToDelete.value = producto
      deleteModal.value = true
    }
  }
]]

const confirmDelete = () => {
  if (productoToDelete.value) {
    router.delete(route('productos.destroy', productoToDelete.value.id), {
      onSuccess: () => {
        deleteModal.value = false
        productoToDelete.value = null
      }
    })
  }
}

// NUEVA FUNCION: Determina si un producto debe mostrar alerta de stock
const isLowStock = (producto: Producto) => {
  // Si es un servicio, NUNCA es stock bajo (aunque sea 0)
  if (producto.tipo === 'servicio') return false;
  // Si es un bien, revisamos si es menor a 5
  return producto.stock < 5;
}

// Se actualizó rowClass para usar la nueva lógica
const rowClass = (row: { original: Producto }) => {
  return isLowStock(row.original) ? 'bg-red-50 text-red-700' : ''
}
</script>

<template>
  <UDashboardPanel id="productos">
    <template #header>
      <UDashboardNavbar title="Productos" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nuevo Producto"
            @click="router.visit(route('productos.create'))"
          />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <UInput
            v-model="search"
            icon="i-lucide-search"
            placeholder="Buscar producto..."
            class="w-64"
          />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
              <UIcon name="i-lucide-box" class="w-6 h-6 text-primary-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Total Productos</p>
              <p class="text-2xl font-bold">{{ stats.total }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <UIcon name="i-lucide-check-circle" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Activos</p>
              <p class="text-2xl font-bold">{{ stats.active }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
              <UIcon name="i-lucide-alert-triangle" class="w-6 h-6 text-red-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Stock Bajo</p>
              <p class="text-2xl font-bold">{{ stats.lowStock }}</p>
            </div>
          </div>
        </UCard>
      </div>

      <UCard>
        <UTable :data="filteredProductos" :columns="columns" :row-class="rowClass">
          
          <template #nombre-cell="{ row }">
            <div class="flex items-center gap-3" :class="isLowStock(row.original) ? 'text-red-700' : ''">
              <UAvatar :alt="row.original.nombre" size="sm" />
              <div>
                <p class="font-medium">{{ row.original.nombre }}</p>
                <p class="text-xs text-muted">{{ row.original.codigo }}</p>
              </div>
            </div>
          </template>

          <template #codigo-cell="{ row }">
            <span :class="isLowStock(row.original) ? 'text-red-700' : ''">
              {{ row.original.codigo }}
            </span>
          </template>

          <template #precioUnitario-cell="{ row }">
            <span class="font-medium" :class="isLowStock(row.original) ? 'text-red-700' : ''">
              $ {{ row.original.precioUnitario.toFixed(2) }}
            </span>
          </template>

          <template #stock-cell="{ row }">
            <div class="flex items-center gap-2">
              <span :class="isLowStock(row.original) ? 'text-red-700 font-semibold' : ''">
                {{ row.original.stock }}
              </span>
              <UBadge v-if="isLowStock(row.original)" color="error" variant="subtle">
                Stock Bajo
              </UBadge>
            </div>
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

        <div v-if="filteredProductos.length === 0" class="text-center py-8">
          <UIcon name="i-lucide-box" class="w-12 h-12 text-muted mx-auto mb-4" />
          <p class="text-muted">No se encontraron productos</p>
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
            <h3 class="text-lg font-semibold">Eliminar Producto</h3>
          </div>
        </template>

        <p>
          ¿Esta seguro que desea eliminar el producto
          <strong>{{ productoToDelete?.nombre }}</strong>?
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