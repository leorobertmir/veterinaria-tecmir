<script setup lang="ts">
import { reactive, computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

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
}

interface MascotaOption {
  id: string
  nombre: string
  especie: string
  cliente: { id: string; razonSocial: string } | null
}

interface VeterinarioOption {
  id: string
  name: string
}

interface Props {
  cita: Cita
  mascotas: MascotaOption[]
  veterinarios: VeterinarioOption[]
}

const props = defineProps<Props>()
const page = usePage()
const backendErrors = computed(() => page.props.errors || {})
const errors = computed(() => {
  const result: Record<string, string> = {}
  Object.keys(backendErrors.value).forEach(key => {
    const error = backendErrors.value[key]
    result[key] = Array.isArray(error) ? error[0] : error
  })
  return result
})

const isLoading = ref(false)

const formatDatetimeLocal = (datetime: string | null) => {
  if (!datetime) return ''
  return datetime.replace(' ', 'T').slice(0, 16)
}

const state = reactive({
  mascotaId: props.cita.mascotaId,
  veterinarioId: props.cita.veterinarioId,
  fechaHora: formatDatetimeLocal(props.cita.fechaHora),
  fechaHoraFin: formatDatetimeLocal(props.cita.fechaHoraFin),
  motivo: props.cita.motivo,
  estado: props.cita.estado,
  tipoConsulta: props.cita.tipoConsulta,
  observaciones: props.cita.observaciones || ''
})

const tipoConsultaOptions = [
  { label: 'Consulta General', value: 'consulta_general' },
  { label: 'Vacunacion', value: 'vacunacion' },
  { label: 'Cirugia', value: 'cirugia' },
  { label: 'Emergencia', value: 'emergencia' },
  { label: 'Control', value: 'control' },
  { label: 'Peluqueria', value: 'peluqueria' },
  { label: 'Desparasitacion', value: 'desparasitacion' }
]

const estadoOptions = [
  { label: 'Programada', value: 'programada' },
  { label: 'Confirmada', value: 'confirmada' },
  { label: 'En curso', value: 'en_curso' },
  { label: 'Completada', value: 'completada' },
  { label: 'Cancelada', value: 'cancelada' },
  { label: 'No asistio', value: 'no_asistio' }
]

const mascotaOptions = computed(() =>
  props.mascotas.map(m => ({
    label: `${m.nombre} (${m.especie}) - ${m.cliente?.razonSocial || 'Sin propietario'}`,
    value: m.id
  }))
)

const veterinarioOptions = computed(() =>
  props.veterinarios.map(v => ({
    label: v.name,
    value: v.id
  }))
)

const handleSubmit = () => {
  isLoading.value = true
  router.put(route('citas.update', props.cita.id), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="cita-edit">
    <template #header>
      <UDashboardNavbar title="Editar Cita">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('citas.index'))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-2xl mx-auto">
        <UCard>
          <template #header>
            <div class="flex items-center gap-3">
              <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
                <UIcon name="i-lucide-calendar-check" class="w-6 h-6 text-primary-500" />
              </div>
              <div>
                <h2 class="text-lg font-semibold">Editar Cita</h2>
                <p class="text-sm text-muted">Modifique la informacion de la cita</p>
              </div>
            </div>
          </template>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <FormField label="Mascota" name="mascotaId" required :error="errors.mascota_id">
              <USelect
                v-model="state.mascotaId"
                :items="mascotaOptions"
                placeholder="Seleccionar mascota..."
                searchable
                class="w-full"
              />
            </FormField>

            <FormField label="Veterinario" name="veterinarioId" required :error="errors.veterinario_id">
              <USelect
                v-model="state.veterinarioId"
                :items="veterinarioOptions"
                placeholder="Seleccionar veterinario..."
                class="w-full"
              />
            </FormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Fecha y Hora" name="fechaHora" required :error="errors.fecha_hora">
                <UInput
                  v-model="state.fechaHora"
                  type="datetime-local"
                  class="w-full"
                />
              </FormField>

              <FormField label="Hora de Fin (opcional)" name="fechaHoraFin" :error="errors.fecha_hora_fin">
                <UInput
                  v-model="state.fechaHoraFin"
                  type="datetime-local"
                  class="w-full"
                />
              </FormField>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Tipo de Consulta" name="tipoConsulta" required :error="errors.tipo_consulta">
                <USelect
                  v-model="state.tipoConsulta"
                  :items="tipoConsultaOptions"
                  class="w-full"
                />
              </FormField>

              <FormField label="Estado" name="estado" required :error="errors.estado">
                <USelect
                  v-model="state.estado"
                  :items="estadoOptions"
                  class="w-full"
                />
              </FormField>
            </div>

            <FormField label="Motivo de la Cita" name="motivo" required :error="errors.motivo">
              <UTextarea
                v-model="state.motivo"
                placeholder="Describa el motivo de la consulta..."
                :rows="3"
                class="w-full"
              />
            </FormField>

            <FormField label="Observaciones" name="observaciones" :error="errors.observaciones">
              <UTextarea
                v-model="state.observaciones"
                placeholder="Observaciones adicionales..."
                :rows="2"
                class="w-full"
              />
            </FormField>

            <div class="flex justify-end gap-3 pt-4">
              <UButton
                color="neutral"
                variant="outline"
                label="Cancelar"
                @click="router.visit(route('citas.index'))"
              />
              <UButton
                type="submit"
                color="primary"
                label="Actualizar Cita"
                :loading="isLoading"
              />
            </div>
          </form>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
