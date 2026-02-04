<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { sub } from 'date-fns'
import type { Period, Range } from '../types'
import HomeDateRangePicker from '../components/home/HomeDateRangePicker.vue'
import HomePeriodSelect from '../components/home/HomePeriodSelect.vue'
import HomeStats from '../components/home/HomeStats.vue'
import HomeChart from '../components/home/HomeChart.client.vue'

// Se eliminó la importación de HomeSales para quitar la tabla inferior

interface SalesDatum {
  date: string
  amount: number
}

const props = defineProps<{
  revenue: number
  orders: number
  customers: number
  growth: number
  salesData: SalesDatum[]
}>()

const range = shallowRef<Range>({
  start: sub(new Date(), { days: 14 }),
  end: new Date()
})
const period = ref<Period>('daily')
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <UDashboardNavbar title="Inicio" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <HomeDateRangePicker v-model="range" class="-ms-1" />

          <HomePeriodSelect v-model="period" :range="range" />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <HomeStats
        :period="period"
        :range="range"
        :stats="{ revenue: props.revenue, orders: props.orders, customers: props.customers, growth: props.growth }"
      />
      <HomeChart :period="period" :range="range" :sales-data="props.salesData" />
      
      </template>
  </UDashboardPanel>
</template>