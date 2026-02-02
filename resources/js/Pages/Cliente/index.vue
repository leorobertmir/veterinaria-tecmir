<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Cliente } from '../../types'

interface Props {
  customers: {
    data: Cliente[]
    meta: { total: number }
  }
  stats: {
    total: number
    active: number
    inactive: number
  }
}

const props = defineProps<Props>()
const page = usePage()

const search = ref('')
const deleteModal = ref(false)
const clienteToDelete = ref<Cliente | null>(null)

const filteredClientes = computed(() => {
  if (!search.value) return props.customers.data
  const searchLower = search.value.toLowerCase()
  return props.customers.data.filter(c =>
    c.razonSocial.toLowerCase().includes(searchLower) ||
    c.numeroDocumento.includes(searchLower) ||
    c.email.toLowerCase().includes(searchLower)
  )
})

const columns = [
  { id: 'razonSocial', accessorKey: 'razonSocial', header: 'Cliente' },
  { id: 'numeroDocumento', accessorKey: 'numeroDocumento', header: 'Documento' },
  { id: 'telefono', accessorKey: 'telefono', header: 'Telefono' },
  { id: 'email', accessorKey: 'email', header: 'Email' },
  { id: 'actions', accessorKey: 'actions', header: '' }
]

const getDropdownActions = (cliente: Cliente) => [[
  {
    label: 'Ver detalles',
    icon: 'i-lucide-eye',
    onSelect: () => router.visit(route('clientes.edit', cliente.id))
  },
  {
    label: 'Editar',
    icon: 'i-lucide-pencil',
    onSelect: () => router.visit(route('clientes.edit', cliente.id))
  },
  {
    label: 'Ver mascotas',
    icon: 'i-lucide-paw-print',
    onSelect: () => router.visit(route('mascotas.index', { cliente_id: cliente.id }))
  }
], [
  {
    label: 'Eliminar',
    icon: 'i-lucide-trash-2',
    color: 'error' as const,
    onSelect: () => {
      clienteToDelete.value = cliente
      deleteModal.value = true
    }
  }
]]

const confirmDelete = () => {
  if (clienteToDelete.value) {
    router.delete(route('clientes.destroy', clienteToDelete.value.id), {
      onSuccess: () => {
        deleteModal.value = false
        clienteToDelete.value = null
      }
    })
  }
}
</script>

<template>
  <UDashboardPanel id="clientes">
    <template #header>
      <UDashboardNavbar title="Clientes" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nuevo Cliente"
            @click="router.visit(route('clientes.create'))"
          />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <UInput
            v-model="search"
            icon="i-lucide-search"
            placeholder="Buscar cliente..."
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
              <UIcon name="i-lucide-users" class="w-6 h-6 text-primary-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Total Clientes</p>
              <p class="text-2xl font-bold">{{ stats.total }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <UIcon name="i-lucide-user-check" class="w-6 h-6 text-green-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Activos</p>
              <p class="text-2xl font-bold">{{ stats.active }}</p>
            </div>
          </div>
        </UCard>

        <UCard>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
              <UIcon name="i-lucide-paw-print" class="w-6 h-6 text-orange-500" />
            </div>
            <div>
              <p class="text-sm text-muted">Con Mascotas</p>
              <p class="text-2xl font-bold">{{ stats.active }}</p>
            </div>
          </div>
        </UCard>
      </div>

      <UCard>
        <UTable :data="filteredClientes" :columns="columns">
          <template #razonSocial-cell="{ row }">
            <div class="flex items-center gap-3">
              <UAvatar :alt="row.original.razonSocial" size="sm" />
              <div>
                <p class="font-medium">{{ row.original.razonSocial }}</p>
                <p class="text-xs text-muted">{{ row.original.tipoDocumento }}</p>
              </div>
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

        <div v-if="filteredClientes.length === 0" class="text-center py-8">
          <UIcon name="i-lucide-users" class="w-12 h-12 text-muted mx-auto mb-4" />
          <p class="text-muted">No se encontraron clientes</p>
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
            <h3 class="text-lg font-semibold">Eliminar Cliente</h3>
          </div>
        </template>

        <p>
          ¿Esta seguro que desea eliminar al cliente
          <strong>{{ clienteToDelete?.razonSocial }}</strong>?
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
