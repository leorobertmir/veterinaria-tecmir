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

interface Historia {
  id: string
  mascotaId: string
  citaId: string | null
  veterinarioId: string
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
}

interface Props {
  historia: Historia
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

const state = reactive({
  mascotaId: props.historia.mascotaId,
  citaId: props.historia.citaId,
  veterinarioId: props.historia.veterinarioId,
  fecha: props.historia.fecha,
  peso: props.historia.peso,
  temperatura: props.historia.temperatura,
  frecuenciaCardiaca: props.historia.frecuenciaCardiaca,
  frecuenciaRespiratoria: props.historia.frecuenciaRespiratoria,
  anamnesis: props.historia.anamnesis || '',
  examenFisico: props.historia.examenFisico || '',
  diagnostico: props.historia.diagnostico || '',
  tratamiento: props.historia.tratamiento || '',
  receta: props.historia.receta || '',
  observaciones: props.historia.observaciones || '',
  proximaVisita: props.historia.proximaVisita || ''
})

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
  router.put(route('historias-clinicas.update', props.historia.id), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="historia-edit">
    <template #header>
      <UDashboardNavbar title="Editar Historia Clinica">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('historias-clinicas.show', historia.id))"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="max-w-4xl mx-auto">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <UCard>
            <template #header>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-primary-100 dark:bg-primary-900 rounded-lg">
                  <UIcon name="i-lucide-user" class="w-6 h-6 text-primary-500" />
                </div>
                <div>
                  <h3 class="font-semibold">Datos Generales</h3>
                  <p class="text-sm text-muted">Informacion del paciente y fecha</p>
                </div>
              </div>
            </template>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                  placeholder="Seleccionar..."
                  class="w-full"
                />
              </FormField>

              <FormField label="Fecha" name="fecha" required :error="errors.fecha">
                <UInput
                  v-model="state.fecha"
                  type="date"
                  class="w-full"
                />
              </FormField>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                  <UIcon name="i-lucide-activity" class="w-6 h-6 text-green-500" />
                </div>
                <div>
                  <h3 class="font-semibold">Signos Vitales</h3>
                  <p class="text-sm text-muted">Mediciones del paciente</p>
                </div>
              </div>
            </template>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <FormField label="Peso (kg)" name="peso" :error="errors.peso">
                <UInput
                  v-model.number="state.peso"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  class="w-full"
                />
              </FormField>

              <FormField label="Temperatura (C)" name="temperatura" :error="errors.temperatura">
                <UInput
                  v-model.number="state.temperatura"
                  type="number"
                  step="0.1"
                  min="30"
                  max="45"
                  placeholder="38.5"
                  class="w-full"
                />
              </FormField>

              <FormField label="FC (bpm)" name="frecuenciaCardiaca" :error="errors.frecuencia_cardiaca">
                <UInput
                  v-model.number="state.frecuenciaCardiaca"
                  type="number"
                  min="20"
                  max="300"
                  placeholder="80"
                  class="w-full"
                />
              </FormField>

              <FormField label="FR (rpm)" name="frecuenciaRespiratoria" :error="errors.frecuencia_respiratoria">
                <UInput
                  v-model.number="state.frecuenciaRespiratoria"
                  type="number"
                  min="5"
                  max="100"
                  placeholder="20"
                  class="w-full"
                />
              </FormField>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 dark:bg-amber-900 rounded-lg">
                  <UIcon name="i-lucide-stethoscope" class="w-6 h-6 text-amber-500" />
                </div>
                <div>
                  <h3 class="font-semibold">Evaluacion Clinica</h3>
                  <p class="text-sm text-muted">Anamnesis, examen y diagnostico</p>
                </div>
              </div>
            </template>

            <div class="space-y-4">
              <FormField label="Anamnesis" name="anamnesis" :error="errors.anamnesis">
                <UTextarea
                  v-model="state.anamnesis"
                  placeholder="Motivo de consulta, sintomas reportados..."
                  :rows="3"
                  class="w-full"
                />
              </FormField>

              <FormField label="Examen Fisico" name="examenFisico" :error="errors.examen_fisico">
                <UTextarea
                  v-model="state.examenFisico"
                  placeholder="Hallazgos del examen fisico..."
                  :rows="3"
                  class="w-full"
                />
              </FormField>

              <FormField label="Diagnostico" name="diagnostico" :error="errors.diagnostico">
                <UTextarea
                  v-model="state.diagnostico"
                  placeholder="Diagnostico presuntivo o definitivo..."
                  :rows="2"
                  class="w-full"
                />
              </FormField>
            </div>
          </UCard>

          <UCard>
            <template #header>
              <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                  <UIcon name="i-lucide-pill" class="w-6 h-6 text-purple-500" />
                </div>
                <div>
                  <h3 class="font-semibold">Tratamiento</h3>
                  <p class="text-sm text-muted">Plan terapeutico y medicacion</p>
                </div>
              </div>
            </template>

            <div class="space-y-4">
              <FormField label="Tratamiento" name="tratamiento" :error="errors.tratamiento">
                <UTextarea
                  v-model="state.tratamiento"
                  placeholder="Plan de tratamiento..."
                  :rows="3"
                  class="w-full"
                />
              </FormField>

              <FormField label="Receta" name="receta" :error="errors.receta">
                <UTextarea
                  v-model="state.receta"
                  placeholder="Medicamentos prescritos..."
                  :rows="3"
                  class="w-full"
                />
              </FormField>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <FormField label="Observaciones" name="observaciones" :error="errors.observaciones">
                  <UTextarea
                    v-model="state.observaciones"
                    placeholder="Notas adicionales..."
                    :rows="2"
                    class="w-full"
                  />
                </FormField>

                <FormField label="Proxima Visita" name="proximaVisita" :error="errors.proxima_visita">
                  <UInput
                    v-model="state.proximaVisita"
                    type="date"
                    class="w-full"
                  />
                </FormField>
              </div>
            </div>
          </UCard>

          <div class="flex justify-end gap-3">
            <UButton
              color="neutral"
              variant="outline"
              label="Cancelar"
              @click="router.visit(route('historias-clinicas.show', historia.id))"
            />
            <UButton
              type="submit"
              color="primary"
              label="Actualizar Historia"
              :loading="isLoading"
            />
          </div>
        </form>
      </div>
    </template>
  </UDashboardPanel>
</template>
