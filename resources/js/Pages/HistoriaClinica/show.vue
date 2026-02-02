<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

interface Historia {
  id: string
  mascotaId: string
  fecha: string
  peso: number | null
  temperatura: number | null
  frecuenciaCardiaca: number | null
  frecuenciaRespiratoria: number | null
  anamnesis: string | null
  examenFisico: string | null
  diagnostico: string | null
  tratamiento: string | null
  receta: string | null
  observaciones: string | null
  proximaVisita: string | null
  mascota: {
    id: string
    nombre: string
    especie: string
    raza: string | null
    sexo: string | null
    fechaNacimiento: string | null
    cliente: {
      id: string
      razonSocial: string
      telefono: string
      email: string
    } | null
  } | null
  veterinario: { id: string; name: string } | null
  cita: {
    id: string
    fechaHora: string
    tipoConsulta: string
    motivo: string
  } | null
}

interface HistorialPrevio {
  id: string
  fecha: string
  diagnostico: string | null
}

interface Props {
  historia: Historia
  historialPrevio: HistorialPrevio[]
}

const props = defineProps<Props>()

const formatFecha = (fecha: string) => {
  return format(new Date(fecha), "d 'de' MMMM 'de' yyyy", { locale: es })
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
  <UDashboardPanel id="historia-show">
    <template #header>
      <UDashboardNavbar title="Historia Clinica">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('historias-clinicas.index'))"
          />
        </template>

        <template #right>
          <UButton
            color="neutral"
            variant="outline"
            icon="i-lucide-printer"
            label="Imprimir"
            @click="window.print()"
          />
          <UButton
            color="primary"
            icon="i-lucide-pencil"
            label="Editar"
            @click="router.visit(route('historias-clinicas.edit', historia.id))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-5xl mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <UCard class="md:col-span-2">
            <template #header>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
                    <UIcon name="i-lucide-paw-print" class="w-6 h-6 text-primary-500" />
                  </div>
                  <div>
                    <h2 class="text-lg font-semibold">{{ historia.mascota?.nombre }}</h2>
                    <p class="text-sm text-muted">
                      {{ historia.mascota?.especie }}
                      {{ historia.mascota?.raza ? ` - ${historia.mascota.raza}` : '' }}
                    </p>
                  </div>
                </div>
                <UBadge color="primary" size="lg">
                  {{ formatFecha(historia.fecha) }}
                </UBadge>
              </div>
            </template>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
              <div v-if="historia.mascota?.sexo" class="text-center p-3 bg-muted/30 rounded-lg">
                <p class="text-xs text-muted">Sexo</p>
                <p class="font-medium capitalize">{{ historia.mascota.sexo }}</p>
              </div>
              <div v-if="historia.mascota?.fechaNacimiento" class="text-center p-3 bg-muted/30 rounded-lg">
                <p class="text-xs text-muted">Edad</p>
                <p class="font-medium">{{ calcularEdad(historia.mascota.fechaNacimiento) }}</p>
              </div>
              <div v-if="historia.veterinario" class="text-center p-3 bg-muted/30 rounded-lg">
                <p class="text-xs text-muted">Veterinario</p>
                <p class="font-medium">{{ historia.veterinario.name }}</p>
              </div>
              <div v-if="historia.cita" class="text-center p-3 bg-muted/30 rounded-lg">
                <p class="text-xs text-muted">Tipo Consulta</p>
                <p class="font-medium capitalize">{{ historia.cita.tipoConsulta.replace('_', ' ') }}</p>
              </div>
            </div>

            <UButton
              v-if="historia.mascota"
              block
              variant="outline"
              icon="i-lucide-file-text"
              label="Ver Historial Completo"
              @click="router.visit(route('historias-clinicas.por-mascota', historia.mascota.id))"
            />
          </UCard>

          <UCard v-if="historia.mascota?.cliente">
            <template #header>
              <h3 class="font-semibold">Propietario</h3>
            </template>

            <div class="space-y-3">
              <div>
                <p class="text-sm text-muted">Nombre</p>
                <p class="font-medium">{{ historia.mascota.cliente.razonSocial }}</p>
              </div>
              <div>
                <p class="text-sm text-muted">Telefono</p>
                <div class="flex items-center gap-2">
                  <UIcon name="i-lucide-phone" class="w-4 h-4 text-muted" />
                  <p>{{ historia.mascota.cliente.telefono }}</p>
                </div>
              </div>
              <div>
                <p class="text-sm text-muted">Email</p>
                <div class="flex items-center gap-2">
                  <UIcon name="i-lucide-mail" class="w-4 h-4 text-muted" />
                  <p class="text-sm">{{ historia.mascota.cliente.email }}</p>
                </div>
              </div>
            </div>
          </UCard>
        </div>

        <UCard v-if="historia.peso || historia.temperatura || historia.frecuenciaCardiaca || historia.frecuenciaRespiratoria">
          <template #header>
            <div class="flex items-center gap-2">
              <UIcon name="i-lucide-activity" class="w-5 h-5 text-green-500" />
              <h3 class="font-semibold">Signos Vitales</h3>
            </div>
          </template>

          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div v-if="historia.peso" class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
              <UIcon name="i-lucide-scale" class="w-8 h-8 text-green-500 mx-auto mb-2" />
              <p class="text-2xl font-bold">{{ historia.peso }}</p>
              <p class="text-sm text-muted">kg</p>
            </div>

            <div v-if="historia.temperatura" class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
              <UIcon name="i-lucide-thermometer" class="w-8 h-8 text-red-500 mx-auto mb-2" />
              <p class="text-2xl font-bold">{{ historia.temperatura }}</p>
              <p class="text-sm text-muted">°C</p>
            </div>

            <div v-if="historia.frecuenciaCardiaca" class="text-center p-4 bg-pink-50 dark:bg-pink-900/20 rounded-lg">
              <UIcon name="i-lucide-heart-pulse" class="w-8 h-8 text-pink-500 mx-auto mb-2" />
              <p class="text-2xl font-bold">{{ historia.frecuenciaCardiaca }}</p>
              <p class="text-sm text-muted">bpm</p>
            </div>

            <div v-if="historia.frecuenciaRespiratoria" class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <UIcon name="i-lucide-wind" class="w-8 h-8 text-blue-500 mx-auto mb-2" />
              <p class="text-2xl font-bold">{{ historia.frecuenciaRespiratoria }}</p>
              <p class="text-sm text-muted">rpm</p>
            </div>
          </div>
        </UCard>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <UCard v-if="historia.anamnesis">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-message-circle" class="w-5 h-5 text-amber-500" />
                <h3 class="font-semibold">Anamnesis</h3>
              </div>
            </template>
            <p class="whitespace-pre-wrap">{{ historia.anamnesis }}</p>
          </UCard>

          <UCard v-if="historia.examenFisico">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-stethoscope" class="w-5 h-5 text-blue-500" />
                <h3 class="font-semibold">Examen Fisico</h3>
              </div>
            </template>
            <p class="whitespace-pre-wrap">{{ historia.examenFisico }}</p>
          </UCard>
        </div>

        <UCard v-if="historia.diagnostico">
          <template #header>
            <div class="flex items-center gap-2">
              <UIcon name="i-lucide-clipboard-check" class="w-5 h-5 text-purple-500" />
              <h3 class="font-semibold">Diagnostico</h3>
            </div>
          </template>
          <p class="whitespace-pre-wrap text-lg">{{ historia.diagnostico }}</p>
        </UCard>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <UCard v-if="historia.tratamiento">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-syringe" class="w-5 h-5 text-green-500" />
                <h3 class="font-semibold">Tratamiento</h3>
              </div>
            </template>
            <p class="whitespace-pre-wrap">{{ historia.tratamiento }}</p>
          </UCard>

          <UCard v-if="historia.receta">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-pill" class="w-5 h-5 text-orange-500" />
                <h3 class="font-semibold">Receta</h3>
              </div>
            </template>
            <p class="whitespace-pre-wrap">{{ historia.receta }}</p>
          </UCard>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <UCard v-if="historia.observaciones">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-file-text" class="w-5 h-5 text-gray-500" />
                <h3 class="font-semibold">Observaciones</h3>
              </div>
            </template>
            <p class="whitespace-pre-wrap">{{ historia.observaciones }}</p>
          </UCard>

          <UCard v-if="historia.proximaVisita">
            <template #header>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-calendar-clock" class="w-5 h-5 text-primary-500" />
                <h3 class="font-semibold">Proxima Visita</h3>
              </div>
            </template>
            <p class="text-lg font-medium">{{ formatFecha(historia.proximaVisita) }}</p>
          </UCard>
        </div>

        <UCard v-if="historialPrevio.length > 0">
          <template #header>
            <div class="flex items-center gap-2">
              <UIcon name="i-lucide-history" class="w-5 h-5 text-muted" />
              <h3 class="font-semibold">Historial Previo</h3>
            </div>
          </template>

          <div class="space-y-2">
            <div
              v-for="h in historialPrevio"
              :key="h.id"
              class="flex items-center justify-between p-3 bg-muted/30 rounded-lg cursor-pointer hover:bg-muted/50 transition-colors"
              @click="router.visit(route('historias-clinicas.show', h.id))"
            >
              <div>
                <p class="font-medium">{{ formatFecha(h.fecha) }}</p>
                <p class="text-sm text-muted line-clamp-1">{{ h.diagnostico || 'Sin diagnostico' }}</p>
              </div>
              <UIcon name="i-lucide-chevron-right" class="w-5 h-5 text-muted" />
            </div>
          </div>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
