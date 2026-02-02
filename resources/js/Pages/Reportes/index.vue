<script setup lang="ts">
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { route } from 'ziggy-js'

interface ReporteVentaMes {
  periodo: string
  total: number
}

interface TopProducto {
  id: string
  nombre: string
  cantidad: number
  total: number
}

const loading = ref(true)
const error = ref<string | null>(null)
const ventasTotales = ref<number>(0)
const ventasPorMes = ref<ReporteVentaMes[]>([])
const topProductos = ref<TopProducto[]>([])

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-PE', {
    style: 'currency',
    currency: 'PEN'
  }).format(value)
}

const loadReportes = async () => {
  try {
    loading.value = true
    const response = await axios.get(route('reportes.data'))
    ventasTotales.value = response.data.ventas_totales || 0
    ventasPorMes.value = response.data.ventas_por_mes || []
    topProductos.value = response.data.top_productos || []
  } catch (err: any) {
    error.value = err?.message || 'No se pudo cargar el reporte'
  } finally {
    loading.value = false
  }
}

onMounted(loadReportes)
</script>

<template>
  <UDashboardPanel id="reportes">
    <template #header>
      <UDashboardNavbar title="Reportes" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div v-if="loading" class="text-muted">Cargando reportes...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>

      <div v-else>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
          <UCard>
            <p class="text-sm text-muted">Ventas Totales</p>
            <p class="text-2xl font-bold">{{ formatCurrency(ventasTotales) }}</p>
          </UCard>

          <UCard>
            <p class="text-sm text-muted">Ultimo Mes</p>
            <p class="text-2xl font-bold">
              {{ formatCurrency(ventasPorMes.length ? ventasPorMes[ventasPorMes.length - 1].total : 0) }}
            </p>
          </UCard>

          <UCard>
            <p class="text-sm text-muted">Top Producto</p>
            <p class="text-lg font-semibold">
              {{ topProductos.length ? topProductos[0].nombre : 'Sin datos' }}
            </p>
            <p class="text-sm text-muted">
              {{ topProductos.length ? topProductos[0].cantidad : 0 }} unidades
            </p>
          </UCard>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <UCard>
            <template #header>
              <h3 class="text-lg font-semibold">Ventas por Mes</h3>
            </template>
            <UTable :data="ventasPorMes" :columns="[
              { id: 'periodo', accessorKey: 'periodo', header: 'Periodo' },
              { id: 'total', accessorKey: 'total', header: 'Total' }
            ]">
              <template #total-cell="{ row }">
                <span>{{ formatCurrency(row.original.total) }}</span>
              </template>
            </UTable>
          </UCard>

          <UCard>
            <template #header>
              <h3 class="text-lg font-semibold">Top Productos</h3>
            </template>
            <UTable :data="topProductos" :columns="[
              { id: 'nombre', accessorKey: 'nombre', header: 'Producto' },
              { id: 'cantidad', accessorKey: 'cantidad', header: 'Cantidad' },
              { id: 'total', accessorKey: 'total', header: 'Total' }
            ]">
              <template #total-cell="{ row }">
                <span>{{ formatCurrency(row.original.total) }}</span>
              </template>
            </UTable>
          </UCard>
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
