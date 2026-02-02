<script setup lang="ts">
import { reactive, computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

interface ClienteOption {
  id: string
  razonSocial: string
  numeroDocumento: string
}

interface Props {
  clientes: ClienteOption[]
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
  clienteId: '',
  nombre: '',
  especie: 'perro',
  raza: '',
  color: '',
  sexo: '',
  fechaNacimiento: '',
  peso: null as number | null,
  microchip: '',
  observaciones: '',
  activo: true
})

const especieOptions = [
  { label: 'Perro', value: 'perro' },
  { label: 'Gato', value: 'gato' },
  { label: 'Ave', value: 'ave' },
  { label: 'Roedor', value: 'roedor' },
  { label: 'Reptil', value: 'reptil' },
  { label: 'Otro', value: 'otro' }
]

const sexoOptions = [
  { label: 'Macho', value: 'macho' },
  { label: 'Hembra', value: 'hembra' }
]

const clienteOptions = computed(() =>
  props.clientes.map(c => ({
    label: `${c.razonSocial} (${c.numeroDocumento})`,
    value: c.id
  }))
)

const handleSubmit = () => {
  isLoading.value = true
  router.post(route('mascotas.store'), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="mascota-create">
    <template #header>
      <UDashboardNavbar title="Nueva Mascota">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('mascotas.index'))"
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
                <UIcon name="i-lucide-paw-print" class="w-6 h-6 text-primary-500" />
              </div>
              <div>
                <h2 class="text-lg font-semibold">Registrar Mascota</h2>
                <p class="text-sm text-muted">Complete la informacion de la nueva mascota</p>
              </div>
            </div>
          </template>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <FormField label="Propietario" name="clienteId" required :error="errors.cliente_id">
              <USelect
                v-model="state.clienteId"
                :items="clienteOptions"
                placeholder="Seleccionar propietario..."
                searchable
                class="w-full"
              />
            </FormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Nombre" name="nombre" required :error="errors.nombre">
                <UInput
                  v-model="state.nombre"
                  placeholder="Max"
                  icon="i-lucide-paw-print"
                  class="w-full"
                />
              </FormField>

              <FormField label="Especie" name="especie" required :error="errors.especie">
                <USelect
                  v-model="state.especie"
                  :items="especieOptions"
                  class="w-full"
                />
              </FormField>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Raza" name="raza" :error="errors.raza">
                <UInput
                  v-model="state.raza"
                  placeholder="Labrador"
                  class="w-full"
                />
              </FormField>

              <FormField label="Color" name="color" :error="errors.color">
                <UInput
                  v-model="state.color"
                  placeholder="Dorado"
                  class="w-full"
                />
              </FormField>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <FormField label="Sexo" name="sexo" :error="errors.sexo">
                <USelect
                  placeholder="Seleccione Sexo"
                  v-model="state.sexo"
                  :items="sexoOptions"
                  class="w-full"
                />
              </FormField>

              <FormField label="Fecha de Nacimiento" name="fechaNacimiento" :error="errors.fecha_nacimiento">
                <UInput
                  v-model="state.fechaNacimiento"
                  type="date"
                  class="w-full"
                />
              </FormField>

              <FormField label="Peso (kg)" name="peso" :error="errors.peso">
                <UInput
                  v-model.number="state.peso"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="12.5"
                  class="w-full"
                />
              </FormField>
            </div>

            <FormField label="Microchip" name="microchip" :error="errors.microchip">
              <UInput
                v-model="state.microchip"
                placeholder="Numero de microchip"
                icon="i-lucide-cpu"
                class="w-full"
              />
            </FormField>

            <FormField label="Observaciones" name="observaciones" :error="errors.observaciones">
              <UTextarea
                v-model="state.observaciones"
                placeholder="Observaciones adicionales..."
                :rows="3"
                class="w-full"
              />
            </FormField>

            <div class="flex justify-end gap-3 pt-4">
              <UButton
                color="neutral"
                variant="outline"
                label="Cancelar"
                @click="router.visit(route('mascotas.index'))"
              />
              <UButton
                type="submit"
                color="primary"
                label="Guardar Mascota"
                :loading="isLoading"
              />
            </div>
          </form>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
