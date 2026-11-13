# 🚀 Guía de Migración y Despliegue en Linux Debian con MariaDB y DBeaver
**GymManagerFit — Versión PHP + MariaDB + HTML/CSS/Vanilla JS**

Esta guía contiene el paso a paso exacto para configurar tu máquina virtual con **Linux Debian**, permitir la conexión remota con **DBeaver**, transferir los archivos con **WinSCP** y poner a funcionar la aplicación completa mediante la dirección IP de tu servidor.

---

## 📋 Resumen de la Arquitectura
* **Servidor Web:** Apache 2 + PHP 8.x
* **Base de Datos:** MariaDB Server
* **Gestor de Base de Datos:** DBeaver (conectado por red local mediante la IP de Debian)
* **Transferencia de Archivos:** WinSCP (SFTP/SSH)
* **Frontend:** HTML5 + CSS3 (Dark Theme idéntico) + Vanilla JavaScript (QR Code con `qrcode.js` y `html5-qrcode`)
* **Backend:** PHP nativo con arquitectura API JSON y conexión mediante PDO

---

## 🛠️ PASO 1: Instalación de Paquetes en Debian Linux

Abre la terminal de tu máquina virtual Debian y ejecuta los siguientes comandos con privilegios `sudo`:

```bash
# 1. Actualizar repositorios del sistema
sudo apt update && sudo apt upgrade -y

# 2. Instalar Apache, MariaDB y PHP con sus extensiones necesarias
sudo apt install -y apache2 mariadb-server php libapache2-mod-php php-mysql php-curl php-json php-mbstring php-xml ufw

# 3. Asegurar que los servicios inicien automáticamente al encender la máquina
sudo systemctl enable apache2
sudo systemctl enable mariadb
sudo systemctl start apache2
sudo systemctl start mariadb
```

---

## 🗄️ PASO 2: Configurar MariaDB para Conexión Remota desde DBeaver

Por defecto, MariaDB en Debian solo escucha en `127.0.0.1` (localhost). Para poder conectarte desde **DBeaver** en tu computadora física usando la IP de la máquina virtual, realiza lo siguiente:

### 2.1 Permitir conexiones externas en el archivo de configuración
Edita el archivo de configuración de MariaDB:
```bash
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```
Busca la línea:
```ini
bind-address = 127.0.0.1
```
Cámbiala por:
```ini
bind-address = 0.0.0.0
```
*(Guarda los cambios presionando `Ctrl + O`, luego `Enter`, y sal con `Ctrl + X`)*.

### 2.2 Crear la base de datos y el usuario remoto para DBeaver
Entra a la consola de MariaDB:
```bash
sudo mysql
```

Ejecuta las siguientes instrucciones SQL dentro de MariaDB:
```sql
-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS gymfit_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Crear el usuario administrador con acceso remoto (%)
-- Nota: Puedes cambiar 'TuPasswordSegura123!' por la contraseña que prefieras
CREATE USER 'gymfit_admin'@'%' IDENTIFIED BY 'TuPasswordSegura123!';

-- 3. Otorgar todos los privilegios sobre la base de datos
GRANT ALL PRIVILEGES ON gymfit_db.* TO 'gymfit_admin'@'%';

-- 4. Aplicar cambios y salir
FLUSH PRIVILEGES;
EXIT;
```

### 2.3 Reiniciar MariaDB y habilitar los puertos en el Firewall
```bash
# Reiniciar servicio para aplicar el bind-address 0.0.0.0
sudo systemctl restart mariadb

# Abrir el puerto HTTP (80) y MariaDB (3306) en el firewall
sudo ufw allow 80/tcp
sudo ufw allow 3306/tcp
sudo ufw allow 22/tcp
sudo ufw --force enable
```

### 2.4 Averiguar la dirección IP de tu máquina virtual Debian
Ejecuta:
```bash
hostname -I
```
*Anota la IP resultante (por ejemplo: `192.168.1.50`).*

---

## 🐬 PASO 3: Conexión y Carga de Tablas con DBeaver

1. Abre **DBeaver** en tu computadora principal.
2. Haz clic en **Nueva Conexión** (ícono del enchufe arriba a la izquierda).
3. Selecciona **MariaDB** (o **MySQL**) y haz clic en *Siguiente*.
4. Rellena los datos de conexión:
   * **Host / Servidor:** La IP de tu máquina virtual (Ej: `192.168.1.50`)
   * **Puerto:** `3306`
   * **Base de datos:** `gymfit_db`
   * **Usuario:** `gymfit_admin`
   * **Contraseña:** `TuPasswordSegura123!`
5. Haz clic en **Probar conexión** (Test Connection). Si todo está correcto, dirá *Connected / Exitoso*.
6. Haz clic en **Finalizar**.
7. En el menú superior de DBeaver: **SQL Editor** → **New SQL Script**.
8. Abre y copia todo el contenido del archivo [`database.sql`](file:///c:/Users/ricar/OneDrive/Desktop/Gim_manager/php_version/database.sql) incluido en esta carpeta.
9. Pégalo en el editor y presiona **Alt + X** (Ejecutar script completo).
10. ¡Listo! Se habrán creado las tablas `usuarios`, `clientes`, `membresias`, `pagos`, `asistencias`, `auditoria` con sus **Triggers automáticos** y datos de prueba.

---

## 📁 PASO 4: Subir los Archivos con WinSCP a Debian

1. Abre **WinSCP** en tu computadora.
2. Configura una nueva sesión:
   * **Protocolo de archivo:** `SFTP` (o `SCP`)
   * **Nombre de host:** La IP de tu Debian (Ej: `192.168.1.50`)
   * **Puerto:** `22`
   * **Usuario:** Tu usuario de Debian
   * **Contraseña:** Tu contraseña de Debian
3. Haz clic en **Conectar**.
4. En el panel izquierdo (tu PC), abre la carpeta `php_version`.
5. En el panel derecho (Debian), navega hasta:
   ```
   /var/www/html/
   ```
6. Puedes crear una subcarpeta llamada `gymfit` y arrastrar todos los archivos adentro:
   ```
   /var/www/html/gymfit/
   ├── api/
   ├── config/
   │   └── db.php
   ├── css/
   ├── js/
   ├── index.php
   ├── admin.php
   └── socio.php
   ```
7. Verifica que los datos en `config/db.php` coincidan con tu contraseña configurada en MariaDB:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'gymfit_db');
   define('DB_USER', 'gymfit_admin');
   define('DB_PASS', 'TuPasswordSegura123!');
   ```
8. En la terminal de Debian, asigna los permisos para que Apache pueda servir los archivos correctamente:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/gymfit
   sudo chmod -R 755 /var/www/html/gymfit
   ```

---

## 🌐 PASO 5: Probar la Aplicación desde el Navegador

Desde cualquier dispositivo conectado a tu red (PC, laptop, celular o tablet), abre el navegador e ingresa:

```
http://<IP_DE_TU_DEBIAN>/gymfit/
```
*(Ejemplo: `http://192.168.1.50/gymfit/`)*

### 🔑 Credenciales para Iniciar Sesión

#### 1. Pestaña "Administrador":
* **Correo:** `admin@gimfit.com`
* **Contraseña:** `Gimfit2026!`
* **Acceso a:** Dashboard con métricas, Registro de nuevos socios, Cobro de membresías (Venta Rápida), Caja con exportación CSV a Excel, Historial de Auditoría inmutable y Lector QR de asistencias por cámara web o DNI manual.

#### 2. Pestaña "Portal Socio":
* **DNI:** `65432109` (Luis Mendoza) o `72345678` (Juan Pérez) o `45678901` (María Rodríguez).
* **Acceso a:**
  * **Pase Digital Deportivo** con código QR personal generado en vivo.
  * Botón de **1 Clic para Registrar Asistencia**.
  * Contador de asistencias totales y última visita en tiempo real.
  * Barra de progreso de días restantes de su membresía.
  * Ficha física con **cálculo de IMC automático**, objetivos y observaciones.
