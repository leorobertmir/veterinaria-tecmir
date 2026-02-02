<script setup lang="ts">
import { reactive, computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

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
  mascotas: MascotaOption[]
  veterinarios: VeterinarioOption[]
  selectedMascotaId?: string | null
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

const now = new Date()
const state = reactive({
  mascotaId: props.selectedMascotaId || '',
  veterinarioId: '',
  fechaHora: now.toISOString().slice(0, 16),
  fechaHoraFin: '',
  motivo: '',
  tipoConsulta: 'consulta_general',
  observaciones: ''
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
  router.post(route('citas.store'), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="cita-create">
    <template #header>
      <UDashboardNavbar title="Nueva Cita">
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
                <UIcon name="i-lucide-calendar-plus" class="w-6 h-6 text-primary-500" />
              </div>
              <div>
                <h2 class="text-lg font-semibold">Programar Cita</h2>
                <p class="text-sm text-muted">Complete la informacion de la cita</p>
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

            <FormField label="Tipo de Consulta" name="tipoConsulta" required :error="errors.tipo_consulta">
              <USelect
                v-model="state.tipoConsulta"
                :items="tipoConsultaOptions"
                class="w-full"
              />
            </FormField>

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
                label="Programar Cita"
                :loading="isLoading"
              />
            </div>
          </form>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
