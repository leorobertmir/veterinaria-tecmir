<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { format } from 'date-fns'
import { es } from 'date-fns/locale'

interface Cita {
  id: string
  mascotaId: string
  veterinarioId: string
  fechaHora: string
  fechaHoraFin: string | null
  motivo: string
  estado: string
  tipoConsulta: string
  observaciones: string | null
  tieneHistoriaClinica: boolean
  mascota: {
    id: string
    nombre: string
    especie: string
    raza: string | null
    cliente: {
      id: string
      razonSocial: string
      telefono: string
      email: string
    } | null
  } | null
  veterinario: {
    id: string
    name: string
  } | null
}

interface Props {
  cita: Cita
}

const props = defineProps<Props>()

const formatFechaHora = (fechaHora: string) => {
  return format(new Date(fechaHora), "EEEE d 'de' MMMM 'de' yyyy 'a las' HH:mm", { locale: es })
}

const getEstadoColor = (estado: string) => {
  const colors: Record<string, 'primary' | 'success' | 'warning' | 'error' | 'neutral'> = {
    programada: 'primary',
    confirmada: 'success',
    en_curso: 'warning',
    completada: 'success',
    cancelada: 'error',
    no_asistio: 'neutral'
  }
  return colors[estado] || 'neutral'
}

const getTipoConsultaLabel = (tipo: string) => {
  const labels: Record<string, string> = {
    consulta_general: 'Consulta General',
    vacunacion: 'Vacunacion',
    cirugia: 'Cirugia',
    emergencia: 'Emergencia',
    control: 'Control',
    peluqueria: 'Peluqueria',
    desparasitacion: 'Desparasitacion'
  }
  return labels[tipo] || tipo
}

const cambiarEstado = (nuevoEstado: string) => {
  router.patch(route('citas.update-estado', props.cita.id), { estado: nuevoEstado })
}
</script>

<template>
  <UDashboardPanel id="cita-show">
    <template #header>
      <UDashboardNavbar title="Detalle de Cita">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('citas.index'))"
          />
        </template>

        <template #right>
          <UButton
            v-if="!cita.tieneHistoriaClinica && (cita.estado === 'confirmada' || cita.estado === 'en_curso')"
            color="primary"
            icon="i-lucide-file-plus"
            label="Crear Historia Clinica"
            @click="router.visit(route('historias-clinicas.create', { cita_id: cita.id }))"
          />
          <UButton
            color="neutral"
            variant="outline"
            icon="i-lucide-pencil"
            label="Editar"
            @click="router.visit(route('citas.edit', cita.id))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-4xl mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <UCard>
            <template #header>
              <div class="flex items-center justify-between">
                <h3 class="font-semibold">Informacion de la Cita</h3>
                <UBadge :color="getEstadoColor(cita.estado)" size="lg">
                  {{ cita.estado }}
                </UBadge>
              </div>
            </template>

            <div class="space-y-4">
              <div>
                <p class="text-sm text-muted">Fecha y Hora</p>
                <p class="font-medium capitalize">{{ formatFechaHora(cita.fechaHora) }}</p>
              </div>

              <div>
                <p class="text-sm text-muted">Tipo de Consulta</p>
                <UBadge variant="subtle" color="neutral">
                  {{ getTipoConsultaLabel(cita.tipoConsulta) }}
                </UBadge>
              </div>

              <div>
                <p class="text-sm text-muted">Motivo</p>
                <p>{{ cita.motivo }}</p>
              </div>

              <div v-if="cita.observaciones">
                <p class="text-sm text-muted">Observaciones</p>
                <p>{{ cita.observaciones }}</p>
              </div>

              <div v-if="cita.veterinario">
                <p class="text-sm text-muted">Veterinario</p>
                <p class="font-medium">{{ cita.veterinario.name }}</p>
              </div>
            </div>

            <template #footer>
              <div class="flex flex-wrap gap-2">
                <UButton
                  v-if="cita.estado === 'programada'"
                  size="sm"
                  color="success"
                  variant="outline"
                  label="Confirmar"
                  @click="cambiarEstado('confirmada')"
                />
                <UButton
                  v-if="cita.estado === 'confirmada'"
                  size="sm"
                  color="warning"
                  variant="outline"
                  label="Iniciar"
                  @click="cambiarEstado('en_curso')"
                />
                <UButton
                  v-if="cita.estado === 'en_curso'"
                  size="sm"
                  color="success"
                  variant="outline"
                  label="Completar"
                  @click="cambiarEstado('completada')"
                />
                <UButton
                  v-if="['programada', 'confirmada'].includes(cita.estado)"
                  size="sm"
                  color="error"
                  variant="outline"
                  label="Cancelar"
                  @click="cambiarEstado('cancelada')"
                />
              </div>
            </template>
          </UCard>

          <UCard v-if="cita.mascota">
            <template #header>
              <h3 class="font-semibold">Paciente</h3>
            </template>

            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-lg">
                  <UIcon name="i-lucide-paw-print" class="w-8 h-8 text-primary-500" />
                </div>
                <div>
                  <p class="text-lg font-semibold">{{ cita.mascota.nombre }}</p>
                  <p class="text-muted">{{ cita.mascota.especie }} {{ cita.mascota.raza ? `- ${cita.mascota.raza}` : '' }}</p>
                </div>
              </div>

              <UButton
                block
                variant="outline"
                icon="i-lucide-file-text"
                label="Ver Historial Clinico"
                @click="router.visit(route('historias-clinicas.por-mascota', cita.mascota.id))"
              />
            </div>
          </UCard>
        </div>

        <UCard v-if="cita.mascota?.cliente">
          <template #header>
            <h3 class="font-semibold">Propietario</h3>
          </template>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-muted">Nombre</p>
              <p class="font-medium">{{ cita.mascota.cliente.razonSocial }}</p>
            </div>

            <div>
              <p class="text-sm text-muted">Telefono</p>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-phone" class="w-4 h-4 text-muted" />
                <p>{{ cita.mascota.cliente.telefono }}</p>
              </div>
            </div>

            <div>
              <p class="text-sm text-muted">Email</p>
              <div class="flex items-center gap-2">
                <UIcon name="i-lucide-mail" class="w-4 h-4 text-muted" />
                <p>{{ cita.mascota.cliente.email }}</p>
              </div>
            </div>
          </div>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
