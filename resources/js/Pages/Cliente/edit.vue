<script setup lang="ts">
import { reactive, computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'
import type { Cliente } from '../../types'

interface Props {
  cliente: Cliente
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
  tipoDocumento: props.cliente.tipoDocumento,
  numeroDocumento: props.cliente.numeroDocumento,
  razonSocial: props.cliente.razonSocial,
  direccion: props.cliente.direccion,
  telefono: props.cliente.telefono,
  email: props.cliente.email
})

const tiposDocumento = [
  { label: 'DNI', value: 'DNI' },
  { label: 'RUC', value: 'RUC' },
  { label: 'Carnet de Extranjeria', value: 'CE' },
  { label: 'Pasaporte', value: 'PASAPORTE' }
]

const handleSubmit = () => {
  isLoading.value = true
  router.put(route('clientes.update', props.cliente.id), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="cliente-edit">
    <template #header>
      <UDashboardNavbar title="Editar Cliente">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('clientes.index'))"
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
                <UIcon name="i-lucide-user-pen" class="w-6 h-6 text-primary-500" />
              </div>
              <div>
                <h2 class="text-lg font-semibold">Editar Datos del Cliente</h2>
                <p class="text-sm text-muted">Modifique la informacion del cliente</p>
              </div>
            </div>
          </template>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Tipo de Documento" name="tipoDocumento" required :error="errors.tipo_documento">
                <USelect
                  v-model="state.tipoDocumento"
                  :items="tiposDocumento"
                  class="w-full"
                />
              </FormField>

              <FormField label="Numero de Documento" name="numeroDocumento" required :error="errors.numero_documento">
                <UInput
                  v-model="state.numeroDocumento"
                  placeholder="12345678"
                  class="w-full"
                />
              </FormField>
            </div>

            <FormField label="Razon Social / Nombre" name="razonSocial" required :error="errors.razon_social">
              <UInput
                v-model="state.razonSocial"
                placeholder="Juan Perez Garcia"
                icon="i-lucide-user"
                class="w-full"
              />
            </FormField>

            <FormField label="Direccion" name="direccion" required :error="errors.direccion">
              <UInput
                v-model="state.direccion"
                placeholder="Av. Principal 123"
                icon="i-lucide-map-pin"
                class="w-full"
              />
            </FormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Telefono" name="telefono" required :error="errors.telefono">
                <UInput
                  v-model="state.telefono"
                  placeholder="999888777"
                  icon="i-lucide-phone"
                  class="w-full"
                />
              </FormField>

              <FormField label="Email" name="email" required :error="errors.email">
                <UInput
                  v-model="state.email"
                  type="email"
                  placeholder="correo@ejemplo.com"
                  icon="i-lucide-mail"
                  class="w-full"
                />
              </FormField>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <UButton
                color="neutral"
                variant="outline"
                label="Cancelar"
                @click="router.visit(route('clientes.index'))"
              />
              <UButton
                type="submit"
                color="primary"
                label="Actualizar Cliente"
                :loading="isLoading"
              />
            </div>
          </form>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
