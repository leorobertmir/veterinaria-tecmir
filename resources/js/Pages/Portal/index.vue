<script setup lang="ts">
import { computed } from 'vue'

interface Mascota {
  id: string
  nombre: string
  especie: string
  raza?: string | null
  sexo?: string | null
  fechaNacimiento?: string | null
}

interface Cita {
  id: string
  mascotaId: string
  fechaHora: string
  motivo: string
  estado: string
  tipoConsulta?: string | null
}

const props = defineProps<{
  cliente?: {
    id: string
    razonSocial: string
    email: string
  } | null
  mascotas: Mascota[]
  citas: Cita[]
}>()

const mascotaColumns = [
  { id: 'nombre', accessorKey: 'nombre', header: 'Mascota' },
  { id: 'especie', accessorKey: 'especie', header: 'Especie' },
  { id: 'raza', accessorKey: 'raza', header: 'Raza' },
  { id: 'sexo', accessorKey: 'sexo', header: 'Sexo' }
]

const citaColumns = [
  { id: 'fechaHora', accessorKey: 'fechaHora', header: 'Fecha' },
  { id: 'motivo', accessorKey: 'motivo', header: 'Motivo' },
  { id: 'estado', accessorKey: 'estado', header: 'Estado' },
  { id: 'tipoConsulta', accessorKey: 'tipoConsulta', header: 'Tipo' }
]

const formatDateTime = (value?: string | null) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('es-ES')
}

const hasData = computed(() => props.mascotas.length > 0 || props.citas.length > 0)
</script>

<template>
  <UDashboardPanel id="portal">
    <template #header>
      <UDashboardNavbar title="Portal Cliente" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <UCard>
          <p class="text-sm text-muted">Cliente</p>
          <p class="text-lg font-semibold">{{ props.cliente?.razonSocial || 'Sin datos' }}</p>
          <p class="text-sm text-muted">{{ props.cliente?.email || '' }}</p>
        </UCard>

        <UCard>
          <p class="text-sm text-muted">Mascotas registradas</p>
          <p class="text-2xl font-bold">{{ props.mascotas.length }}</p>
        </UCard>

        <UCard>
          <p class="text-sm text-muted">Citas programadas</p>
          <p class="text-2xl font-bold">{{ props.citas.length }}</p>
        </UCard>
      </div>

      <div v-if="hasData" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold">Mis Mascotas</h3>
            </div>
          </template>

          <UTable :data="props.mascotas" :columns="mascotaColumns">
            <template #raza-cell="{ row }">
              <span>{{ row.original.raza || '-' }}</span>
            </template>
            <template #sexo-cell="{ row }">
              <span>{{ row.original.sexo || '-' }}</span>
            </template>
          </UTable>

          <div v-if="props.mascotas.length === 0" class="text-center py-6 text-muted">
            No hay mascotas registradas.
          </div>
        </UCard>

        <UCard>
          <template #header>
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold">Mis Citas</h3>
            </div>
          </template>

          <UTable :data="props.citas" :columns="citaColumns">
            <template #fechaHora-cell="{ row }">
              <span>{{ formatDateTime(row.original.fechaHora) }}</span>
            </template>
            <template #tipoConsulta-cell="{ row }">
              <span>{{ row.original.tipoConsulta || '-' }}</span>
            </template>
          </UTable>

          <div v-if="props.citas.length === 0" class="text-center py-6 text-muted">
            No hay citas registradas.
          </div>
        </UCard>
      </div>

      <UCard v-else>
        <div class="text-center py-10">
          <UIcon name="i-lucide-paw-print" class="w-10 h-10 text-muted mx-auto mb-3" />
          <p class="text-muted">Aun no tienes mascotas ni citas registradas.</p>
        </div>
      </UCard>
    </template>
  </UDashboardPanel>
</template>
