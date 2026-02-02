<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

interface Historia {
  id: string
  fecha: string
  diagnostico: string | null
  tratamiento: string | null
  peso: number | null
  veterinario: { id: string; name: string } | null
}

interface Mascota {
  id: string
  nombre: string
  especie: string
  raza: string | null
  sexo: string | null
  fechaNacimiento: string | null
  peso: number | null
  cliente: {
    id: string
    razonSocial: string
    telefono: string
  } | null
}

interface Props {
  mascota: Mascota
  historias: {
    data: Historia[]
    meta: { total: number }
  }
}

const props = defineProps<Props>()

const formatFecha = (fecha: string) => {
  return format(new Date(fecha), "d 'de' MMM, yyyy", { locale: es })
}

const calcularEdad = (fechaNacimiento: string | null) => {
  if (!fechaNacimiento) return null
  const nacimiento = new Date(fechaNacimiento)
  const hoy = new Date()
  const años = hoy.getFullYear() - nacimiento.getFullYear()
  const meses = hoy.getMonth() - nacimiento.getMonth()
  if (años > 0) return `${años} año${años > 1 ? 's' : ''}`
  if (meses > 0) return `${meses} mes${meses > 1 ? 'es' : ''}`
  return 'Menos de 1 mes'
}
</script>

<template>
  <UDashboardPanel id="historia-por-mascota">
    <template #header>
      <UDashboardNavbar :title="`Historial de ${mascota.nombre}`">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('mascotas.index'))"
          />
        </template>

        <template #right>
          <UButton
            color="primary"
            icon="i-lucide-plus"
            label="Nueva Consulta"
            @click="router.visit(route('historias-clinicas.create', { mascota_id: mascota.id }))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-4xl mx-auto space-y-6">
        <UCard>
          <div class="flex flex-col md:flex-row md:items-center gap-6">
            <div class="flex items-center gap-4">
              <div class="p-4 bg-primary-100 dark:bg-primary-900 rounded-xl">
                <UIcon name="i-lucide-paw-print" class="w-12 h-12 text-primary-500" />
              </div>
              <div>
                <h2 class="text-2xl font-bold">{{ mascota.nombre }}</h2>
                <p class="text-muted">
                  {{ mascota.especie }}
                  {{ mascota.raza ? ` - ${mascota.raza}` : '' }}
                </p>
              </div>
            </div>

            <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
              <div v-if="mascota.sexo" class="text-center">
                <p class="text-xs text-muted">Sexo</p>
                <p class="font-medium capitalize">{{ mascota.sexo }}</p>
              </div>
              <div v-if="mascota.fechaNacimiento" class="text-center">
                <p class="text-xs text-muted">Edad</p>
                <p class="font-medium">{{ calcularEdad(mascota.fechaNacimiento) }}</p>
              </div>
              <div v-if="mascota.peso" class="text-center">
                <p class="text-xs text-muted">Peso Actual</p>
                <p class="font-medium">{{ mascota.peso }} kg</p>
              </div>
              <div class="text-center">
                <p class="text-xs text-muted">Consultas</p>
                <p class="font-medium">{{ historias.meta.total }}</p>
              </div>
            </div>
          </div>

          <div v-if="mascota.cliente" class="mt-4 pt-4 border-t border-default">
            <p class="text-sm text-muted mb-1">Propietario</p>
            <div class="flex items-center justify-between">
              <p class="font-medium">{{ mascota.cliente.razonSocial }}</p>
              <div class="flex items-center gap-2 text-muted">
                <UIcon name="i-lucide-phone" class="w-4 h-4" />
                <span>{{ mascota.cliente.telefono }}</span>
              </div>
            </div>
          </div>
        </UCard>

        <div class="space-y-4">
          <h3 class="text-lg font-semibold flex items-center gap-2">
            <UIcon name="i-lucide-history" class="w-5 h-5" />
            Historial Clinico
          </h3>

          <div v-if="historias.data.length > 0" class="space-y-4">
            <UCard
              v-for="historia in historias.data"
              :key="historia.id"
              class="cursor-pointer hover:ring-2 hover:ring-primary-500 transition-all"
              @click="router.visit(route('historias-clinicas.show', historia.id))"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <UBadge color="primary">
                      {{ formatFecha(historia.fecha) }}
                    </UBadge>
                    <span v-if="historia.veterinario" class="text-sm text-muted">
                      Dr. {{ historia.veterinario.name }}
                    </span>
                    <span v-if="historia.peso" class="text-sm text-muted">
                      {{ historia.peso }} kg
                    </span>
                  </div>

                  <div v-if="historia.diagnostico" class="mb-2">
                    <p class="text-sm text-muted">Diagnostico:</p>
                    <p class="font-medium">{{ historia.diagnostico }}</p>
                  </div>

                  <div v-if="historia.tratamiento">
                    <p class="text-sm text-muted">Tratamiento:</p>
                    <p class="text-sm line-clamp-2">{{ historia.tratamiento }}</p>
                  </div>
                </div>

                <UIcon name="i-lucide-chevron-right" class="w-5 h-5 text-muted flex-shrink-0" />
              </div>
            </UCard>
          </div>

          <div v-else class="text-center py-12">
            <UIcon name="i-lucide-file-x" class="w-16 h-16 text-muted mx-auto mb-4" />
            <p class="text-lg font-medium mb-2">Sin historial clinico</p>
            <p class="text-muted mb-4">Esta mascota aun no tiene consultas registradas</p>
            <UButton
              color="primary"
              icon="i-lucide-plus"
              label="Registrar Primera Consulta"
              @click="router.visit(route('historias-clinicas.create', { mascota_id: mascota.id }))"
            />
          </div>
        </div>
      </div>
    </template>
  </UDashboardPanel>
</template>
