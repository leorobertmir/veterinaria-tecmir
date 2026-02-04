import type { AvatarProps } from '@nuxt/ui'

export type UserStatus = 'subscribed' | 'unsubscribed' | 'bounced'
export type SaleStatus = 'paid' | 'failed' | 'refunded'

export interface User {
  id: number
  name: string
  email: string
  avatar?: AvatarProps
  status: UserStatus
  location: string
}

export interface Mail {
  id: number
  unread?: boolean
  from: User
  subject: string
  body: string
  date: string
}

export interface Member {
  name: string
  username: string
  role: 'member' | 'owner'
  avatar: AvatarProps
}

export interface Stat {
  title: string
  icon: string
  value: number | string
  variation: number
  formatter?: (value: number) => string
}

export interface Sale {
  id: string
  date: string
  status: SaleStatus
  email: string
  amount: number
}

export interface Notification {
  id: number
  unread?: boolean
  sender: User
  body: string
  date: string
}

export type Period = 'daily' | 'weekly' | 'monthly'

export interface Range {
  start: Date
  end: Date
}

export interface Cliente {
  id: string
  tipoDocumento: string
  numeroDocumento: string
  razonSocial: string
  direccion: string
  telefono: string
  email: string
  createdAt: string
  updatedAt: string
}

export type FacturaEstado = 'emitida' | 'pagada' | 'anulada'

export interface Producto {
  id: string
  codigo: string
  nombre: string
  descripcion: string | null
  precioUnitario: number
  stock: number
  categoriaId: string
  tipo?: 'bien' | 'servicio'
  activo?: boolean
  createdAt: string
  updatedAt: string
}

export interface DetalleFactura {
  productoId: string
  cantidad: number
  precioUnitario: number
  descuento: number
  subtotal: number
}

export interface Factura {
  id: string
  numeroFactura: string
  serie: string
  clienteId: string
  usuarioId: string
  fechaEmision: string
  fechaVencimiento: string | null
  subtotal: number
  igv: number
  descuento: number
  total: number
  estado: FacturaEstado
  observaciones: string | null
  detalles?: DetalleFactura[]
  createdAt: string
  updatedAt: string
}

export type Especie = 'perro' | 'gato' | 'ave' | 'roedor' | 'reptil' | 'otro'
export type Sexo = 'macho' | 'hembra'
export type CitaEstado = 'programada' | 'confirmada' | 'en_curso' | 'completada' | 'cancelada' | 'no_asistio'
export type TipoConsulta = 'consulta_general' | 'vacunacion' | 'cirugia' | 'emergencia' | 'control' | 'peluqueria' | 'desparasitacion'

export interface Mascota {
  id: string
  clienteId: string
  nombre: string
  especie: Especie
  raza: string | null
  color: string | null
  sexo: Sexo | null
  fechaNacimiento: string | null
  peso: number | null
  microchip: string | null
  observaciones: string | null
  activo: boolean
  cliente?: Cliente
  createdAt: string
  updatedAt: string
}

export interface Cita {
  id: string
  mascotaId: string
  veterinarioId: string
  fechaHora: string
  fechaHoraFin: string | null
  motivo: string
  estado: CitaEstado
  tipoConsulta: TipoConsulta
  observaciones: string | null
  mascota?: Mascota
  veterinario?: { id: string; name: string }
  createdAt: string
  updatedAt: string
}

export interface HistoriaClinica {
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
  mascota?: Mascota
  veterinario?: { id: string; name: string }
  cita?: Cita
  createdAt: string
  updatedAt: string
}
