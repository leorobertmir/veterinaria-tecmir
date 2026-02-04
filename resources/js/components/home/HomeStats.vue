<script setup lang="ts">
import { computed } from 'vue'
import type { Period, Range, Stat } from '../../types'

const props = defineProps<{
  period: Period
  range: Range
  stats: {
    revenue: number
    orders: number
    customers: number
    growth: number
  }
}>()

function formatCurrency(value: number): string {
  // Mantenemos en-US para asegurar que el símbolo $ se muestre bien
  return value.toLocaleString('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0
  })
}

// AQUÍ ESTÁ LA TRADUCCIÓN
const stats = computed<Stat[]>(() => ([
  {
    title: 'Ingresos', // Antes Revenue
    icon: 'i-lucide-circle-dollar-sign',
    value: formatCurrency(props.stats.revenue),
    variation: props.stats.growth
  },
  {
    title: 'Ventas', // Antes Orders
    icon: 'i-lucide-shopping-cart',
    value: props.stats.orders,
    variation: 0
  },
  {
    title: 'Clientes', // Antes Customers
    icon: 'i-lucide-users',
    value: props.stats.customers,
    variation: 0
  },
  {
    title: 'Crecimiento', // Antes Growth
    icon: 'i-lucide-trending-up',
    value: `${props.stats.growth.toFixed(1)}%`,
    variation: props.stats.growth
  }
]))
</script>

<template>
  <UPageGrid class="lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-px">
    <UPageCard
      v-for="(stat, index) in stats"
      :key="index"
      :icon="stat.icon"
      :title="stat.title"
      to="/customers"
      variant="subtle"
      :ui="{
        container: 'gap-y-1.5',
        wrapper: 'items-start',
        leading: 'p-2.5 rounded-full bg-primary/10 ring ring-inset ring-primary/25 flex-col',
        title: 'font-normal text-muted text-xs uppercase'
      }"
      class="lg:rounded-none first:rounded-l-lg last:rounded-r-lg hover:z-1"
    >
      <div class="flex items-center gap-2">
        <span class="text-2xl font-semibold text-highlighted">
          {{ stat.value }}
        </span>

        <UBadge
          :color="stat.variation > 0 ? 'success' : 'error'"
          variant="subtle"
          class="text-xs"
        >
          {{ stat.variation > 0 ? '+' : '' }}{{ stat.variation }}%
        </UBadge>
      </div>
    </UPageCard>
  </UPageGrid>
</template>