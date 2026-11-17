-- Motor de tiendas de los ejemplos por rubro (SQLite).
-- Montos en pesos chilenos (enteros). Fechas en segundos Unix.

CREATE TABLE meta (
  clave TEXT PRIMARY KEY,
  valor TEXT NOT NULL
);

CREATE TABLE usuarios (
  id INTEGER PRIMARY KEY,
  email TEXT NOT NULL UNIQUE COLLATE NOCASE,
  nombre TEXT NOT NULL,
  telefono TEXT NOT NULL DEFAULT '',
  clave_hash TEXT NOT NULL,
  rol TEXT NOT NULL DEFAULT 'cliente' CHECK (rol IN ('cliente', 'dueno')),
  tipo TEXT NOT NULL DEFAULT 'particular' CHECK (tipo IN ('particular', 'mayorista')),
  estado TEXT NOT NULL DEFAULT 'activo' CHECK (estado IN ('activo', 'pendiente', 'rechazado')),
  empresa TEXT NOT NULL DEFAULT '',
  creado_en INTEGER NOT NULL
);

CREATE TABLE direcciones (
  id INTEGER PRIMARY KEY,
  usuario_id INTEGER NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE,
  etiqueta TEXT NOT NULL,
  calle TEXT NOT NULL,
  comuna TEXT NOT NULL,
  referencia TEXT NOT NULL DEFAULT ''
);
CREATE INDEX direcciones_usuario ON direcciones (usuario_id);

CREATE TABLE categorias (
  id INTEGER PRIMARY KEY,
  slug TEXT NOT NULL UNIQUE,
  nombre TEXT NOT NULL,
  orden INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE productos (
  id INTEGER PRIMARY KEY,
  categoria_id INTEGER NOT NULL REFERENCES categorias (id),
  nombre TEXT NOT NULL,
  descripcion TEXT NOT NULL DEFAULT '',
  precio INTEGER NOT NULL CHECK (precio >= 0),
  precio_mayorista INTEGER CHECK (precio_mayorista IS NULL OR precio_mayorista >= 0),
  stock INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0),
  foto TEXT NOT NULL DEFAULT '',
  modelo3d TEXT NOT NULL DEFAULT '',
  visible INTEGER NOT NULL DEFAULT 1,
  destacado INTEGER NOT NULL DEFAULT 0,
  solo_mayorista INTEGER NOT NULL DEFAULT 0,
  orden INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX productos_categoria ON productos (categoria_id);

CREATE TABLE pedidos (
  id INTEGER PRIMARY KEY,
  codigo TEXT NOT NULL UNIQUE,
  usuario_id INTEGER NOT NULL REFERENCES usuarios (id),
  tipo_cliente TEXT NOT NULL,
  entrega TEXT NOT NULL CHECK (entrega IN ('retiro', 'despacho')),
  comuna TEXT NOT NULL DEFAULT '',
  direccion TEXT NOT NULL DEFAULT '',
  cuando TEXT NOT NULL DEFAULT '',
  nota TEXT NOT NULL DEFAULT '',
  subtotal INTEGER NOT NULL,
  costo_despacho INTEGER NOT NULL DEFAULT 0,
  total INTEGER NOT NULL,
  pago TEXT NOT NULL CHECK (pago IN ('prueba', 'al_recibir')),
  pagado INTEGER NOT NULL DEFAULT 0,
  estado TEXT NOT NULL DEFAULT 'nuevo' CHECK (estado IN ('nuevo', 'preparando', 'listo', 'entregado', 'cancelado')),
  creado_en INTEGER NOT NULL,
  actualizado_en INTEGER NOT NULL
);
CREATE INDEX pedidos_usuario ON pedidos (usuario_id);
CREATE INDEX pedidos_fecha ON pedidos (creado_en);

CREATE TABLE pedido_items (
  id INTEGER PRIMARY KEY,
  pedido_id INTEGER NOT NULL REFERENCES pedidos (id) ON DELETE CASCADE,
  producto_id INTEGER NOT NULL,
  nombre TEXT NOT NULL,
  precio_unitario INTEGER NOT NULL,
  cantidad INTEGER NOT NULL CHECK (cantidad > 0)
);
CREATE INDEX pedido_items_pedido ON pedido_items (pedido_id);

-- Límite de intentos por IP (registro, inicio de sesión). La IP se guarda como hash.
CREATE TABLE limites (
  id INTEGER PRIMARY KEY,
  clave TEXT NOT NULL,
  accion TEXT NOT NULL,
  creado_en INTEGER NOT NULL
);
CREATE INDEX limites_busqueda ON limites (accion, clave, creado_en);
