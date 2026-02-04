<script setup lang="ts">
import { reactive, computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'
import type { Producto } from '../../types'

interface CategoriaOption {
  id: string
  nombre: string
}

const props = defineProps<{
  producto: Producto
  categorias: CategoriaOption[]
}>()

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
  categoriaId: props.producto.categoriaId,
  codigo: props.producto.codigo,
  nombre: props.producto.nombre,
  descripcion: props.producto.descripcion ?? '',
  precioUnitario: props.producto.precioUnitario,
  stock: props.producto.stock,
  tipo: props.producto.tipo ?? 'bien',
  activo: props.producto.activo ?? true
})

const categoriaOptions = computed(() =>
  props.categorias.map(c => ({
    label: c.nombre,
    value: c.id
  }))
)

const tipoOptions = [
  { label: 'Bien', value: 'bien' },
  { label: 'Servicio', value: 'servicio' }
]

const handleSubmit = () => {
  isLoading.value = true
  router.put(route('productos.update', props.producto.id), state, {
    onFinish: () => { isLoading.value = false }
  })
}
</script>

<template>
  <UDashboardPanel id="producto-edit">
    <template #header>
      <UDashboardNavbar title="Editar Producto">
        <template #leading>
          <UButton
            icon="i-lucide-arrow-left"
            color="neutral"
            variant="ghost"
            @click="router.visit(route('productos.index'))"
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
                <UIcon name="i-lucide-box" class="w-6 h-6 text-primary-500" />
              </div>
              <div>
                <h2 class="text-lg font-semibold">Editar Producto</h2>
                <p class="text-sm text-muted">Modifique la informacion del producto</p>
              </div>
            </div>
          </template>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <FormField label="Categoria" name="categoriaId" required :error="errors.categoria_id">
              <USelect
                v-model="state.categoriaId"
                :items="categoriaOptions"
                placeholder="Seleccione una categoria"
                class="w-full"
              />
            </FormField>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <FormField label="Codigo" name="codigo" required :error="errors.codigo">
                <UInput
                  v-model="state.codigo"
                  placeholder="PRD-001"
                  class="w-full"
                />
              </FormField>

              <FormField label="Nombre" name="nombre" required :error="errors.nombre">
                <UInput
                  v-model="state.nombre"
                  placeholder="Producto"
                  class="w-full"
                />
              </FormField>
            </div>

            <FormField label="Descripcion" name="descripcion" :error="errors.descripcion">
              <UTextarea
                v-model="state.descripcion"
                placeholder="Descripcion del producto"
                :rows="3"
                class="w-full"
              />
            </FormField>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <FormField label="Precio Unitario" name="precioUnitario" required :error="errors.precio_unitario">
                <UInput
                  v-model.number="state.precioUnitario"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full"
                />
              </FormField>

              <FormField label="Stock" name="stock" :error="errors.stock">
                <UInput
                  v-model.number="state.stock"
                  type="number"
                  min="0"
                  class="w-full"
                />
              </FormField>

              <FormField label="Tipo" name="tipo" :error="errors.tipo">
                <USelect
                  v-model="state.tipo"
                  :items="tipoOptions"
                  class="w-full"
                />
              </FormField>
            </div>

            <div class="flex items-center">
              <UCheckbox v-model="state.activo" label="Producto activo" />
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <UButton
                color="neutral"
                variant="outline"
                label="Cancelar"
                @click="router.visit(route('productos.index'))"
              />
              <UButton
                type="submit"
                color="primary"
                label="Actualizar Producto"
                :loading="isLoading"
              />
            </div>
          </form>
        </UCard>
      </div>
    </template>
  </UDashboardPanel>
</template>
